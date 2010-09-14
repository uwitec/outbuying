<?
/**
 * 国际化处理类
 * 目前包括界面上的字符串 将来还应包括货币单位，时间格式等
 * 
 * @author Terry
 * @package Watt
 */
class Watt_I18n{
	const LOG_UNREG_LANG_KEY = false;	//是否记录未注册的多语键值
	
	const I18N_LANGUAGE_DEFAULT = "default";
	const I18N_LANGUAGE_CHS     = "cn_jt";	//zh-cn
	const I18N_LANGUAGE_CHT     = "cn_tw";	//zh-tw
	const I18N_LANGUAGE_EN      = "en";
	const I18N_LANGUAGE_JP      = "jp";
	
	/**
	 * 默认语言
	 *
	 * @var unknown_type
	 */
	private static $lang = "default";

	/**
	 * @var Watt_I18n_StringLoader_Interface
	 */
	private static $_stringLoader;
	
	/**
	 * 翻译一个字串到指定的语言
	 * 大小写敏感
	 * @param string $strKey
	 * @return string
	 */
	public static function trans($strKey){
		$theLoader = self::_getLoader();
		$rev = $theLoader->trans($strKey);
		if( $rev === false ){
			if(self::LOG_UNREG_LANG_KEY){
				if(class_exists('Watt_Log')){
					$logger = new Watt_Log_File('LOG_UNREG_LANG');
					$logger->log($strKey."			|	".self::$lang."	");

					$fp = fopen(Watt_Config::getRootPath().'/log/LOG_UNREG_LANG_'.self::$lang."-".date("Ymd").'.txt', 'a+');
					fwrite($fp, "\$s['$strKey']	=	'$strKey';\n");
					fclose($fp);
				}
			}
			return $strKey;
		}else{
			return $rev;
		}
	}
	
	/**
	 * @return Watt_I18n_StringLoader_Interface
	 */
	private static function _getLoader(){
		if( !self::$_stringLoader ){
			$lang = Watt_Session::getSession()->getLanguage();
			if(self::_connectMemcache()){
				$theLoader = new Watt_I18n_StringLoader_MemcacheLoader();
			}else{
				$theLoader = new Watt_I18n_StringLoader_FileLoader();		
			}
			self::setLang($lang);//注意順序。因為setLang里会检测语言路径 by terry at Fri Aug 27 17:29:59 CST 2010
			$theLoader->setLang(self::$lang);
			self::$_stringLoader = $theLoader;
		}
		return self::$_stringLoader;
	}
	
	/**
	 * 是否载入了语言文件
	 *
	 * @var boolean
	 */
	private static $isStringsLoaded = false;
	
	/**
	 * 记录语言 key 对应的翻译
	 *
	 * @var array
	 */
	private static $strings = array();
	
	public static function setLang( $lang_code ){
		if( $lang_code ){
			if( is_dir( Watt_Config::getLangPath().$lang_code ) ){
				self::$lang = $lang_code;				
			}else{
				if(class_exists('Watt_Log')){
					Watt_Log::addLog('No language file for ['.$lang_code.']');	
				}
			}
		}
	}
	
	/**
	 * 重新装载字串
	 */
	public static function reloadString(){
		//self::setLang( Watt_Session::getSession()->getLanguage() );
		self::_connectMemcache();
		$theLoader = self::_getLoader();
		
		$langs = array("default", "cn_jt", "en", "jp");//, "cn_tw"
		foreach ($langs as $lang) {
			self::$isStringsLoaded = false;
			self::setLang( $lang );
			$theLoader->setLang( $lang );
			if(self::$_memcache){
				self::$_memcache->set( self::$_memcacheKeyPrefix.self::$lang.'_LOADED', 0);
				echo( "Load : ".self::$_memcacheKeyPrefix.self::$lang.'_LOADED'."<br/>" );
				echo( "Rev : ".self::$_memcache->get( self::$_memcacheKeyPrefix.self::$lang.'_LOADED')."<br/>" );
				//exit();
			}
			$theLoader->reloadStrings();
			echo( "Load : ".self::$_memcacheKeyPrefix.self::$lang.'_LOADED'."<br/>" );
			echo( "Rev : ".self::$_memcache->get( self::$_memcacheKeyPrefix.self::$lang.'_LOADED')."<br/>" );
		}
	}
	
