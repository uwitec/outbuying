<?
/**
 * Pft基础类库
 *
 * @author Terry
 * @package Pft
 */

/**
 * 调试类, 调试信息通过这个类来输出
 *
 * 使用方式如下
 * <code>
 * Pft_Debug::addInfo( basename(__FILE__), "testDebugInfo");
 * </code>
 *                      Source               信息
 * @author Terry
 * @package Pft
 */
class Pft_Debug{
	/*timestamp , source, info*/
	protected $_debugInfo    = array();
	protected $_startTime    = 0;
	protected $_lastStepTime = 0;
	
	/**
	 * @var Pft_Debug
	 */
	protected static $_defaultDebug;

	/**
	 * 获得默认的Debuger
	 *
	 * @return Pft_Debug
	 */
	public static function getDefaultDebug(){
		if( !self::$_defaultDebug ){
			$theDebuger = new Pft_Debug();
			if( defined( 'APP_START_TIME' ) ){
				$theDebuger->setStartTime( (int) ( APP_START_TIME * 1000 ) );
			}
			$theDebuger->begin();
			self::$_defaultDebug = $theDebuger;
		}
		return self::$_defaultDebug;
	}

	/**
	 * 添加一条调试信息
	 *
	 * @param string $source 发生源
	 * @param string $info 信息
	 */
	public function addInfo($source="", $info=""){
		if( defined("DEBUG") && DEBUG ){
			$now_time = $this->_microtime_float();
			$aDebugInfo = array( $now_time, $this->_lastStepTime, $source, $info );
			if( function_exists( 'memory_get_usage' ) ){
				$aDebugInfo[] = memory_get_usage();
			}else{
				$aDebugInfo[] = 0;
			}
			$this->_debugInfo[] = $aDebugInfo;
			$this->_lastStepTime = $now_time;
		}
	}

	/**
	 * 用添加一条信息到默认的 debuger
	 * 一般用 __FILE__.__LINE__作为 source比较好
	 * 
	 * @param String $source
	 * @param String $info
	 */
	public static function addInfoToDefault( $source="", $info="" ){
		if( defined("DEBUG") && DEBUG ){
			self::getDefaultDebug()->addInfo( $source, $info );
		}
		//Pft_Log::addLog($info, Pft_Log::LEVEL_DEBUG);		
	}	
	
	public function setStartTime( $ms ){
		$this->_startTime = $ms;
	}
	
	/**
	 * 开始进行计时
	 * 本操作将重新设置调试器的开始时间
	 *
	 */
	public function begin(){
		if( defined("DEBUG") && DEBUG ){
			if(!$this->_startTime){
				$this->_startTime = $this->_microtime_float();		
			}
			$this->_lastStepTime = $this->_startTime;
		}		
	}
	
	public function clearDebugInfo(){
		$this->_debugInfo = array();
	}
	
	/**
	 * 输出调试信息
	 *
	 */
	public function output(){
		if( defined("DEBUG") && DEBUG && count( $this->_debugInfo ) > 0 ){
			//echo "<pre>";
			$lastTime = $this->_startTime;
			$endTime = $this->_microtime_float();
			// . " | " . $info[0] 
			foreach ( $this->_debugInfo as $info )
			{
//				print "At " . ($info[0]-$this->_startTime) 
//				    . " ms | Pre step used " . ($info[0] - $lastTime) . " ms | " 
//				    . $info[1] . " | " . $info[2] . "\n";
//				$lastTime = $info[0];
				print "<div style='width:100%;'>At " . ($info[0]-$this->_startTime) 
				    . "ms | +" . ($info[0] - $info[1]) . "ms | " 
				    . $info[4] . "B | "
				    . $info[2] . " | " . $info[3] . "</div>\n";
				$lastTime = $info[0];
			}

			$totalTime = $endTime - $this->_startTime;
			$lastestStep = $endTime - $lastTime;
			echo "<div>Lastest steps used $lastestStep ms</div>\n";
			echo "<div>All steps used $totalTime ms</div>\n";
			print"<pre>Terry :";print_r( get_included_files() );print"</pre>";
			//echo "</pre>";
		}
	}
	
	/**
	 * 返回毫秒数
	 *
	 * @return int
	 */
	private function _microtime_float(){
	    //list($usec, $sec) = explode(" ", microtime());
	    //return ceil( ((float)$usec + (float)$sec) * 1000 );
	    //return (int)( ((float)$usec + (float)$sec) * 1000 );
	    return  (int)( microtime( true ) * 1000 );
	}
}
?>