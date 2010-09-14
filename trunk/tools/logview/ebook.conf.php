<?
//define( "PATH_SITE", realpath( dirname(__FILE__)."/../")."/" );
//define( "PATH_UPLOAD", PATH_SITE."ebook/books/" );
//define( "PATH_SITE_UPLOAD", "ebook/books/" );

if( $_SERVER['SERVER_ADDR'] == '127.0.0.1' ){
	define( "PATH_UPLOAD", "G:/WorksInChnmed/" );	
}else{
	//define( "PATH_UPLOAD", "/home/ythroneo/books/books/" );
	define( "PATH_UPLOAD", realpath( dirname(__FILE__)."/../../../log/" )."/" );
}

define( 'DOWNLOAD_COUNT_PER_MIN', 6 );	//每分钟文件下载名额

class Pft_Ebook_Download_Counter{
	private $_downloadCountPerMin = 2;
	private $_lockPath;
	private $_lockFile;
	
	/**
	 * @return Pft_Ebook_Download_Counter
	 */
	public static function factory(){
		return new Pft_Ebook_Download_Counter();
	}
	
	public function __construct(){
		$this->_downloadCountPerMin = DOWNLOAD_COUNT_PER_MIN;
		$this->_lockPath = dirname( __FILE__ ).'/log/';
		$this->_lockFile = $this->_lockPath.date( 'Ymd-Hi' ).'.lock';
	}
	
//	private function _isEnableDownload(){
//		
//	}
	
	/**
	 *
	 * @return boolean
	 */
	public function startDownload(){
		$currentFiles = @file_get_contents( $this->_lockFile );
		if( !$currentFiles ){
			$currentFiles = 1;
		}else{
			$currentFiles++;
		}
		if( @file_put_contents( $this->_lockFile, $currentFiles ) ){
			chmod( $this->_lockFile, 0777 );
			if( $currentFiles <= $this->_downloadCountPerMin ){
				$rev = true;	
			}else{
				Pft_Log::addLog( 'Max download request ['.$currentFiles.']!', Pft_Log::LEVEL_INFO );
				$rev = false;
			}
		}else{
			Pft_Log::addLog( 'Write lock file ['.$this->_lockFile.'] failed!', Pft_Log::LEVEL_ERROR );
			$rev = false;
		}
	
		return $rev;
	}
	
}

/**
 * 请不要在此版本的Log类上升级
 * @author y31
 * Tue Jul 10 22:11:07 CST 2007
 */

class Pft_Log{
	
	const LEVEL_DEBUG  = 1; //调试信息
	const LEVEL_SYSTEM = 2; //系统信息
	const LEVEL_ERROR  = 4;	//系统错误
	const LEVEL_INFO   = 8;	//业务级记录的信息
	
	/**
	 * 默认的loger
	 *
	 * @var Pft_Log
	 */
	private static $_defaultLoger;	//debuger loger 记录 调试日志和系统日志
	
	private static $_systemLoger;	//系统Loger 记录系统运行日志 2 4 8
	/**
	 * 记录：
	 * 使用默认的 loger 记录日志
	 * 
	 * 时间 - 服务器自动记录时间
	 *
	 * 起因 - msg
	 * 经过
	 * 结果
	 * 
	 * 地点 - sourceName
	 * 人物 - userId
	 *      - actorName (userName)
	 *
	 * @param string $msg 记录的信息
	 * @param int $level
	 * @param string $sourceName
	 * @param string $actorName
	 * @param string $actorId
	 * @param mix $exts
	 * @return boolean
	 */
	public static function addLog( $msg, $level=0, $sourceName=""
	                             , $actorName="", $actorId=""
	                             , $exts=null )
	{
		//默认所有日志就记录入 debuger 日志
		self::getDefaultLogger()->log( $msg, $level, $sourceName
	                                 , $actorName, $actorId
	                                 , $exts );

//	    if( $level > self::LEVEL_DEBUG ){
//	    	//self::getDefaultLogger()->log( 'Before DB Log' );
//
//		    self::getSystemLogger()->log( $msg, $level, $sourceName
//		                                 , $actorName, $actorId
//		                                 , $exts );	
//
//		    //self::getDefaultLogger()->log( 'After DB Log' );
//	    }

	    return true;
	}
	
