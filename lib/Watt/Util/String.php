<?
/**
 * 字符串相关的工具函数
 *
 * @author Terry
 * @package Watt_Util
 */

class Watt_Util_String{
	/**
	 * 全角转半角
	 *
	 * @param string $str
	 * @return string
	 */
	public static function quanjiao2banjiao( $str ){
		$arr	=	array(
		'0'=>'０','a'=>'ａ','k'=>'ｋ',
		'1'=>'１','b'=>'ｂ','l'=>'ｌ',
		'2'=>'２','c'=>'ｃ','m'=>'ｍ',
		'3'=>'３','d'=>'ｄ','n'=>'ｎ',
		'4'=>'４','e'=>'ｅ','o'=>'ｏ',
		'5'=>'５','f'=>'ｆ','p'=>'ｐ',
		'6'=>'６','g'=>'ｇ','q'=>'ｑ',
		'7'=>'７','h'=>'ｈ','r'=>'ｒ',
		'8'=>'８','i'=>'ｉ','s'=>'ｓ',
		'9'=>'９','j'=>'ｊ','t'=>'ｔ',
		'u'=>'ｕ','v'=>'ｖ','w'=>'ｗ',
		'x'=>'ｘ','y'=>'ｙ','z'=>'ｚ',
		'!'=>'！','#'=>'＃','$'=>'￥',
		'@'=>'＠','%'=>'％',
		'^'=>'＾','&'=>'＆','*'=>'＊',
		'('=>'（',')'=>'）','_'=>'＿',
		'+'=>'＋','|'=>'｜','{'=>'｛',
		'}'=>'｝','"'=>'＂',':'=>'：',
		'<'=>'＜','>'=>'＞','?'=>'？',
		'-'=>'－','='=>'＝',
		';'=>'；',','=>'，',
		'.'=>'．','/'=>'／');
		foreach ($arr as $key=>$van)
		{
			$str	=	str_replace($van,$key,$str);
		}
		return $str;
	}


