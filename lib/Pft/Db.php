<?
/**
 * Pft基础类库
 *
 * @author Terry
 * @package Pft
 * @version 1.0.3
 */

/**
 * 直接对数据库操作的类
 * 使用方法：
 * <code>
 * Pft_Db::getDb()->getAll( $sql );
 * </code>
 *
 * @author Terry
 * @package Pft
 */
class Pft_Db{
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

	public function getConnName(){return $this->_conn_name;}
	
	/**
	 * A Pft_Db instance
	 *
	 * @var Pft_Db
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
		$this->_debug = ( defined("DEBUG") && DEBUG && class_exists('Pft_Debug') );
		$this->_log = class_exists('Pft_Log');
		$this->_mutile_read_db = class_exists('Pft_Config');

		$this->reconnect( $con_name );
	}
	
	/**
	 * 获得 Pft_Db 对象
	 *
	 * @return Pft_Db
	 */
	public static function getDb( $con_name = "propel" ){
		if( !self::$_db )		{
			self::$_db = new Pft_Db( $con_name );
		}else{
			self::$_db->reconnect( $con_name );
		}
		return self::$_db;
	}

	/**
	 * 获得 Pft_Db 对象
	 *
	 * @return Pft_Db
	 */
	public static function getDbx( $con_name = "propel" )
	{
		if( !self::$_dbx ){
			self::$_dbx = new Pft_Dbx( $con_name );
		}else{
			self::$_dbx->reconnect( $con_name );
		}
		return self::$_dbx;
	}
	
	/**
	 * 重新连接一次数据库
	 * 如果 $con_name 相同则不连接
	 *
	 * @param string $con_name
	 * @param string $forceReconnect
	 */
	public function reconnect_old( $con_name , $forceReconnect = false ){
		if( $forceReconnect || $con_name != $this->getConnName() ){
			if( $this->_debug ){
				Pft_Debug::addInfoToDefault( "Pft", "Before connect use Pft_Db." );
			}
			$this->_connection = Propel::getConnection( $con_name );
			$this->_conn = $this->_connection->getResource();
			$this->_dsn = $this->_connection->getDSN();
/*	
			$configuration = include( Pft_Config::getConfigPath()."propel.conf.php" );
			$cfg = $configuration['datasources'][$con_name]['connection'];
			$this->_conn = mysql_connect($cfg["hostspec"].($cfg["port"]?":".$cfg["port"]:""), $cfg["username"], $cfg["password"]);
			mysql_select_db( $cfg["database"], $this->_conn );
*/	
			$this->setConnName( $con_name );

			if( $this->_debug ){
				Pft_Debug::addInfoToDefault( "Pft", "After connect [$con_name] use Pft_Db." );
			}
		}
	}

	private $_connCfg;
	
