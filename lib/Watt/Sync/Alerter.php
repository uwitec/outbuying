<?
class Watt_Sync_Alerter{
	/**
	 * @return Watt_Sync_Alerter
	 *
	 */
	public static function factory(){
		return new Watt_Sync_Alerter();
	}
	
	private $_recerverlists =array();
	
	Public function __construct(){
		$this->_recerverlists = Watt_Config::getCfgFromFile('sync/alertMonitor.conf.php');	
	}
	const ALERT_TYPE_LEVEL_1 = 1;
	const ALERT_TYPE_LEVEL_2 = 2;
	const ALERT_TYPE_REPORT  = 99;

	const NOTICE_TYPE_MAIL = 'mail';
	const MOTICE_TYPE_SMS  = 'sms';
	/*private $_recerverlists = array('1'=> array(
												'sms'=>'13391827932',
												'mail'=>'Jute@wattcan.net',
												),
									'2'=> array(
												'sms'=>'13391827932',
												'mail'=>'Jute@wattcan.net',
												),
									'99'=> array(
												'mail'=>'Jute@wattcan.net',
												),
									);
									*/
	
	
	/**
	 * 设置接受者列表
	 * 列表结构为
	 * reviceverlist = array(
							array( 'sms' => '123456789'),
							array( 'mail' => 'mail@abc.com');
						);
	 *
	 * @param Watt_Sync_Alerter::ALERT_TYPE_ $alertType
	 * @param unknown_type $recerverlist
	 */
	public function setReceiverList( $alertType, $recerverlist ){
		$this->_recerverlists[$alertType] = $recerverlist;
	}
	
	public function getReceiverList( $alertType ){
		if( key_exists( $alertType, $this->_recerverlists ) ){
			return $this->_recerverlists[$alertType];
		}else{
			return null;
		}
	}
	
	public function notice( $alertType, $msg ){
		$msg .= ' '.date( 'Y-m-d H:i:s' );
		$recerverlist = $this->getReceiverList( $alertType );
		if( is_array( $recerverlist ) ){
			foreach ( $recerverlist as $type => $recerver ) {				
				switch ( $type ){
					case self::NOTICE_TYPE_MAIL:
						$rev = Watt_Util_Msg_Mail::sendMail( $recerver, Watt_I18n::trans("JT_OTHER_TONGBUJIANKONGFAXIAOXI"), $msg );
						Watt_Log::addLog( "Notice [$msg] to [$recerver] and rev [$rev]" );
						break;
					case self::MOTICE_TYPE_SMS:
						$rev = Watt_Util_Msg_Sms::sendSms( $recerver, $msg );
						Watt_Log::addLog( "Notice [$msg] to [$recerver] and rev [$rev]" );
						break;
				}
			}
		}else{
			Watt_Log::addLog( "Notice [$msg] to Nobody!" );
		}
	}
}
?>