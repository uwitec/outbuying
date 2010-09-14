<?
/**
 * 国际化处理类
 * 目前包括界面上的字符串 将来还应包括货币单位，时间格式等
 * 
 * @author Terry
 * @package Pft
 */
class Pft_I18n{
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
	
	public static function setLang( $lang_code )
	{
		if( $lang_code ){
			if( is_dir( Pft_Config::getLangPath().$lang_code ) )
			{
				self::$lang = $lang_code;				
			}
		}
	}
	
	/**
	 * 翻译一个字串到指定的语言
	 * ！！本方法[不再]自动将 strKey 转成全部大写字母的形式
	 *
	 * @param string $strKey
	 * @return string
	 */
	public static function trans( $strKey )
	{
		if( !self::$isStringsLoaded )
		{
			self::_loadStrings();
		}
		//$strKey = strtoupper( $strKey );
		$strKey = trim( $strKey );
		return key_exists( $strKey, self::$strings )?self::$strings[$strKey]:$strKey;
	}
	
	/**
	 * 载入语言串
	 * 
	 * @todo I18n优化 速度 设置语言的位置
	 */
	private static function _loadStrings()
	{
		if( self::$isStringsLoaded )return;
		
		self::setLang( Pft_Session::getSession()->getLanguage() );

		//include后 1.8 1.9 ms左右
		//include前 1.5 1.6 ms左右 include 还挺费时间...
		//考虑放到各个模块里..用到那个载入哪个？ //这个更像php风格
		//还是放到一起一并载入？
		//先每个人用到的分开，最后合并

		$langPath = Pft_Config::getLangPath().self::$lang.DIRECTORY_SEPARATOR;
		self::_loadLangFilesInDir( $langPath );

		//载入备用目录的语言文件 //
		$langPath = Pft_Config::getLangPath(1).self::$lang.DIRECTORY_SEPARATOR;
		self::_loadLangFilesInDir( $langPath );
		
/* 合并语言文件以后用下面这段代码，合并以前用上面那段
		$str2s = include_once( Pft_Config::getLangPath()
		                      .self::$lang
		                      .DIRECTORY_SEPARATOR."lang.php" );
		self::$strings = array_merge( self::$strings, $str2s);
*/
		
		self::$isStringsLoaded = true;
	}
	
	private static function _loadLangFilesInDir( $langPath )
	{
		if( !is_dir( $langPath ) ) return ;
		$d = dir( $langPath );
		if( is_object( $d ) )
		{
			while (false !== ($entry = $d->read())) {
				if( is_file( $langPath.$entry ) )
				{
					$str2s = include_once( $langPath.$entry );
					if( is_array( $str2s ) )
					{
						self::$strings = array_merge( self::$strings, $str2s);
					}			
				}
			}
			$d->close();			
		}
	}
	
	/**
	 * 格式化时间
	 *
	 * @param int $timestamp
	 */
	public static function formatDate( $timestamp, $format="" )
	{
		if (!$timestamp) 
		{
			return '';
		}
		if( is_numeric($timestamp) ){
			if ($format) 
			{
				return date( "Y-m-d", $timestamp );
			}
			return date( "Y-m-d H:i", $timestamp );			
		}else{
			return $timestamp;
		}
	}
	
	public static function formatCurrency( $number ){
		if( !is_null( $number ) ){
			//return ( sprintf( '%0.2f', $number ) );
			return number_format( $number, 2 );
		}else{
			return '';
		}
	}
}

