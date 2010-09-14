<?

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

		$datetime  = date( "Y-m-d H:i:s" );
		$timestamp = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		$session_id = Pft_Session::getSession()->getUserId();
		$session_name = Pft_Session::getSession()->getUserName();

		/**
		 * Log Post To test
		 * @author terry
		 * @version 0.1.0
		 * Thu Jul 05 09:38:47 CST 2007
		 */
//			$exts .= 'Post:'.$this->_formatInfo( var_export( $_POST, true  ) );
//			$exts .= 'Cookie:'.$this->_formatInfo( var_export( $_COOKIE, true  ) );
		
		$logLine = $datetime . " | " . $timestamp . " | $ip | "
		         . "{$used}ms/{$totalUsed}ms" . " | " . $sourceName . " | " 
		         . $this->_formatInfo( $msg ) . " | " . $level . " | " 
		         . 'uid:' . $session_id . " | uname:" . $session_name . " | "
		         . $_SERVER['REQUEST_URI']. " | "
		         . $actorName . " | " . $actorId . " | " . $exts
		         . "\n";		
		
		$rev = false;
		if( $this->_hLogFile )
		{
			$rev = fwrite( $this->_hLogFile, $logLine );
		}
		/**
		 * 增加系统级别的日志
		 * @author terry
		 * @version 0.1.0
		 * Thu Sep 20 19:06:00 CST 2007
		 */
		
		exec( "logger $logLine" );
		return $rev;
	}
	
	/**
	 * 将消息格式化为一行
	 */
	private function _formatInfo( $info ){
		//return $info;
		$info = str_replace( "\r", '' , $info );
		return str_replace( "\n", "<##>" , $info );
	}
	
	function __construct( $logFileName = "", $logPath = "" )
	{
		if( $logFileName != "" )
		{
			$this->_logFileName = $logFileName.date( "Ymd" ).".log";
		}
		else
		{
			$this->_logFileName = date( "Ymd" ).".log";
		}

		$this->_logPath = ($logPath == "")?Pft_Config::getLogPath():$logPath;
		if( !file_exists( $this->_logPath ) ){
			mkdir( $this->_logPath, 0777, true );
			@chmod( $this->_logPath, 0777 );
		}
		$this->_hLogFile = fopen( $this->_logPath.$this->_logFileName, "a" );
		@chmod( $this->_logPath.$this->_logFileName, 0766 );
		
		//$this->_startTime = $this->_microtime_float();
		if( defined( 'APP_START_TIME' ) ){
			$this->_startTime = (int) ( APP_START_TIME * 1000 );
		}else{
			$this->_startTime = $this->_microtime_float();				
		}		
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
	    //list($usec, $sec) = explode(" ", microtime());
	    //return ceil( ((float)$usec + (float)$sec) * 1000 );
	    //return (int)( ((float)$usec + (float)$sec) * 1000 );
	    return (int)( microtime( true ) * 1000 );
	}
}