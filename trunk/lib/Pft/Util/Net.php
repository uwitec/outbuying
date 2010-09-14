<?
/**
 * 网络相关的工具
 * @author terry
 * Mon Jun 11 11:48:50 CST 2007
 */
class Pft_Util_Net
{
	public static function isLANIp( $ip ){
		return ( preg_match( "/^10.0/", $ip ) || preg_match( "/^192.168/", $ip ) );
	}
}

?>
