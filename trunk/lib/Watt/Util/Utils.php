<?
/**
 * 其他重要工具
 * 
 * @version 0.0.1
 */

class Watt_Util_Utils
{
	const GUID_LEN = 36;
	
	/**
	 * 获得 经过 组ID 偏移后的 RealEpollId 
	 *
	 * @param int $idOffset
	 * @return int
	 */
	public static function getRealEpollId( $idOffset=0 ){
		if( defined( 'TQ_33' ) ){
			if (Watt_Config::getEpollGroupId()){
				$epo	=	Watt_Config::getEpollGroupId()*65536*256 + $idOffset;
			}else {
				$epo	=	65536*256 + $idOffset;
			}
		}else{
			$epo = $idOffset;
		}
		return $epo;
	}
	
	/**
	 * 获得一个GUID
	 * 
	 * 形式为 2b4323e4-4801-a075-42c0-45b26bc3be23
	 *
	 * @return string GUID
	 */
	public static function getGuId()
	{
		return self::create_guid();
	}
	
	/**
	 * 判断一个id是否是GuId
	 *
	 * @param string $id
	 * @return boolean
	 */
	public static function isGuId( $id )
	{
		return (strlen( $id ) == Watt_Util_Utils::GUID_LEN);
	}
	
	/**
	 * A temporary method of generating GUIDs of the correct format for our DB.
	 * @return String contianing a GUID in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
	 *
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
	 * All Rights Reserved.
	 * Contributor(s): ______________________________________..
	*/
	private static function create_guid()
	{
	    $microTime = microtime();
		list($a_dec, $a_sec) = explode(" ", $microTime);
	
		$dec_hex = sprintf("%x", $a_dec* 1000000);
		$sec_hex = sprintf("%x", $a_sec);
	
		self::ensure_length($dec_hex, 5);
		self::ensure_length($sec_hex, 6);
	
		$guid = "";
		$guid .= $dec_hex;
		$guid .= self::create_guid_section(3);
		$guid .= '-';
		$guid .= self::create_guid_section(4);
		$guid .= '-';
		$guid .= self::create_guid_section(4);
		$guid .= '-';
		$guid .= self::create_guid_section(4);
		$guid .= '-';
		$guid .= $sec_hex;
		$guid .= self::create_guid_section(6);
	
		return $guid;
	
	}
	
	private static function create_guid_section($characters)
	{
		$return = "";
		for($i=0; $i<$characters; $i++)
		{
			$return .= sprintf("%x", mt_rand(0,15));
		}
		return $return;
	}
	
	private static function ensure_length(&$string, $length)
	{
		$strlen = strlen($string);
		if($strlen < $length)
		{
			$string = str_pad($string,$length,"0");
		}
		else if($strlen > $length)
		{
			$string = substr($string, 0, $length);
		}
	}
}