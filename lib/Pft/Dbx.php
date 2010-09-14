<?
/**
 * Pft基础类库
 *
 * @author Terry
 * @package Pft
 * @version 1.0.2
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
class Pft_Dbx extends Pft_Db
{

	/**
	 * 覆盖基类方法
	 * 
	 * 重新连接一次数据库
	 * 如果 $con_name 相同则不连接
	 *
	 * @param string $con_name
	 * @param string $forceReconnect
	 */
	public function reconnect( $con_name , $forceReconnect = false ){
		if( $forceReconnect || $con_name != $this->getConnName() ){
//			$this->_conn = Propel::getConnection( $con_name )->getResource();
	
			$configuration = include( Pft_Config::getConfigPath()."propel.conf.php" );
			$cfg = $configuration['datasources'][$con_name]['connection'];
			$this->_conn = mysql_connect($cfg["hostspec"].($cfg["port"]?":".$cfg["port"]:""), $cfg["username"], $cfg["password"]);
			$this->_dsn = $cfg;
			mysql_select_db( $cfg["database"], $this->_conn );
			mysql_query( "set names 'utf8'" );
			//$this->execute( "set names 'utf8'" );
			
			//Not effect
			//$this->execute( "SET @auto_increment_increment=5;" );
			//$this->execute( "SET @auto_increment_offset=1;" );
			//
			
//			$stmt = mysql_query( "show tables" );
//			while ( $row = mysql_fetch_assoc( $stmt ) ){
//				var_dump( $row );
//			}
//			mysql_free_result( $stmt );
//			print"<pre>Terry :";var_dump( $this->_conn );print"</pre>";
//			exit();
			$this->setConnName( $con_name );			
		}
	}
	
	public static function getAdodb( $con_name = "propel" )
	{
		/**
		* 数据库连结类：ADODB
		*/
		$configuration = include( Pft_Config::getConfigPath()."propel.conf.php" );
		if( !isset($configuration['datasources'][$con_name]) )
		{
			$con_name = $configuration['datasources']["default"];
		}
		$cfg = $configuration['datasources'][$con_name]['connection'];		
		
		include_once(Pft_Config::getLibPath(1). "adodb/adodb.inc.php");
		$db_a	= ADONewConnection("mysql");
		$ADODB_CACHE_DIR	= Pft_Config::getRootPath()."cache/adodb";
		
		$db_a->Connect($cfg["hostspec"].($cfg["port"]?":".$cfg["port"]:""), $cfg["username"], $cfg["password"], $cfg["database"]);
		$db_a->Execute("set names 'utf8'");				//数据库字符编码设置
		$db_a->SetFetchMode(ADODB_FETCH_ASSOC);
		
		// 设置缓存有效时间为5分钟
		$db_a->cacheSecs = 300;				
		return $db_a;
	}
	
//	public static function getDbx( $con_name = "propel" )
//	{
//		if( !self::$_db )		{
//			self::$_db = new Pft_Dbx( $con_name );
//		}else{
//			self::$_db->reconnect( $con_name );
//		}
//		return self::$_db;
//	}	
}
?>
