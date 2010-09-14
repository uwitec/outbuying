<?
class Watt_Util_SearchTip{
	/**
	 * @param string $tipType
	 * @return Watt_Util_SearchTip
	 */
	public static function factory( $tipType ){
		Watt_Log::addLog($tipType);
		return new $tipType();
		/*
		switch ( $tipType ){
			case "xiangmubianhao":
				return new Tpm_Util_SearchTip_XmBianhao();
				break;
			case "ddbianhao":
				return new Tpm_Util_SearchTip_DdBianhao();
				break;
			default:
				return new Watt_Util_SearchTip_DdBianhao();
				break;
		}
		*/
	}
	
	public function search( $param ){
		
	}
}


?>