	private static $_memcacheKeyPrefix = 'TPMI18N_';
	private static $_memcache = null;
	
	private static function _connectMemcache(){
		$memcacheHost = Watt_Config::getCfg("MEMCACHE_HOST");
		$memcachePort = Watt_Config::getCfg("MEMCACHE_PORT");
		if( $memcacheHost && $memcachePort ){
			$memcache = new Memcache;
			if( $memcache->connect($memcacheHost, $memcachePort) ){
				self::$_memcache = $memcache;
			}
		}
		return self::$_memcache;
	}
	
	/**
	 * 载入语言串
	 * 
	 * @todo I18n优化 速度 设置语言的位置
	 */
	private static function _loadStrings()
	{
		if( self::$isStringsLoaded )return;

		Watt_Debug::addInfoToDefault('Before load language string.');
		self::setLang( Watt_Session::getSession()->getLanguage() );
		self::_connectMemcache();
		if(self::$_memcache){			
			if( self::$_memcache->get(self::$_memcacheKeyPrefix.self::$lang.'_LOADED') ){
				self::$isStringsLoaded = true;
				Watt_Debug::addInfoToDefault('After load language string.');
				return ;
			}
			Watt_Debug::addInfoToDefault('Before load language string from memcache.');
		}
		
		//include后 1.8 1.9 ms左右
		//include前 1.5 1.6 ms左右 include 还挺费时间...
		//考虑放到各个模块里..用到那个载入哪个？ //这个更像php风格
		//还是放到一起一并载入？
		//先每个人用到的分开，最后合并
		$langPath = Watt_Config::getLangPath().self::$lang.DIRECTORY_SEPARATOR;
		self::_loadLangFilesInDir( $langPath );

		//载入备用目录的语言文件 //
		$langPath = Watt_Config::getLangPath(1).self::$lang.DIRECTORY_SEPARATOR;
		self::_loadLangFilesInDir( $langPath );
		
/* 合并语言文件以后用下面这段代码，合并以前用上面那段
		$str2s = include_once( Watt_Config::getLangPath()
		                      .self::$lang
		                      .DIRECTORY_SEPARATOR."lang.php" );
		self::$strings = array_merge( self::$strings, $str2s);
*/
		
		if( self::$_memcache ){
			self::$_memcache->set(self::$_memcacheKeyPrefix.self::$lang.'_LOADED', true);
			Watt_Debug::addInfoToDefault('After load language string from memcache.');
		}

		self::$isStringsLoaded = true;
		Watt_Debug::addInfoToDefault('After load language string.');
	}
	
	/**
	 * 格式化时间
	 *
	 * @param int $timestamp
	 */
	public static function formatDate( $timestamp, $format="" ){
		if (!$timestamp){
			return '';
		}
		if( is_numeric($timestamp) ){
			if ($format){
				if($format == 3){
					return date( "Y-m-d H:i:s", $timestamp );		
				}
				if( strlen($format) < 2 ){
					return date( "Y-m-d", $timestamp );
				}else{
					return date( $format, $timestamp );	
				}
			}
			return date( "Y-m-d H:i", $timestamp );			
		}else{
			return $timestamp;
		}
	}
	
	/**
	 * 将周期转化为小时数
	 *
	 * @param int $number
	 * @return int
	 */
	public static function formatHours($timestamp){
		if (!$timestamp){
			return '';
		}
		if( is_numeric($timestamp) ){
			$hours = ceil($timestamp/3600);
			return $hours;
		}
		return '';
	}
	
