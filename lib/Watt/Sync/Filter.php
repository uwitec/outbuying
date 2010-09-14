<?
/**
 * 过滤器管理类
 *
 */
class Watt_Sync_Filter
{
	/**
	 * 过滤
	 *
	 * @param array $data
	 * @return $data
	 */
	public function filter( $data ){
		return $data;
	}
	
	/**
	 * 过滤器池
	 *
	 * @var array
	 */
	private static $_filterPool = array();
	
	/**
	 * 过滤器工厂
	 *
	 * @param string $filterName
	 * @return Watt_Sync_Filter
	 */
	public static function filterFactory( $filterName ){
		if( !isset( self::$_filterPool[$filterName] ) ){
			switch ( $filterName ){
				case 'TpmInToOut':
					self::$_filterPool[$filterName]  = new Watt_Sync_Filter_TpmInToOut();
					break;
				case 'TpmOutToIn':
					self::$_filterPool[$filterName]  = new Watt_Sync_Filter_TpmOutToIn();
					break;
				default:
					self::$_filterPool[$filterName]  = new Watt_Sync_Filter_Normal();
			}
		}
		return self::$_filterPool[$filterName];
	}
}
?>