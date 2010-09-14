<?
/**
 * Watt基础类库
 *
 * @author Terry
 * @package Watt
 * @version 1.0.3
 */

/**
 * 直接对数据库操作的类
 * 使用方法：
 * <code>
 * Watt_Db::getDb()->getAll( $sql );
 * </code>
 *
 * @author Terry
 * @package Watt
 */
class Watt_Db
{
	/**
	 * A database connection
	 *
	 * @var resource
	 */
	protected $_conn = null;
	
	/**
	 * @var Connection
	 */
	protected $_connection = null;
	
	protected $_conn_name;

	protected $_dsn;//array

	/**
	 * 最近一次的查询结果集
	 * @var mysql rs
	 */
	protected $_latestRs;
	
	public function setConnName( $v ){$this->_conn_name = $v;}

	/**
	 * @return string|array
	 */
	public function getConnName(){return $this->_conn_name;}
	
	/**
	 * A Watt_Db instance
	 *
	 * @var Watt_Db
	 */
	private static $_db;

	private static $_dbx;

	//是否允许调试 by terry at Thu Feb 19 11:30:39 CST 2009
	private $_debug = false;
	
	//是否允许log by terry at Thu Feb 19 11:30:39 CST 2009
	private $_log = false;

	//是否允许多个读数据库 by terry at Thu Feb 19 11:51:27 CST 2009
	private $_mutile_read_db = false;

	/**
	 * 禁止外部实例化
	 *
	 * @param string $con_name
	 */
	protected function __construct( $con_name ){
		$this->_debug = ( defined("DEBUG") && DEBUG && class_exists('Watt_Debug') );
		$this->_log = class_exists('Watt_Log');
		$this->_mutile_read_db = class_exists('Watt_Config');

		$this->reconnect( $con_name );
	}

	/**
	 * 获得 Watt_Db 对象
	 *
	 * @example 
	 * //in tpm
	 * $db = Watt_Db::getDb("propel");
	 * 
	 * //stand alone
	 * $db_cfg['hostspec'] = '10.0.0.15';
	 * $db_cfg['port'] = '3306';
	 * $db_cfg['username'] = 'tpm_watt';
	 * $db_cfg['password'] = 'tpm20080808';
	 * $db_cfg['database'] = 'DEV_tpm_watt';
	 * $db_cfg['charset'] = 'utf8';
	 * $db = Watt_Db::getDb($db_cfg);
	 * 
	 * @return Watt_Db
	 */
	public static function getDb( $con_name = "propel" ){
		if( !self::$_db ){
			self::$_db = new Watt_Db( $con_name );
		}else{
			self::$_db->reconnect( $con_name );
		}
		return self::$_db;
	}

	/**
	 * 获得 Watt_Db 对象
	 *
	 * @return Watt_Db
	 */
	public static function getDbx( $con_name = "propel" )
	{
		if( !self::$_dbx )		{
			self::$_dbx = new Watt_Dbx( $con_name );
		}else{
			self::$_dbx->reconnect( $con_name );
		}
		return self::$_dbx;
	}
	
	/**
	 * 重新连接一次数据库
	 * 如果 $con_name 相同则不连接
	 *
	 * @param string|array $con_name
	 * @param string $forceReconnect
	 */
	public function reconnect( $con_name , $forceReconnect = false ){
		if( is_array( $con_name ) ){
			$cfg = $con_name;
		}else{
			$cfg = null;
		}
		
		if( $forceReconnect || $con_name != $this->getConnName() ){
			if( $this->_debug )
				Watt_Debug::addInfoToDefault( "Watt", "Before connect use Watt_Db." );

			if( class_exists('Propel') ){
				$this->_connection = Propel::getConnection( $con_name );
				$this->_conn = $this->_connection->getResource();
				$this->_dsn = $this->_connection->getDSN();				
			}else{
				if( !is_array( $cfg ) ){
					$configuration = include( Watt_Config::getConfigPath()."propel.conf.php" );
					$cfg = $configuration['datasources'][$con_name]['connection'];
				}
				$this->_conn = mysql_connect($cfg["hostspec"].($cfg["port"]?":".$cfg["port"]:""), $cfg["username"], $cfg["password"]);
				$this->_dsn = $cfg;
				mysql_select_db( $cfg["database"], $this->_conn );
				$charset = @$cfg["charset"]?$cfg["charset"]:'utf8';
				mysql_query( "set names '$charset'" );			
			}
			
			$this->setConnName( $con_name );

			if( $this->_debug )
				Watt_Debug::addInfoToDefault( "Watt", "After connect [$con_name] use Watt_Db." );
		}
	}
	