	public static function formatCurrency( $number ){
		if( !is_null( $number ) ){
			//return ( sprintf( '%0.2f', $number ) );
			return number_format( $number, 2 );
		}else{
			return '';
		}
	}
	
	/**
	 * 获取string
	 * @return array
	 * @author terry
	 * Mon Dec 01 14:55:51 CST 2008
	 */
	private static function _getStrings(){
		if( !self::$isStringsLoaded ){
			self::_loadStrings();
		}
		return self::$strings;
	}
	
	/**
	 * 通过文字查找Key
	 * @author terry
	 * @return array
	 * Mon Dec 01 14:54:00 CST 2008
	 */
	public static function findKeyByStr( $str ){
		$strings = self::_getStrings();
		$rev = array();
		foreach ($strings as $key => $string) {
			if( false !== strpos( $string, $str ) ){
				$rev[$key] = $string;
			}
		}
		return $rev;
	}
}

class Watt_I18n_StringLoader_FileLoader implements Watt_I18n_StringLoader_Interface{
	private $_lang = 'default';

	/**
	 * 记录语言 key 对应的翻译
	 *
	 * @var array
	 */
	private $_strings = array();
	private $_isLoadedString = false;

	private function _loadStrings(){
		if(!$this->isLoadedStrings()){
			Watt_Debug::addInfoToDefault('Before load language string.');
			//include后 1.8 1.9 ms左右
			//include前 1.5 1.6 ms左右 include 还挺费时间...
			//考虑放到各个模块里..用到那个载入哪个？ //这个更像php风格
			//还是放到一起一并载入？
			//先每个人用到的分开，最后合并
			$langPath = Watt_Config::getLangPath().$this->_lang.DIRECTORY_SEPARATOR;
			if(!file_exists($langPath)){
				$langPath = Watt_Config::getLangPath()."default".DIRECTORY_SEPARATOR;
			}
			$this->_loadLangFilesInDir( $langPath );
	
			//载入备用目录的语言文件 //
			//$langPath = Watt_Config::getLangPath(1).$this->_lang.DIRECTORY_SEPARATOR;
			//$this->_loadLangFilesInDir( $langPath );
			/* 合并语言文件以后用下面这段代码，合并以前用上面那段
					$str2s = include_once( Watt_Config::getLangPath()
					                      .self::$lang
					                      .DIRECTORY_SEPARATOR."lang.php" );
					self::$strings = array_merge( self::$strings, $str2s);
			*/
			Watt_Debug::addInfoToDefault('After load language string.');
		}
		return true;
	}

	private function _loadLangFilesInDir( $langPath ){
		if( !is_dir( $langPath ) ) return ;
		$d = dir( $langPath );
		if( is_object( $d ) ){
			while (false !== ($entry = $d->read())){
				if( is_file( $langPath.$entry ) && strtolower(substr($entry,-4,4)) == '.php' ){
					$str2s = include_once( $langPath.$entry );
					if( is_array( $str2s ) ){
						$this->_strings = array_merge( $this->_strings, $str2s);							
					}
				}
			}
			$d->close();			
		}
	}

	public function setLang($lang){
		$this->_lang = $lang;
	}
	
	/**
	 * @return boolean
	 */
	public function isLoadedStrings(){
		return $this->_isLoadedString;
	}
	
	public function reloadStrings(){
		$this->_isLoadedString = false;
		$this->_loadStrings();
		return true;
	}
	
	/**
	 * @return string
	 */
	public function trans($strKey){
		if( !$this->isLoadedStrings() ){
			$this->_loadStrings();
		}
		//$strKey = strtoupper( $strKey );
		$strKey = trim( $strKey );
		$bFind = true;
		if( key_exists( $strKey, $this->_strings ) ){
			$val = $this->_strings[$strKey];
		}else{
			$bFind = false;
		}
		if( $bFind ){
			return $val;
		}else{
			return false;	//return $strKey;
		}
	}
}