	/**
	 * 返回默认的 日志记录器
	 *
	 * @return Pft_Log
	 */
	
	public static final function getDefaultLogger()
	{
		if( !is_object( self::$_defaultLoger ) )
		{
			self::$_defaultLoger = new Pft_Log_File();
		}
		return self::$_defaultLoger;
	}
	
	/**
	 * 获得系统日志程序
	 *
	 * @return Pft_Log
	 */
	public static final function getSystemLogger()
	{
		if( !is_object( self::$_systemLoger ) )
		{
			self::$_systemLoger = new Pft_Log_Db();
		}
		return self::$_systemLoger;
	}
	
	/**
	 * 记录日志， 子类必须覆盖
	 *
	 * @param string $msg 记录的信息
	 * @param int $level
	 * @param string $sourceName
	 * @param string $actorName
	 * @param string $actorId
	 * @param mix $exts
	 * @return boolean
	 */
	public function log( $msg, $level, $sourceName
	                                 , $actorName, $actorId
	                                 , $exts ){
		return true;
	}
	
	/**
	 * 获得日志 Level 列表
	 *
	 * @return array
	 */
	public static function getLevelList(){
		$arrLevel = array(
			self::LEVEL_DEBUG  => 'DEBUG',
			self::LEVEL_SYSTEM => 'SYSTEM',
			self::LEVEL_ERROR  => 'ERROR',
			self::LEVEL_INFO   => 'INFO'
		);
		return $arrLevel;
	}
	
	
	const DB_OPRATE_TYPE_PREFIX = 'DB';
	const DB_OPRATE_TYPE_INSERT = 'C';
	const DB_OPRATE_TYPE_UPDATE = 'U';
	const DB_OPRATE_TYPE_DELETE = 'D';
	const DB_OPRATE_MSG_SPLITER = ' |*| ';
	