	/**
	 * 据测试更好用些
	 * Returns true if $string is valid UTF-8 and false otherwise.
	 * http://it.oyksoft.com/post/49/
	 *
	 * @param string $word
	 * @return boolean
	 */
	public static function is_utf8($word){
		if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$word) == true){
			return true;
		}else{
			return false;
		}

	} // function is_utf8

	/**
	 * http://syre.blogbus.com/logs/4403268.html
	 *
	 * @param string $string
	 * @return boolean
	 */
	public static function is_utf8_v08($string) {
		// From http://w3.org/International/questions/qa-forms-utf-8.html
		return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
   )*$%xs', $string);  
	}

	/**
	 * 计算有效的字符数 | 去除回车/换行/空格
	 *
	 * @param string $str
	 */
	public static function countValidCharLen( $str ){
		return iconv_strlen( $str, 'UTF-8' );
	}

	/**
	 * 过滤字符串
	 *
	 * @param string $str 欲过滤的字符串
	 * @param array|string $maskList 数组或者,分隔的字符串
	 * @param string $mask 过滤后的替换字符
	 * @return string
	 */
	public static function filterString( $str, $maskList, $mask='--' ){
		if( !is_array( $maskList ) ){
			$maskList = explode( ',', $maskList );
		}
		foreach ( $maskList as $val ){
			$count = 0;
			$str = str_replace( $val, $mask, $str, $count );
			if( $count > 0 ){
				if(class_exists('Watt_Log')){
					Watt_Log::addLog( "filter string:[$val]", Watt_Log::LEVEL_INFO );
				}
			}
		}
		return $str;
	}

	/**
	 * 过滤数组
	 * @todo 未编写逻辑
	 * @param array $array
	 * @param array|string $maskList 数组或者,分隔的字符串
	 * @param string $mask 过滤后的替换字符
	 * @return $array
	 */
	public static function filterArray( $array, $maskList, $mask='--'  ){
		return $array;
	}

	public static function iconv_split( $str, $charset='UTF-8' ){
		$rev = array();
		for( $i=0;$i=iconv_strlen($str,$charset);$i++ ){
			$rev[] = iconv_substr($str,$i,1,$charset);
		}
		return $rev;
	}
	/**
	 * 格式化输出
	 *
	 * @param unknown_type $string
	 * @param unknown_type $option
	 * @return unknown
	 */
	public static function sechof($string,$option=false,$tihuanfuhao=true)
	{
		$num=2;
		if($option)
		{
			$num=$option;
		}
		$string = number_format($string,$num,'.',',');
		if($string >= 0){
			return $string;
		}else{
			if($tihuanfuhao){
				return '<span style="color:red;">'.str_replace('-','多',$string).'</span>';
			}else{
				return '<span style="color:red;">'.$string.'</span>';
			}
		}
	}

	public static function getround($vall){
		if($vall)
		{
			return number_format($vall,2);
		}
		else
		{
			return "0.00";
		}
	}

	public static function htmlBlank( $str ){
		return str_replace( ' ', '&nbsp;', $str );
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $str
	 * @return unknown
	 */
	function getUnicode( $str )
	{

		$unicode = array();
		$values = array();
		$lookingFor = 1;

		for ($i = 0; $i < strlen( $str ); $i++ )
		{
			$thisValue = ord( $str[ $i ] );

			//如果ASCII值小于65
			if ( $thisValue < ord('A') )
			{
				//如果ASCII值在48到57之间
				if ($thisValue >= ord('0') && $thisValue <= ord('9'))
				{
					$unicode[] = chr($thisValue);
				}else {//如果是ASCII值中的一些符号,如：#,!
					//$unicode[] = '%'.dechex($thisValue);
				}
			} else {
				//如果值在ASCII值的范围内直接返回相应的十进制值
				if ( $thisValue < 128)
				{
					$unicode[] = ord($str[ $i ]);
				}else {//如果其值大于ASCII值的范围则权限一定的算法返回相应的unicode值
					if ( count( $values ) == 0 ) $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
					$values[] = $thisValue;
					if ( count( $values ) == $lookingFor )
					{
						$number = ( $lookingFor == 3 ) ?
						( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
						( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
						$number = dechex($number);
						$unicode[] = base_convert((strlen($number)==3)?"%u0".$number:"%u".$number,16,10);
						$values = array();
						$lookingFor = 1;
					}
				}
			}
		}
		if(count($unicode) == 1)return $unicode[0];
		else return $unicode;
	}


	/**
	 * Enter description here...
	 *
	 * @param unknown_type $str
	 * @return unknown
	 */
	public static function getSingleLetterUnicode($str)
	{
		$result = Watt_Util_String::getUnicode($str);
		$i=0;
		foreach($result as $v)
		{
			if($v > 255)$i++;
		}
		return $i;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $str
	 * @return unknown
	 */
	public static function getStringUnicode($str)
	{
		$counter=0;
		$str_len = strlen($str);
		for($i=0;$i<$str_len;$i++){

			while( ($i<$str_len)&& (chr($str[$i])==' '|| Watt_Util_String::getUnicode($str[$i])>255 || chr($str[$i])=='\n' )) $i++;
			if( chr($str[$i+1])==' '|| chr($str[$i+1])=='\n' || Watt_Util_String::getUnicode($str[$i+1])>255 || $i==$str_len-1 ) $counter++;
		}
		return $counter;
	}

	public static function statWordsNum($str)
	{
		return Watt_Util_String::getSingleLetterUnicode($str)+Watt_Util_String::getStringUnicode($str);
	}

	public static function addBlankInStrMiddle($str){
		$len = iconv_strlen( $str, 'UTF-8' );
		$mid = intval($len/2);
		return iconv_substr( $str, 0, $mid, 'UTF-8' ).' '.iconv_substr( $str, $mid, $len, 'UTF-8' );
	}

	/**
	 *  1 | >0  current version is greater than $toCompareVersion
	 *  0 =     current version is equare to $toCompareVersion
	 * -1 | <0  current version is less than $toCompareVersion
	 * 
	 * @param string $version
	 * @param string $toCompareVersion
	 * @return >0 | 0 | <0
	 * 
	 * @author terry
	 * Tue Aug 18 16:33:41 CST 2009
	 */
	public static function compareVersion( $version, $toCompareVersion ){
		if( $version == $toCompareVersion ){
			return 0;
		}else{
			if( !$version ){
				//如果没有注册TqVersion， currentVersion < 任何有值的 version $toCompareVersion
				return -1;
			}else{
				$toVersionArr      = explode( '.', $toCompareVersion );
				$currentVersionArr = explode( '.', $version );
				for ( $i = 0; $i < count( $toVersionArr ); $i++ ){
					if( $currentVersionArr[$i] != $toVersionArr[$i] ){
						return intval( $currentVersionArr[$i] ) - intval( $toVersionArr[$i] ) ;
					}
				}
				return 0;	//依次比较完成，说明相等
			}
		}
	}
}