class Watt_I18n_StringLoader_MemcacheLoader{
	private $_lang = 'default';
	private $_isLoadedString = false;
	

	private $_memcacheKeyPrefix = 'TPMI18N_';
	private $_memcache = null;
	
	private function _connectMemcache(){
		if( !$this->_memcache ){
			$memcacheHost = Watt_Config::getCfg("MEMCACHE_HOST");
			$memcachePort = Watt_Config::getCfg("MEMCACHE_PORT");
			if( $memcacheHost && $memcachePort ){
				$memcache = new Memcache;
				if( $memcache->connect($memcacheHost, $memcachePort) ){
					$this->_memcache = $memcache;
				}
			}
		}
		return $this->_memcache;
	}
	public function setLang($lang){
		$this->_lang = $lang;
	}
	
	/**
	 * @return boolean
	 */
	public function isLoadedStrings(){
		$this->_isLoadedString;
	}
	
	public function reloadStrings(){
		$this->_isLoadedString = false;
		$this->_connectMemcache();
		$this->_memcache->set($this->_memcacheKeyPrefix.$this->_lang.'_LOADED', false);
		$this->_loadStrings();
		return true;
	}
	
	/**
	 * @return string
	 */
	public function trans($strKey){
		if( !$this->isLoadedStrings() ){
			$this->_loadStrings();
		}
		//$strKey = strtoupper( $strKey );
		$strKey = trim( $strKey );
		$bFind = true;
		$val = $this->_memcache->get( $this->_memcacheKeyPrefix.$this->_lang.'_'.base64_encode($strKey) );
		if( $val === false ){
			$bFind = false;
		}
		if( $bFind ){
			return $val;
		}else{
			return false;	//return $strKey;
		}
	}
	
	private function _loadStrings(){
		$this->_connectMemcache();
		if($this->_memcache){			
			if( $this->_memcache->get($this->_memcacheKeyPrefix.$this->_lang.'_LOADED') ){
				$this->isStringsLoaded = true;
				Watt_Debug::addInfoToDefault('After load language string from memcache.(cached)');
				return true;
			}
			Watt_Debug::addInfoToDefault('Before load language string from memcache.');

			$langPath = Watt_Config::getLangPath().$this->_lang.DIRECTORY_SEPARATOR;
			if(!file_exists($langPath)){
				$langPath = Watt_Config::getLangPath()."default".DIRECTORY_SEPARATOR;
			}
			$this->_loadLangFilesInDir( $langPath );
	
			//载入备用目录的语言文件 //
			//$langPath = Watt_Config::getLangPath(1).$this->_lang.DIRECTORY_SEPARATOR;
			//$this->_loadLangFilesInDir( $langPath );

			$this->_memcache->set($this->_memcacheKeyPrefix.$this->_lang.'_LOADED', true);
			Watt_Debug::addInfoToDefault('After load language string from memcache.');
			return true;
		}else{
			return false;
		}
	}

	private function _loadLangFilesInDir( $langPath ){
		if( !is_dir( $langPath ) ) return ;
		$d = dir( $langPath );
		if( is_object( $d ) ){
			while (false !== ($entry = $d->read())){
				if( is_file( $langPath.$entry ) && strtolower(substr($entry,-4,4)) == '.php' ){
					$str2s = include_once( $langPath.$entry );
					if( is_array( $str2s ) ){
						foreach ($str2s as $k => $v) {
							$this->_memcache->set($this->_memcacheKeyPrefix.$this->_lang.'_'.base64_encode($k), $v);
						}
					}
				}
			}
			$d->close();			
		}
	}
}

interface Watt_I18n_StringLoader_Interface{
	public function setLang($lang);
	
	/**
	 * @return boolean
	 */
	public function isLoadedStrings();
	
	public function reloadStrings();
	
	/**
	 * @return string
	 */
	public function trans($strKey);
}