	/**
	 * 记录操作数据库日志
	 *
	 * @param string $sql
	 */
	public static function logDbOprate( $sql ){
		$sql = trim( $sql );
		if( !$sql )return false;
		$tableName = '';
		$logName   = '';
		$msg       = '';
		
		//忽略列表优先于允许列表
		$ignoreTalbenameList = array(
			'tpm_rizhi' => 'rz_id',
			);
		
		$toLogTablenameList = array(
			'tpm_dingdan' => 'dd_id',
			'tpm_shengchandingdan' => 'sd_id',
			'tpm_xiangmu' => 'xm_id',
			'tpm_renwu'   => 'rw_id',
			);
			
		//因为之前已经trim了，所以 === 0
		if( stripos( $sql, 'SELECT' ) === 0 ){
			//不记录Select
			return false;
			$sql = str_replace( array("\n","\r") , array(" ", "" ) , $sql );
		}elseif( stripos( $sql, 'UPDATE' ) === 0 ){
			/*
			/update[\s]+(\w+)[\s]+set(.*)where[\s]+(.*)/
                          ^^table name ^^col         ^^cond
             */
			if( preg_match( "/update[\\s]+(\\w+)[\\s]+set(.*)where[\\s]+(.*)/i", $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cols      = $matchs[2];
				$cond      = $matchs[3];
				$logName   = self::DB_OPRATE_TYPE_PREFIX.'-'.$tableName.'-'.self::DB_OPRATE_TYPE_UPDATE;
				$msg       = $cols.self::DB_OPRATE_MSG_SPLITER.$cond;
			}
		}elseif( stripos( $sql, 'INSERT' ) === 0 ){
			/*
			/insert into[\s]+(\w+)[\s]+\((.*)\)[\s]+values[\s]+\((.*)\)/
			*/
			if( preg_match( '/insert into[\s]+(\w+)[\s]+\((.*)\)[\s]+values[\s]+\((.*)\)/i', $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cols      = $matchs[2];
				$values    = $matchs[3];
				$logName   = self::DB_OPRATE_TYPE_PREFIX.'-'.$tableName.'-'.self::DB_OPRATE_TYPE_INSERT;
				$msg       = $cols.self::DB_OPRATE_MSG_SPLITER.$values;
			}
		}elseif( stripos( $sql, 'DELETE' ) === 0 ){
			/*
			/insert into[\s]+(\w+)[\s]+\((.*)\)[\s]+values[\s]+\((.*)\)/
			*/
			if( preg_match( '/DELETE FROM[\s]+(\w+)[\s]+WHERE(.*)/i', $sql, $matchs ) ){
				$tableName = $matchs[1];
				$cond      = $matchs[2];
				$logName   = self::DB_OPRATE_TYPE_PREFIX.'-'.$tableName.'-'.self::DB_OPRATE_TYPE_DELETE;
				$msg       = $cond;
			}
		}
		//strtolower( $tableName ) != 'tpm_rizhi'
		if( $msg && $logName 
		 && !key_exists( strtolower( $tableName ), $ignoreTalbenameList )
		 &&  key_exists( strtolower( $tableName ), $toLogTablenameList ) ){
//			print"<pre>Terry :";var_dump( $logName );print"</pre>";
//			print"<pre>Terry :";print ( $msg );print"</pre>";
//			exit();
			self::addLog( "Execute [ $sql ]".self::DB_OPRATE_MSG_SPLITER.$msg, self::LEVEL_SYSTEM, $logName );	
			return true;
		}else{
			self::addLog( "Execute [ $sql ]", Pft_Log::LEVEL_DEBUG, $logName );
			return false;
		}
	}
	
}

class Pft_Log_File extends Pft_Log{
	/**
	 * 日志文件的句柄
	 *
	 * @var handle
	 */
	private $_hLogFile;
	private $_logFileName;
	private $_logPath;	
	
	private $_startTime;
	private $_lastLogTime;
	
	/**
	 * 记录日志
	 *
	 * @param string $msg 记录的信息
	 * @param int $level
	 * @param string $sourceName
	 * @param string $actorName
	 * @param string $actorId
	 * @param mix $exts
	 * @return boolean
	 */
	public function log( $msg, $level=0, $sourceName=""
                       , $actorName="", $actorId=""
                       , $exts=null )
	{
		$now = $this->_microtime_float();
		$used = $now - $this->_lastLogTime;
		$totalUsed = $now - $this->_startTime;
		$this->_lastLogTime = $now;
		
		$rev = false;
		if( $this->_hLogFile )
		{
			$datetime  = date( "Y-m-d H:i:s" );
			$timestamp = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			$session_id = '';//Pft_Session::getSession()->getUserId();
			$session_name = '';//Pft_Session::getSession()->getUserName();

			$logLine = $datetime . " | " . $timestamp . " | $ip | "
			         . "{$used}ms/{$totalUsed}ms" . " | " . $sourceName . " | " 
			         . $this->_formatInfo( $msg ) . " | " . $level . " | " 
			         . 'uid:' . $session_id . " | uname:" . $session_name . " | "
			         . $_SERVER['REQUEST_URI']. " | "
			         . $actorName . " | " . $actorId . " | " . $exts
			         . "\n";
			$rev = @fwrite( $this->_hLogFile, $logLine );
			return true;
		}
		return $rev;
	}
	
	/**
	 * 将消息格式化为一行
	 */
	private function _formatInfo( $info ){
		//return $info;
		return str_replace( "\n", "<##>" , $info );
	}
	
	function __construct( $logFileName = "", $logPath = "" )
	{
		if( $logFileName != "" )
		{
			$this->_logFileName = $logFileName;
		}
		else
		{
			$this->_logFileName = date( "Ymd" ).".log";
		}

		//$this->_logPath = ($logPath == "")?Pft_Config::getLogPath():$logPath;
		$this->_logPath = ($logPath == "")?dirname( __FILE__ ).'/log/':$logPath;
		$this->_hLogFile = @fopen( $this->_logPath.$this->_logFileName, "a" );
		if( $this->_hLogFile ){
			@chmod( $this->_logPath.$this->_logFileName, 0777 );			
		}
		
		$this->_startTime = $this->_microtime_float();
		$this->_lastLogTime = $this->_startTime;
	}
	
	function __destruct()
	{
		if( $this->_hLogFile ) fclose( $this->_hLogFile );
	}
	
	/**
	 * 返回毫秒数
	 *
	 * @return int
	 */
	private function _microtime_float()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    //return ceil( ((float)$usec + (float)$sec) * 1000 );
	    return (int)( ((float)$usec + (float)$sec) * 1000 );
	}
}
?>