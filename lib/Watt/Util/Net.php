<?
/**
 * �?�???��?��??工�??
 * @author terry
 * Mon Jun 11 11:48:50 CST 2007
 */
class Watt_Util_Net
{
	public static function isLANIp( $ip ){
		return ( preg_match( "/^10./", $ip )
		      || preg_match( "/^192.168./", $ip )
		      || preg_match( "/^172.((1[6789])|(2[0-9])|(3[01]))./", $ip )
		      || preg_match( "/^127.0/", $ip )
		       );
	}
}

?>
