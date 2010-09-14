<?
/**
 * 
 */

class Watt_Util_Test_Data
{
	/**
	 * 根据data随机生成一些数据
	 * 如果data是数组，则随机取一个元素
	 * 如果data是string，则从随机的位置取$times 至 $times2 间随机长度的字串
	 * 
	 * <code>
	 * $strChr = "abcdefghijklmnopqrstuvwxyz";
	 * $firstname = Watt_Util_String::randomSubstr($strChr,2,3);
	 * 
	 * $arrDomin = array("com","net","org");
	 * $randomDomin = Watt_Util_String::randomSubstr($arrDomin);
	 * </code>
	 *
	 * @param string|array $data
	 * @param int $times
	 * @param int $times2
	 * @return string
	 */
	public static function randomStr($data,$times=1,$times2=1){
		if( is_null( $data ) )$data = "abcdefghijklmnopqrstuvwxyz";
		
		$rev = "";
		if( $times != $times2)$times = rand($times,$times2);
		while( $times-- > 0 ){
			if( is_array($data) ){
				$rev .= $data[mt_rand(0,count($data)-1)];
			}else{
				//考虑到中文字符串的问题，只取偶数位的开始
				$rev .= substr($data,intval(mt_rand(0,strlen($data)-1)/2)*2,2);
			}
		}
		return $rev;
	}
	
	/**
	 * 随机email
	 *
	 * @return string
	 */
	public static function randomEmail()
	{
		$arrDomin = array("com","net","org");
		return self::randomStr(null,2,3)."@".self::randomStr(null,2,3).".".self::randomStr($arrDomin);
	}

	/**
	 * 随机手机
	 *
	 * @return string
	 */
	public static function randomMPhone()
	{
		$arrMPhone = array("130","132","131","135","136","137","138");
		$strNumber = "0123456789";
		return self::randomStr($arrMPhone).self::randomStr($strNumber,4,4);
	}

}