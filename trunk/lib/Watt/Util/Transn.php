<?
/*
*@说明:自动翻译工具集
*@作者:John
*@时间:Tue May 12 14:22:26 CST 2009
*/

class Watt_Util_Transn{
	/*
	* @说明：根据传递过来的不同参数 调用不同的内容
	* @参数：  $lang 翻译语种  $text 要翻译的文字
	* @返回: 翻译后的结果
	* @作者：John
	* @时间：Tue May 12 14:22:54 CST 2009
	*/
	public static function getTransn($lang,$text)
	{
		
		if($rev=self::yahoo($lang,$text))
		{
			return $rev;
		}
		else if($rev=self::google($lang,$text))
		{
			return $rev;
		}
		else if($rev=self::xiamen($lang,$text))
		{
			return $rev;
		}
		else 
		{
			return null;
		}
	}
	/*
	* @说明：google的即时翻译
	* @参数：
	* @返回:
	* @作者：John
	* @时间：Tue May 12 14:33:30 CST 2009
	*/
	private static function google($lang,$text)
	{
		if( $lang == '12' ){
				$sl = "zh-CN";
				$tl = "en";
			}else{
				$sl = "en";
				$tl = "zh-CN";
			}
			$trans=null;
			$url = "http://translate.google.cn/translate_t";
			$params = array(
				"hl" => "zh-CN",
				"ie" => "UTF-8",
				"js" => "n",
				"prev" => "_t",
				"sl" => $sl,
				//"text" => iconv('GBK','UTF-8',$text),
				"text" => $text,
				"tl" => $tl,
			);
			$rev = iconv('GBK','UTF-8',Watt_Http_Client::curlPost($url,$params));
			
			$begin = '<div id=result_box dir="ltr">';
			$firstPos = strpos( $rev, $begin );
			if( $firstPos !== false ){
				$secondPos = strpos( $rev, '</div>', $firstPos );
				$trans = substr( $rev, $firstPos + strlen($begin), $secondPos - $firstPos - strlen($begin) );
				$trans = str_replace('<br> ','',$trans);
			}
			return $trans;	
	}
	/*
	* @说明：YAHOO翻译
	* @参数：
	* @返回:
	* @作者：John
	* @时间：Tue May 12 16:02:22 CST 2009
	*/
	private static function yahoo($lang,$text)
	{
		$url = TpmPeizhiPeer::getPeizhiByPzMingcheng('SERVER_TRANSLATE');
		if( !$url )
		$url = 'http://fanyi.cn.yahoo.com/translate_txt';
		if( $lang == '12' ){
				$sl = 'zh_en';
			}else{
				$sl = 'en_zh';
			}
			$trans=null;
			$params="ei=UTF-8&fr=&lp=".$sl."&trtext=".urlencode($text);
			$rev = Watt_Http_Client::curlPost( $url, $params );
			$begin = '<div id="pd" class="pd">';
			$firstPos = strpos( $rev, $begin );
			if($firstPos!==false)
			{
				$endPos=strpos($rev, '</div>',$firstPos);
				$trans=trim(substr($rev,$firstPos+strlen($begin),$endPos-$firstPos-strlen($begin)));
			}
			return $trans;
	}
	/*
	* @说明：不知道是哪个的即时翻译
	* @参数：
	* @返回:
	* @作者：John
	* @时间：Tue May 12 14:35:48 CST 2009
	*/
	private static function xiamen($lang,$text)
	{
		$url = TpmPeizhiPeer::getPeizhiByPzMingcheng('SERVER_TRANSLATE');
		if( !$url )
		//$url = 'http://mt.xmu.edu.cn/translate';
		$url = 'http://59.77.17.127/translate';
		$param = array(
			'text' => $text,
			'lang' => $lang,
		);
		$trans=null;
		watt_log::addLog('Before translate');
		$params = 'text='.urlencode($param['text']).'&lang='.$param['lang'];
		$rev = Watt_Http_Client::curlPost( $url, $params );
		//$rev = Watt_Http_Client::post_data( $url, $param );
		watt_log::addLog('After translate');
		$begin = '<textarea name="text" wrap="virtual"  rows=8 cols=40>';
		$firstPos = strpos( $rev, $begin );
		
		if( $firstPos !== false ){
			$secondPos = strpos( $rev, '</textarea>', $firstPos );
			$trans = substr( $rev, $firstPos + strlen($begin), $secondPos - $firstPos - strlen($begin) );
		}
		return  $trans;	
	}
}

?>