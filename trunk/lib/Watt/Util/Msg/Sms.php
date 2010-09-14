<?
/*
现役短信发送接口

短信提供商接口测试方法：（用来测试短信接口提供商是否可用）
http://www.ensms.com/fuction/eeqpost.asp?tmobile=13683078625&msgid=888&msg=helloworld&mobile=wattcan&pwd=h2175&action=sendmsg&source=00
http://www.ensms.com/fuction/eeqpost.asp?tmobile=13683078625&msgid=361&msg=hello&mobile=wattcan&pwd=h2175&action=sendmsg&source=00
  	tmobile   => 	目标手机号		
 	msgid	  =>	任意数字	
 	msg  	  =>	短信内容	
  	&mobile=wattcan&pwd=h2175&action=sendmsg＝>系统设置，必须写		
  	source	  =>	短信回复号码后面跟着的数字

短信服务测试方法：（用来测试公司内部服务器短信服务是否可用）
http://devsms.wattcan.net/message/sms/send.php?to=13366588665&source=00&msg=Hello World
	to	=>	接收者手机号
	source	=>	短信回复号码后面跟着的数字（一般为00）
	msg	=>	短信内容
 */

/**
 * 短消息发送工具
 *
 */
class Watt_Util_Msg_Sms{
	//private static $_sms_url_inner	= 'http://sms.wattcan.net/message/sms/send.php?';
	//private static $_sms_url 		= "http://www.ensms.com/fuction/eeqpost.asp?";	
	//private static $_sms_url 		= "http://192.168.0.18/~terry/smscenter/index.php?do=index_send&";
	
	/**
	 * 此站点对应的CODE
	 * @var string
	 */
	private static $_site_code;	
	private static $_sys_code_tpm = '1';	//TPM对应1
	
	/**
	 * 发送短消息
	 *
	 * 前端增加站点代号 = EpollGroupId
	 * 前端增加系统代号 = 1 (TPM = 1)
	 * 
	 * 要求： $from_mobile 是已经由各业务单元增加了业务代码的号码
	 * 
	 * @param string $to
	 * @param string[UTF-8] $msg
	 * @param string $from_mobile
	 * @param int $msgid
	 * @return string[UTF-8]
	 */
	public static function sendSms($to, $msg, $from_mobile, $msgid=0,$tag=''){
		/*
		$to = '13683078625';
		$source	= '00';
		$msg = 'test';
		$string = "&to={$to}&source={$source}&msg={$msg}";
		*/
		self::_init();
		
		$msgid		=	mt_rand( 1, 999 );
		$toMsg		=	iconv('UTF-8', 'GB2312', $msg);
		/**
		 * 发送出的短信，将本地号码加.
		 * 前端增加站点代号 = EpollGroupId
		 * 前端增加系统代号 = 1 (TPM = 1)
		 */
		$source		=	self::addSystemCode( $from_mobile );//($from_mobile?$from_mobile:'00');

		//$other		=	'&mobile=wattcan&pwd=h2175&action=sendmsg';
		$other = ''; //action 与 Watt 框架冲突
		$string     =	"tmobile={$to}&msgid={$msgid}&msg={$toMsg}{$other}&source={$source}&tag={$tag}";

		//发送到短信提供商
		$sms_url = Watt_Config::getCfg( 'SMS_CENTER' );
		
		$data = Watt_Http_Client::curlPost( $sms_url, $string );

		Watt_Log::addLog( "Send Sms [$msg] From [$from_mobile] To [$to] Ok ( Source Code[{$source}] URL[ ".$sms_url.$string." ] REV[$data] ).", Watt_Log::LEVEL_INFO, 'MSG_SMS' );

		return iconv('GB2312', 'UTF-8', $data);
	}
	
	/**
	 * 增加系统代号
	 *
	 * @param string $fromMobile
	 */
	public static function addSystemCode( $from_mobile ){
		self::_init();
		return self::$_sys_code_tpm.self::$_site_code.$from_mobile;
	}
	
	/**
	 * 移除系统代号
	 *
	 * @param string $from_mobile
	 * @return string
	 */
	public static function removeSystemCode( $from_mobile ){
		self::_init();
		$systemCode = self::$_sys_code_tpm.self::$_site_code;
		return preg_replace( '/^'.$systemCode.'/i', '', $from_mobile );
	}
	
	/**
	 * 初始化
	 */
	private static function _init(){
		if( !self::$_site_code ){
			self::$_site_code = sprintf( "%02d", Watt_Config::getEpollGroupId());
		}
	}
	
	/**
	 * dispatch mobile
	 * 如果分发成功，则返回 true
	 * 否则返回 false
	 * @param string $source	去掉特服号的source
	 * @param string $_old_source	最原始的source
	 * @return boolean
	 */
	public static function dispatchMobile( $mobile, $msg, $source, $_old_source ){
		self::_init();
		
		$arrSiteList = array(
			10 => 'testtpm.transn.net',
			20 => 'demotpm.transn.net',
			40 => 'tpm.transn.net',
			50 => 'uetpm.transn.net',	
			60 => 'vtpm.transn.net',	
			70 => 'devtpm.transn.net',
		);
		
		$smsInterface = '/message/sms/get.php';
		
		$siteCode = substr( $source, 1, 2 );
		if( $siteCode == self::$_site_code ){	//如果刚好是本站地址
			return false;						//则不分发，return false
		}
		if( key_exists( $siteCode, $arrSiteList ) ){	//如果存在于站点列表中
			Watt_Log::addLog( "Recieve sms: mobile[{$mobile}],msg[{$msg}],source[{$source}]. And dispatch to [{$arrSiteList[$siteCode]}]( sitecode[$siteCode] ).", Watt_Log::LEVEL_INFO, 'MSG_SMS' );
			$url = "http://".$arrSiteList[$siteCode].$smsInterface."?mobile=$mobile&msg=$msg&source=$_old_source";
			file_get_contents($url);
			//print"<pre>Terry :";var_dump( file_get_contents($url)  );print"</pre>";
			//exit(); 
			return true;	//分发成功，不管结果
		}else{
			return false;	//否则依然在本站内处理
		}
	}
}