	/**
	 * 获取 Connection
	 *
	 * @return Connection
	 */
	public function getConnection(){
		return $this->_connection;
	}
	
	public function begin(){
		if( $this->_connection )$this->_connection->begin();
	}
	
	public function commit(){
		if( $this->_connection )$this->_connection->commit();
	}
	
	public function rollback(){
		if( $this->_connection )$this->_connection->rollback();
	}
	
	/**
	 * 获取结果集第一字段的一维数组
	 *
	 * @param string $sql
	 * @return array | null
	 */
	public function getAllAsCol( $sql )
	{
		$dbAll = $this->getAll( $sql );
		
		if ( !$dbAll )
			return null;
			
		$result = array();
		
		foreach( $dbAll as $row ){
			$row = array_values( $row );
			$result[] = $row[0];
		}
		
		return $result;
	}
	
	/**
	 * 获得全部结果集的二维数组
	 *
	 * @param string $sql
	 * @return array|null
	 */
	public function getAll( $sql ){
		/*
		$rs = $this->_conn->executeQuery( $sql );
		var_dump( $rs->getRecordCount() );
		$result = $rs->getResult();
		*/
		$return = null;
		$result = $this->_query( $sql );

		//mysql_fetch_array
		while( $row = mysql_fetch_assoc( $result ) ){
			//这是转换为 phpname 的代码
			//if( !isset($blankArr) )$blankArr = $this->_getBlankArrFieldNameToPhpName( $row );
			//$return[] = $this->_copyDataArrayToArrayByOrder( $row, $blankArr );;
			
			//这是不转换为 phpname 的代码
			$return[] = $row;
		}
		//var_dump ( $return );
		return $return;

		/*
		这是 propel 用 Rs 获得数据的方法
		while( $row = $rs->getRow() ) {
			$return[] = $row;
			$rs->next();
   		}
   		return $return;
   		var_dump( $return );
   		*/
	}
	
	/**
	 * 获得第一条结果的一维数组
	 *
	 * @param string $sql
	 * @return array|null
	 */
	public function getRow( $sql ){
		//$rs = $this->_conn->executeQuery( $sql );
		$rs = $this->_query( $sql );
		if( $row = mysql_fetch_assoc( $rs ) ){
			//这是转换为 phpname 的代码
			//$blankArr = $this->_getBlankArrFieldNameToPhpName( $row );
			//$rowx = $this->_copyDataArrayToArrayByOrder( $row, $blankArr );
			//return $rowx;
			
			//这是不转换为 phpname 的代码
			return $row;
		}else{
			return null;
		}
	}
	
