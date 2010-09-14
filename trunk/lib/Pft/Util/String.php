<?
/**
 * 字符串相关的工具函数
 *
 * @author Terry
 * @package Pft_Util
 */

class Pft_Util_String{
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
	 * http://syre.blogbus.com/logs/4403268.html
	 *
	 * @param string $string
	 * @return boolean
	 */
	public static function is_utf8($string) {
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
	 * @param unknown_type $str
	 */
	public static function countValidCharLen( $str ){
		return iconv_strlen( $str, 'UTF-8' );
	}
}