	public function reconnect( $con_name , $forceReconnect = false ){
		if( $forceReconnect || $con_name != $this->getConnName() ){
			if (is_array ( $con_name )) {
				$cfg = $con_name;
			} else {
				if(!$this->_connCfg){
					$configuration = include( Pft_Config::getConfigPath()."db.conf.php" );
					$this->_connCfg = $configuration;
				}else{
					$configuration = $this->_connCfg;
				}
				$cfg = @$configuration['datasources'][$con_name]['connection'];
			}

			if(is_array($cfg)){
				$this->_conn = mysql_connect ( $cfg ["hostspec"] . ($cfg ["port"] ? ":" . $cfg ["port"] : ""), $cfg ["username"], $cfg ["password"] );
				$this->_dsn = $cfg;
				mysql_select_db ( $cfg ["database"], $this->_conn );
				mysql_query ( "set names 'utf8'" );
				
				$this->setConnName ( $con_name );
				if (defined ( "DEBUG" ) && DEBUG) {
					Pft_Debug::addInfoToDefault ( "Pft", "After connect [$con_name] use Pft_Db." );
				}
			}else{
				Pft_Debug::addInfoToDefault ( "Pft", "Connot connect [$con_name], use default Db." );
			}	
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
	public function getAllAsCol( $sql ){
		$dbAll = $this->getAll( $sql );
		
		if ( !$dbAll )
			return null;
			
		$result  = array();
		
		//Pft_Log::addLog( "dbAll=" . print_r($dbAll, true) );
	
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
		return $return;
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
		//
		if(  $row = mysql_fetch_assoc( $rs ) ){
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
	 * 获得查询结果中第一条第一个字段的值
	 *
	 * @param string $sql
	 * @return 标量
	 */
	public function getOne( $sql ){
		$rs = $this->_query( $sql );
		if(  $row = mysql_fetch_assoc( $rs ) ){
			return current( $row );
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
		$blankArr = array();
		while ( list($key) = each($row) ){
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
	private function _copyDataArrayToArrayByOrder( $sourceArray, $targetArray ){
//		reset( $sourceArray );
//		reset( $targetArray );				

		while ( list( $key ) = each( $targetArray ) ){
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
	private function _query( $sql, $logSql = true ){
		Pft_Debug::addInfoToDefault( '', "Pre execute sql" );

		//self::autoSelectDbBySql( $sql );
		//return mysql_query( $sql, $this->_conn );
		//如果return $rs 会慢 数十毫秒 @todo  再次验证
		
		if( $this->_mutile_read_db ){
		//if ( is_object($this->_connection) && $this->_connection->getAutoCommit() ){
			if( 'Pft_Dbx' == get_class( $this ) ){
				self::superUpdateConnectionX();
			}else{
				self::superUpdateConnection(); // 强制更新数据库连接，使只需只读的操作连接到“只读数据库服务器”				
			}
		//}
		}
		$rev = mysql_query( $sql, $this->_conn );

		Pft_Debug::addInfoToDefault( __FILE__, "Executed sql [<div> $sql </div>]" );
		if( mysql_errno() ){
			//如果使用System级别会重复记录db日志，可能死循环。
			if( $this->_log )
				Pft_Log::addLog('Execute sql ['.$sql.'] fail, error ['.mysql_errno().':'.mysql_error().']', Pft_Log::LEVEL_DEBUG);
			throw ( new Pft_Db_Exception( mysql_errno() . ": " . mysql_error() . "\n" ) );
		}
		if( $logSql ){
			if( $this->_log )
				Pft_Log::logDbOprate($sql);
		}
		return $rev;
	}
	
	private static $_readDbServerNameOfThisSession;
	
	/**
	 * 连接读服务器
	 * @return Pft_Db
	 * @author terry
	 * @version 0.1.0
	 * Fri Aug 10 20:55:06 CST 2007
	 */
	public static function connectReadDb(){
		if( !self::$_readDbServerNameOfThisSession ){
			$int = mt_rand( 1, 3 );
			self::$_readDbServerNameOfThisSession = 'read'.$int;
		}
		
		$db = Pft_Db::getDb( self::$_readDbServerNameOfThisSession );
		Pft_Debug::addInfoToDefault( '', "Connected to Db [".self::$_readDbServerNameOfThisSession."]" );
		return $db;
	}
	
	/**
	 * 连接写读服务器
	 * @return Pft_Db
	 * @author terry
	 * @version 0.1.0
	 * Fri Aug 10 20:55:06 CST 2007
	 */
	public static function connectWriteDb(){
		$db = Pft_Db::getDb( 'propel' );
		Pft_Debug::addInfoToDefault( '', "Connected to Db [".'propel'."]" );
		return $db;
	}
	
	/**
	 * 连接读写服务器
	 * @return Pft_Db
	 * @author terry
	 * @version 0.1.0
	 * Fri Aug 10 20:55:06 CST 2007
	 */
	public static function connectReadwriteDb(){
		$db = Pft_Db::getDb( 'readwrite' );
		Pft_Debug::addInfoToDefault( '', "Connected to Db [".'readwrite'."]" );
		return $db;
	}
	
	/**
	 * 根据Sql选择 db
	 * @return Pft_Db
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
	public static function superUpdateConnectionX(){
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
	public function useReadWriteDb(){	
		//Pft_Log::addLog("[{$this->_getDbConnectionHost()}-{$this->_conn}]Prepare change connection to READ_WRITE DB server", Pft_Log::LEVEL_DEBUG, 'DB_CONTROL', '', '', __FILE__.__LINE__);
		$this->reconnect('propel');
		//Pft_Log::addLog("[{$this->_getDbConnectionHost()}-{$this->_conn}]Connection was changed", Pft_Log::LEVEL_DEBUG, 'DB_CONTROL', '', '', __FILE__.__LINE__);
		
		Pft_Debug::addInfoToDefault('', "Connected to READ_WRITE DB server[{$this->_getDbConnectionHost()}:{$this->_conn}]");
	}
	
	/**
	 * 使用只读数据库连接
	 *
	 * @author bobit
	 * Mon Sep 24 16:37:49 CST 200716:37:49
	 */
	public function useReadonlyDb(){
		$old_db_host = $this->_getDbConnectionHost();
		
		if( !self::$_readDbServerNameOfThisSession ){
			$int = mt_rand( 1, 3 );
			self::$_readDbServerNameOfThisSession = 'read'.$int;
		}
	
		//Pft_Log::addLog("[{$this->_getDbConnectionHost()}-{$this->_conn}]Prepare change connection to READONLY DB server", Pft_Log::LEVEL_DEBUG, 'DB_CONTROL', '', '',  __FILE__.__LINE__);
		$this->reconnect( self::$_readDbServerNameOfThisSession );
		//Pft_Log::addLog("[{$this->_getDbConnectionHost()}-{$this->_conn}]Connection was changed", Pft_Log::LEVEL_DEBUG, 'DB_CONTROL', '', '', __FILE__.__LINE__);
		
		Pft_Debug::addInfoToDefault( '', "Connected to READONLY DB server[{$this->_getDbConnectionHost()}:{$this->_conn}]" );
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
	public static function needUseReadonlyDb(){
		return self::$_need_use_readonly_db;
	}
	
	/**
	 * 开始使用只读数据库
	 *
	 * @author bobit
	 * Mon Sep 24 16:37:49 CST 200716:37:49
	 */
	public static function startUseReadonlyDb(){
		self::$_need_use_readonly_db = 1;
		Pft_Debug::addInfoToDefault('', 'Set use READONLY DB server');
	}
	
	/**
	 * 结束使用只读数据库
	 *
	 * @author bobit
	 * Mon Sep 24 16:37:49 CST 200716:37:49
	 */
	public static function endUseReadonlyDb(){
		self::$_need_use_readonly_db = 0;
		Pft_Debug::addInfoToDefault('', 'Unset use READONLY DB server');
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
	 * 获得查询结果集，用于fetch
	 *
	 * @param string $sql
	 * @return Pft_Db_Result
	 */
	public function getResult( $sql ){
		$aNewResult = new Pft_Db_Result();
		$aNewResult->setResult( $this->_query( $sql ) );
		return $aNewResult;
	}

	/**
	 * 
	 * @param $tbname
	 * @return string
	 * @author yan
	 * @date 2010-10-3下午06:25:08
	 */
	public static function getTbName($tbname){
		return Pft_Config::getCfg('DB_TB_PREFIX').$tbname;
	}
}

/**
 * Result Objet for fetch mode.
 * When you need use mass data, please use this object throw Pft_Db()->getDb()->getResult().
 */
class Pft_Db_Result{
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
}

/**
 * Pft 数据库相关异常类
 * 数据库查询相关的操作发生异常时，触发此类
 */
class Pft_Db_Exception extends Exception{
	
}
?>