<?

class Pft_View_Helper_Js{
	/**
	 * php数组获取js数组
	 *
	 * 参数: void
	 * 返回: void
	 * 作用域: public
	 * 日期: 2006-9-29
	 */
	public static function makeJsArray($array_data,$array_name)
	{
		if (!is_array($array_data))
		{
			$array_data = array();
		}
		$html = "var ".$array_name . "=[];";

		for ($i=0; $i< count($array_data); $i++)
		{
			$strTmp = str_replace(chr(13),"\\n",$array_data[$i]);//JOhn   替换回车、换行2006-12-14
			$strTmp = str_replace(chr(10),"",$strTmp);
			$html .= $array_name."[".$i."]='".$strTmp."';";
		}

		
		return $html;
	} // end func
	
	/**
	 * 用php数组生成 js 数组
	 *
	 * @param array $array_data
	 * @param string $array_name
	 */
	public static function makeJsArrayExpress( $array_data, $array_name )
	{
		if (!is_array($array_data))
		{
			$array_data = array();
		}
		//$html = "var ".$array_name . "=".
		$html = $array_name . "=".

		$html = self::_toJsArray( $array_data );
		$html = rtrim( $html, "," );

		$html .= ";\n";
		
		return $html;
	}
	
	private static function _toJsArray( $array_data, $level=0 )
	{
		$pretab = str_repeat( "\t", $level );
		$html = "";
		$html .= "\n".$pretab."[";

		foreach ( $array_data as $item )
		{
			if( is_array( $item ) )
			{
				$html .= self::_toJsArray( $item, $level+1 ).",";
			}
			elseif( is_null( $item ) )
			{
				$html .= "null,";
			}
			else
			{
				$html .= "'".$item."',";
			}
//			$strTmp = str_replace(chr(13),"\\n",$array_data[$i]);//JOhn   替换回车、换行2006-12-14
//			$strTmp = str_replace(chr(10),"",$strTmp);
//			$html .= $array_name."[".$i."]='".$strTmp."';";
		}
		$html = rtrim( $html, "," );

		$html .= "]";
		return $html;
	}
}