	/**
	 * @desc 对象类具备的增加的基本功能,作为到数据库的转换接口
	 *
	 * @param String $TableName 表格名称
	 * @param Array $res N1/N2
	 * @return BOOLEAN
	 */
	function copyRow($TableName,$res)
	{
//		echo "app_add";
//		var_dump($res);
//		if (isset($_SESSION['user']['company_data']['company_name']))
//		{
//			$system_gongsi = $_SESSION['user']['company_data']['company_name'];
//		} else
//		{
//			$system_gongsi = SYSTEM_GONGSI;
//		}
		if($TableName == null)
		{
			return false;
		}
		else
		{
			$sql = 'INSERT INTO '.$TableName;
		}
		if(is_array($res))
		{
			$sqlKeys = null;
			$sqlVals = null;
			foreach ($res as $key => $val)
			{
				if(is_array($val))
				{
					foreach ($val as $key2 => $val2)
					{
						$sqlKeys .= $key.',';
						$sqlVals .= $val.',';
					}
					$sqlKeys = BaseOption::subStringByDescCount($sqlKeys);
					$sqlVals = BaseOption::subStringByDescCount($sqlVals);
					$sqls = $sql." (".$sqlKeys.") VALUES (".$sqlVals.")";
					if($this->_query($sqls))
					{
						unset($sqlKeys);
						unset($sqlVals);
						unset($sqls);
					}
				}
				else
				{
					$sqlKeys .= $key.',';
					$sqlVals .= "'".$val."',";
				}
			}

			if($sqlKeys != null && $sqlVals != null)
			{
				$sqlKeys   = BaseOption::subStringByDescCount($sqlKeys);
				$sqlVals   = BaseOption::subStringByDescCount($sqlVals);
				/**
				 * 添加默认值 
				 */
				/*if (!array_key_exists('system_gongsi', $res))
				{
					$sqlKeys .= ',system_gongsi';
					$sqlVals .= ",'".$system_gongsi . "'";
				}*/
				$sqls      = $sql." (".$sqlKeys.") VALUES (".$sqlVals.")";
				
				if ($this->execute($sqls)) {
					return true;
				} else {
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}

	}
	
	/**
	 * 获得查询结果中第一条第一个字段的值
	 *
	 * @param string $sql
	 * @return 标量
	 */
	public function getOne( $sql ){
		$rs = $this->_query( $sql );
		if( $row = mysql_fetch_assoc( $rs ) ){
			return current( $row );
		}else{
			return null;
		}
	}
	/**
	 * 说明:获得查询结果中第一条第一个字段的值 以对象的形式返回
	 * @author john
	 * @version 0.0.0
	 * @time Mon May 12 11:20:19 CST 2008
	 **/
	public function getObOne($sql){
		$rs=$this->_query($sql);
		if( $row=mysql_fetch_object($rs) ){
			return $row;
		}else{
			return null;
		}
	}

	/**
	 * 执行一条Sql语句
	 *
	 * @param string $sql
	 * @return int sql语句影响的列数
	 */
	public function execute( $sql, $logSql = true  ){
		$this->_query( $sql, $logSql );
		return mysql_affected_rows();
	}

	/**
	 * 查询并返回结果集连接
	 * 
	 * @param String $sql
	 * @return resource
	 */
	public function query( $sql ){
		$this->_latestRs = $this->_query( $sql );
		return $this->_latestRs;
	}
	
	/**
	 * 最近一次插入数据返回的ID
	 * @return int
	 */
	public function Insert_ID(){
		return mysql_insert_id();
	}
	
	/**
	 * 返回一个空的,已经格式化好phpname的 array 
	 *
	 * @param array $row
	 */
	private function _getBlankArrFieldNameToPhpName( $row ){
		if( !is_array( $row ) ) return null;
		//foreach ()
		$blankArr = array();
		while ( list($key) = each($row) ) {
			$blankArr[implode( array_map( "ucfirst", split( "_", $key ) ) )] = null;
		}
		reset( $row );
		return $blankArr;
	}
	
	/**
	 * 顺序的将 source array 的数据复制到 target array 里
	 *
	 * @param array $sourceArray
	 * @param array $targetArray
	 */
	private function _copyDataArrayToArrayByOrder( $sourceArray, $targetArray )
	{
//		reset( $sourceArray );
//		reset( $targetArray );				

		while ( list( $key ) = each( $targetArray ) )
		{
			$targetArray[$key] = current( $sourceArray );
			next( $sourceArray );
		}
		return $targetArray;
	}
	
	/**
	 * 查询并返回结果集连接
	 * @param String $sql
	 * @return resource
	 */
	private function _query( $sql, $logSql = true )
	{
		if( $this->_debug )
			Watt_Debug::addInfoToDefault( '', "Pre execute sql" );

		//self::autoSelectDbBySql( $sql );
		//return mysql_query( $sql, $this->_conn );
		//如果return $rs 会慢 数十毫秒 @todo  再次验证
		
		if( $this->_mutile_read_db ){
		//if ( is_object($this->_connection) && $this->_connection->getAutoCommit() ){
			if( 'Watt_Dbx' == get_class( $this ) ){
				self::superUpdateConnectionX();
			}else{
				self::superUpdateConnection(); // 强制更新数据库连接，使只需只读的操作连接到“只读数据库服务器”				
			}
		//}			
		}

		$rev = mysql_query( $sql, $this->_conn );

		self::addQueryTimes();
		$queryTimes = self::getQueryTimes();

		if( $this->_debug )
			Watt_Debug::addInfoToDefault( __FILE__, "[$queryTimes] Executed sql [<p> $sql </p>]" );

		if( mysql_errno() ){
			//如果使用System级别会重复记录db日志，可能死循环。
			if( $this->_log )
				Watt_Log::addLog('Execute sql ['.$sql.'] fail, error ['.mysql_errno().':'.mysql_error().']', Watt_Log::LEVEL_DEBUG);

			throw ( new Watt_Db_Exception( mysql_errno() . ": " . mysql_error() . "\n" ) );
		}
		if( $logSql ){
			if( $this->_log )
				Watt_Log::logDbOprate($sql);
		}
		return $rev;
	}
	
	private static $_readDbServerNameOfThisSession;
	
	/**
	 * 连接读服务器
	 * @return Watt_Db
	 * @author terry
	 * @version 0.1.0
	 * Fri Aug 10 20:55:06 CST 2007
	 */
	public static function connectReadDb(){
		if( !self::$_readDbServerNameOfThisSession ){
			$int = mt_rand( 1, 3 );
			self::$_readDbServerNameOfThisSession = 'read'.$int;
		}
		
		$db = Watt_Db::getDb( self::$_readDbServerNameOfThisSession );
		Watt_Debug::addInfoToDefault( '', "Connected to Db [".self::$_readDbServerNameOfThisSession."]" );
		return $db;
	}
	
	/**
	 * 连接写读服务器
	 * @return Watt_Db
	 * @author terry
	 * @version 0.1.0
	 * Fri Aug 10 20:55:06 CST 2007
	 */
	public static function connectWriteDb(){
		$db = Watt_Db::getDb( 'propel' );
		Watt_Debug::addInfoToDefault( '', "Connected to Db [".'propel'."]" );
		return $db;
	}
	
	/**
	 * 连接读写服务器
	 * @return Watt_Db
	 * @author terry
	 * @version 0.1.0
	 * Fri Aug 10 20:55:06 CST 2007
	 */
	public static function connectReadwriteDb(){
		$db = Watt_Db::getDb( 'readwrite' );
		Watt_Debug::addInfoToDefault( '', "Connected to Db [".'readwrite'."]" );
		return $db;
	}
	
	/**
	 * 根据Sql选择 db
	 * @return Watt_Db
	 * @author terry
	 * @version 0.1.0
	 * Fri Aug 10 21:10:39 CST 2007
	 */
	public static function autoSelectDbBySql( $sql ){
		if( stripos( trim($sql), 'SELECT' ) === 0 ){
			return self::connectReadDb();
		}else{
			return self::connectWriteDb();
		}
	}
	
	/**
	 * 利用PHP多次连MySql后使用最后一次连接源的特性更改系统使用只读还是读写数据库服务器
	 * 
	 * @author bobit
	 */
	public static function superUpdateConnection(){
		$tempDb = self::getDb();	// 这个 $tempDb 其实不做任何数据库查询操作，只是为了更改一次数据库连接

		if ( self::needUseReadonlyDb() ){
			$tempDb->useReadonlyDb();
		}else{
			$tempDb->useReadWriteDb();
		}
	}

	/**
	 * 利用PHP多次连MySql后使用最后一次连接源的特性更改系统使用只读还是读写数据库服务器
	 * 对应的x版本
	 * @author y31x
	 */
	public static function superUpdateConnectionX()
	{
		$tempDb = self::getDbx();	// 这个 $tempDb 其实不做任何数据库查询操作，只是为了更改一次数据库连接

		if ( self::needUseReadonlyDb() ){
			$tempDb->useReadonlyDb();
		}else{
			$tempDb->useReadWriteDb();
		}
	}
	
	/**
	 * 使用读写数据库连接
	 *
	 * @author bobit
	 * Mon Sep 24 16:37:49 CST 200716:37:49
	 */
	public function useReadWriteDb()
	{	
		//Watt_Log::addLog("[{$this->_getDbConnectionHost()}-{$this->_conn}]Prepare change connection to READ_WRITE DB server", Watt_Log::LEVEL_DEBUG, 'DB_CONTROL', '', '', __FILE__.__LINE__);
		$this->reconnect('propel');
		//Watt_Log::addLog("[{$this->_getDbConnectionHost()}-{$this->_conn}]Connection was changed", Watt_Log::LEVEL_DEBUG, 'DB_CONTROL', '', '', __FILE__.__LINE__);
		
		if( $this->_debug )
			Watt_Debug::addInfoToDefault('', "Connected to READ_WRITE DB server[{$this->_getDbConnectionHost()}:{$this->_conn}]");
	}
	
	/**
	 * 使用只读数据库连接
	 *
	 * @author bobit
	 * Mon Sep 24 16:37:49 CST 200716:37:49
	 */
	public function useReadonlyDb()
	{
		//$old_db_host 未被使用 by terry at Thu Feb 19 11:37:44 CST 2009
		//$old_db_host = $this->_getDbConnectionHost();
		
		if( !self::$_readDbServerNameOfThisSession )
		{
			$int = mt_rand( 1, 3 );
			self::$_readDbServerNameOfThisSession = 'read'.$int;
		}
	
		//Watt_Log::addLog("[{$this->_getDbConnectionHost()}-{$this->_conn}]Prepare change connection to READONLY DB server", Watt_Log::LEVEL_DEBUG, 'DB_CONTROL', '', '',  __FILE__.__LINE__);
		$this->reconnect( self::$_readDbServerNameOfThisSession );
		//Watt_Log::addLog("[{$this->_getDbConnectionHost()}-{$this->_conn}]Connection was changed", Watt_Log::LEVEL_DEBUG, 'DB_CONTROL', '', '', __FILE__.__LINE__);
		
		if( $this->_debug )
			Watt_Debug::addInfoToDefault( '', "Connected to READONLY DB server[{$this->_getDbConnectionHost()}:{$this->_conn}]" );
	}
	
	protected static $_need_use_readonly_db = 0;	// 是否需要使用只读数据库。默认为不使用
	
	/**
	 * 检测是否需要使用只读数据库
	 *
	 * @return boolean
	 * 
	 * @author bobit
	 * Mon Sep 24 16:37:49 CST 200716:37:49
	 */
	public static function needUseReadonlyDb()
	{
		return self::$_need_use_readonly_db;
	}
	
	/**
	 * 开始使用只读数据库
	 *
	 * @author bobit
	 * Mon Sep 24 16:37:49 CST 200716:37:49
	 */
	public static function startUseReadonlyDb()
	{
		self::$_need_use_readonly_db = 1;
		Watt_Debug::addInfoToDefault('', 'Set use READONLY DB server');
	}
	
	/**
	 * 结束使用只读数据库
	 *
	 * @author bobit
	 * Mon Sep 24 16:37:49 CST 200716:37:49
	 */
	public static function endUseReadonlyDb()
	{
		self::$_need_use_readonly_db = 0;
		Watt_Debug::addInfoToDefault('', 'Unset use READONLY DB server');
	}
	
	/**
	 * 获得当前连接的主机名
	 *
	 * @return string
	 * 
	 * @author bobit
	 * Tue Sep 25 15:15:15 CST 200715:15:15
	 */
	private function _getDbConnectionHost()
	{
		$dsn = $this->_dsn;
			
		if ( is_array($dsn) && isset( $dsn['hostspec'] ) )
			return $dsn['hostspec'];
		else
			return '';
	}
	
	/**
	 * 获得查询结果中第一条第一个字段的值
	 *
	 * @param string $sql
	 * @return Watt_Db_Result
	 */
	public function getResult( $sql )
	{
		$aNewResult = new Watt_Db_Result();
		$aNewResult->setResult( $this->_query( $sql ) );
		return $aNewResult;
	}

	private static $_queryTimes=0;
	public static function addQueryTimes($times=1){
		self::$_queryTimes += $times;
	}
	/**
	 * @return int
	 */
	public static function getQueryTimes(){
		return self::$_queryTimes;
	}
}

/**
 * Result Objet for fetch mode.
 * When you need use mass data, please use this object throw Watt_Db()->getDb()->getResult().
 */
class Watt_Db_Result{
	private $_result;
	
	public function setResult( $v ){$this->_result = $v;}
	public function getResult(){return $this->_result;}
	
	/**
	 * @return array
	 */
	public function fetchRow(){
		//$result = $this->_query( $sql );

		if( $this->_result ){
			return mysql_fetch_assoc( $this->_result );
		}else{
			return null;
		}
	}
	
	/**
	 * @return boolean
	 */
	public function free(){
		if( $this->_result ){
			return mysql_free_result( $this->_result );
		}else{
			return true;
		}
	}
	
	/**
	 * @author terry
	 * Wed Jan 07 09:28:35 CST 2009
	 */
	public function __destruct(){
		$this->free();
	}
}

/**
 * Watt 数据库相关异常类
 * 数据库查询相关的操作发生异常时，触发此类
 */
class Watt_Db_Exception extends Exception{
	
}
?>