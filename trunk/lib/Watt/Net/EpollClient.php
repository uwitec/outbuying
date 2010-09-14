<?
/**
 * Epoll Server
 *
 */
class Watt_Net_EpollClient{
	const  MAX_CS_PKG = 5120;			//最大消息长度 1024 * 5
	// 登录返回结果
	const  RES_LOGIN_SUCC = 0;			//登录成功
	const  RES_LOGIN_ERROR = -1;			//登录异常
	const  RES_LOGIN_RELOGIN = -2;		//被踢消息
	const  RES_LOGIN_BADPASSWD = -3;		//密码错误
	const  RES_LOGIN_TESTRELOGIN = -4;	//测试登录发现对方在线
	
	const  OPER_MSG = 1;					//客户端发起的操作类型
	
	// 操作对应的结果
	const  RES_FAILED = 0;				//操作失败
	const  RES_SUCC = 1;				//操作成功
	const  RES_MSG_FAILED = 2;			//消息发送失败
	const  RES_MSG_NOT_ONLINE = 3;	//用户未登录
	
	//登录动作选项
	const  LOGIN_ACTION_TEST = 1; //测试登录
	const  LOGIN_ACTION_NONE = 0; //强制登录
	
	// CS协议的消息ID
	const  CS_CMD_LOGIN = 1;		//登录消息
	const  CS_CMD_LOGOUT = 2;		//注销消息
	const  CS_CMD_MSG = 3;		//广播消息
	const  CS_CMD_MULTMSG = 4;	//多条消息
	const  CS_CMD_KICK = 5;		//
	const  CS_CMD_HEARTBIT = 6;	//心跳消息
	const  CS_CMD_ONLINELIST = 7;	//获取在线用户
	const  CS_CMD_RESULT = 8;		//服务器返回消息标记
	const  CS_CMD_TPM_MSG = 9;	//TPM消息
	
	const  MAX_USER_NAME = 32;	//用户名长度-已改用MAX_USER_NAME_EX
	const  MAX_USER_NAME_EX = 36; //用户名长度
	
	const  MAX_PASSWD_LEN = 32;	//密码长度-已改用MAX_PASSWD_LEN_EX
	const  MAX_PASSWD_LEN_EX = 36; //密码长度
	
	const  MAX_MESSAGE_LEN = 512;	//消息内容长度
	const  MAX_MESSAGE_LEN_EX = 516; //消息内容长度—未使用
	
	//TPM消息
	const  MAX_TPMMESSAGETITLE_LEN = 200;	//标题长度
	const  MAX_TPMMESSAGELINK_LEN = 200;	//链接长度
	const  MAX_MSNID_LEN = 100;	//MSN的ID长度
	const  MAX_QQID_LEN = 30;		//QQ的ID长度
	const  MAX_MSN_EXT_MSG_LEN = 500;	//QQ/MSN消息长度
	const  MAX_MSG_LEN = 512; //QQ/MSN消息长度
	//const  MAX_TPM_MESSAGE_LEN = MAX_TPMMESSAGETITLE_LEN + MAX_TPMMESSAGELINK_LEN + MAX_MSNID_LEN + MAX_QQID_LEN + MAX_MSN_EXT_MSG_LEN + MAX_MSG_LEN + 4 + 4 + 4; //TPM消息长度
	const  MAX_TPM_MESSAGE_LEN = 1542;
	
	const  MAX_GUID_LEN = 40;
	
	const  MAX_MSG_DEST = 5;
	const  MAX_MSG_DESTEX = 20;
	
	const  MAX_ONLINE_IN_ONE_PAGE = 32;
	
	const  MAX_MEMO_LEN = 32;
	const  MAX_MEMO_LEN_EX = 32;
	const  MAX_MEMO_LEN_EX1 = 100;

	
	/**
	 * @var Watt_Net_SocketClient
	 */
	private $_epollClient;
	
	function __construct( $host, $port ){
		try{
			$this->_epollClient = new Watt_Net_SocketClient( $host, $port );
			Watt_Debug::getDefaultDebug()->addInfo('Before epoll connect');
			$rev = $this->_epollClient->connect();
			Watt_Debug::getDefaultDebug()->addInfo('After epoll connect');
			Watt_Log::addLog( "Connected to epoll server $host:$port success.", Watt_Log::LEVEL_DEBUG );
		}catch (Exception $e){
			$this->_epollClient = null;
			Watt_Log::addLog( 'Connect epoll server error and catch exception, msg['.$e->getMessage().']', Watt_Log::LEVEL_ERROR );
		}
	}
	
	/**
	 * 发送 Tpm Message
	 * @author terry
	 * @version 0.1.0
	 * Thu Jun 12 15:40:54 CST 2008
	 *
	 * @param int $toEpollId
	 * @param Watt_Net_Epoll_TpmMsg $tpmMsg
	 * @return 1|0
	 */
	public function sendTpmMsg( $fromId, $toId, $msgId, $msgTitle, $msgLink, $msg, $msn, $qq, $extMsg, $flash ){
		$tpmMsg = new Watt_Net_Epoll_TpmMsg();
		$data = $tpmMsg->pack( $fromId, $toId, $msgId, $msgTitle, $msgLink, $msg, $msn, $qq, $extMsg, $flash );
		return $this->_sendMsgToEpoll( self::CS_CMD_TPM_MSG, $data );
	}
	
	public function getOnlineList( $epollId ){
		$onlineListC = new Watt_Net_Epoll_OnlineListC();
		$data = $onlineListC->pack( $epollId );
		$this->_sendMsgToEpoll( self::CS_CMD_ONLINELIST, $data );
		$rev = $this->_getMsgToEpoll();
		return $onlineListC->unpack( $rev );
	}
	
	/**
	 * 登录
	 *
	 * @param int $epollId
	 */
	public function login( $epollId ){
/*
  //// 用户登录
  TCSLoginC = packed record
    m_lUin: long;
    m_szUserName: array[0..MAX_USER_NAME_EX - 1] of char;    
    m_szPasswd: array[0..MAX_PASSWD_LEN_EX - 1] of char;
    m_iLastIP: int;
    m_lAction: long;
    m_szMemo: array[0..MAX_MEMO_LEN_EX1 - 1] of char;
  end;

m_lUin: 用户名（EPOLLID）
m_szUserName：TQ登录用户名
m_szPasswd：EPOLL密码（123）
m_iLastIP：登录客户端的IP地址
m_lAction：登录参数（）
  //登录动作选项
  LOGIN_ACTION_TEST = 1; //测试登录
  LOGIN_ACTION_NONE = 0; //无动作
m_szMemo：纪录登录的其他信息
*/
		//$packStr = "lc*c*ilc*";

		$m_lUin = $epollId;
		$m_szUserName = 'TpmSystem123';
		$m_szPasswd = '123';
		$m_iLastIP = 0x0a0a0a0a;//168430090;//0x04040404;
		$m_lAction = 1;
		$m_szMemo = 'memo';

		$packStr = "Na".self::MAX_USER_NAME_EX."a".self::MAX_PASSWD_LEN_EX."NNa".self::MAX_MEMO_LEN_EX1;		
		$toData = pack( $packStr, $m_lUin, strToWideStr($m_szUserName), strToWideStr($m_szPasswd), $m_iLastIP, $m_lAction, strToWideStr($m_szMemo) );
		$buf = $this->_sendMsgToEpoll( self::CS_CMD_LOGIN, $toData );
		$buf = $this->_getMsgToEpoll();
		/**
		 * @todo Unpack and 
		 * @author terry
		 * @version 0.1.0
		 * Fri Jun 27 08:49:34 CST 2008
		 */
//		echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Fri Jun 13 17:34:26 CST 2008] :\n ";
//		var_dump( strToHex($buf) );
//		var_dump( wideStrToStr($buf) );
//		echo "</pre>";
		//exit();
		
//		$rev = unpack( "nlen/ncmd/NepollId/a".self::MAX_USER_NAME_EX."username/cresult/Nip/a".self::MAX_MEMO_LEN_EX1."memo/", $buf );
//		echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Fri Jun 13 17:47:46 CST 2008] :\n ";
//		var_dump( $rev );
//		echo "</pre>";
		//exit();
		
	}
	
	private function _sendMsgToEpoll( $cmd, $data ){
		$rev = 0;
		if( $this->_epollClient ){
			try{
				/**
				 * 因为存在EpollServer能够连接上，但是无法发送信息的情况，所以设置了超时时间
				 * @author terry
				 * @version 0.1.0
				 * Sun Feb 01 11:38:08 CST 2009
				 */
				set_time_limit(10);
				$header = pack( 'nn', strlen( $data ), $cmd );
				$total = $header.$data;
				$this->_epollClient->write( $total, strlen( $total ) );
				//$rev = $this->_getMsgToEpoll();		
				$rev = 1;			
			}catch (Exception $e){
				Watt_Log::addLog( 'Send epoll msg error, msg['.$e->getMessage().']', Watt_Log::LEVEL_ERROR );
				$rev = 0;
			}
		}
		return $rev;
	}
	
	private function _getMsgToEpoll(){
		$buf = null;
		if( $this->_epollClient ){
			$buf = $this->_epollClient->read(2048);			
		}
		return $buf;
	}
	
	public static function test(){
		set_time_limit(10);

		$url=Watt_Util_Net::isLANIp( $_SERVER['SERVER_ADDR'] );
		$epollServer = Watt_Config::getEpollServer();
/*
		$epoll_url = Watt_Config::getEpollServer();
		
		if($url)//判断是否内网IP
		{
			if(preg_match("/\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}/",$epoll_url))//判断是否IP
			{
				$epollUpdateUrl = $epoll_url;
			}
			else //是否域名
			{
				$epollUpdateUrl = "in".$epoll_url;
			}
		}
		else 
		{
			$epollUpdateUrl = $epoll_url;
		}
		$epollServer = $epollUpdateUrl;
*/
		$port = Watt_Config::getEpollServerPort();
		$epollClient = new Watt_Net_EpollClient( $epollServer, $port );
		
		$fromId = 4040;
		$epollClient->login( $fromId );
		$epollClient->sendTpmMsg( $fromId, 151017933, 1, 'Title', 'Link', ('test消息') , 'msn', 'qq18076495', 'extmsg', 1 );
		
//		$list = $epollClient->getOnlineList( $fromId );
//		echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Fri Jun 13 19:46:17 CST 2008] :\n ";
//		var_dump( $list );
//		echo "</pre>";
		//exit();
//		echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Thu Jun 26 20:28:09 CST 2008] :\n ";
//		var_dump( strToHex( iconv('ISO-8859-1','UTF-8','消息' ) ) );
//		var_dump( ( urlencode('消息') ) );
//		var_dump( pack( "H*","886D6F60" ) );
//		echo "</pre>";
		//exit();
		
		Watt_Debug::getDefaultDebug()->addInfo('After login');
		
//		$docHeader = pack( 'H*', 'D0CF11E0A1B11AE1' );
//		$docHeader = pack( 'H*', 'FFFE' );
//		$total = pack('H*','00910001000003EA');
//		echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Fri Jun 13 17:13:46 CST 2008] :\n ";
//		var_dump( strToHex($total) );
//		echo "</pre>";
//		
//		$total = pack('nnN',0x0091,0x0001,0x000003EA);
//		echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Fri Jun 13 17:13:46 CST 2008] :\n ";
//		var_dump( strToHex($total) );
//		echo "</pre>";
//		
//		$rev = unpack('nlen/ncmd/Nepollid/', $total);
//		echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Fri Jun 13 14:24:03 CST 2008] :\n ";
//		var_dump( $rev );
//		echo "</pre>";
//		//exit();
//		
//		$total = pack( 'H*', '00910001000003EA50F1120058DBEEBFAAC1040880DBEEBF50DBEEBF68DBEEBFD8C1040884DBEEBFC8DCEE00000012F12800000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000' );
//		echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Fri Jun 13 17:13:46 CST 2008] :\n ";
//		var_dump( strToHex($total) );
//		echo "</pre>";
//		
//		$rev = unpack( "nlen/ncmd/Nepollid/a".self::MAX_USER_NAME_EX."user/a".self::MAX_PASSWD_LEN_EX."pwd/Nip/Naction/a*memo/",$total );
//		echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Fri Jun 13 14:24:03 CST 2008] :\n ";
//		var_dump( $rev );
//		echo "</pre>";
//		exit();
		/*
		0091
		0001
		000003EA
		50F1120058DBEEBFAAC1040880DBEEBF50DBEEBF68DBEEBFD8C1040884DBEEBF
		C8DCEE00000012F1280000000000000000000000000000000000000000000000
		00000000
		00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000'
		*/
	}
}

class Watt_Net_Epoll_CsMessage{
/*
  TCSMessage = packed record
    m_lFrom: long;
    m_nCount: short;  //此不存在
    m_lTo: array[0..MAX_MSG_DESTEX] of long;
    m_nMsgLen: short;
    m_szMsg: array[0..MAX_MESSAGE_LEN - 1] of char;
  end;
*/
	public function pack( $fromId, $toId, $msg ){
		$data = pack( "NNn", $fromId, $toId, strlen($msg) ).$msg;
		return $data;
	}
}

class Watt_Net_Epoll_TpmMsg extends Watt_Net_Epoll_CsMessage{
/*
  //TPM聊天消息
  TTPMMsg = packed record
    MsgID: Integer;
    MsgTitle: array[0..MAX_TPMMESSAGETITLE_LEN - 1] of char;
    MsgLink: array[0..MAX_TPMMESSAGELINK_LEN - 1] of char;
    m_szMsg: array[0..MAX_MSG_LEN - 1] of char;
    MSNID: array[0..MAX_MSNID_LEN -1] of char;
    QQID: array[0..MAX_QQID_LEN -1] of char;
    MSN_EXT_MSG: array[0..MAX_MSN_EXT_MSG_LEN -1] of char;
    MsgFlashFrm: Integer;
    FromEpollID: Integer;
  end;
*/
	private $_data = array();
	public function pack( $fromId, $toId, $msgId, $msgTitle, $msgLink, $msg, $msn, $qq, $extMsg, $flash ){
		$data = pack( "N"
					."a".Watt_Net_EpollClient::MAX_TPMMESSAGETITLE_LEN
					."a".Watt_Net_EpollClient::MAX_TPMMESSAGELINK_LEN
					//."a".Watt_Net_EpollClient::MAX_MSG_LEN
					."a".Watt_Net_EpollClient::MAX_MSG_LEN
					."a".Watt_Net_EpollClient::MAX_MSNID_LEN
					."a".Watt_Net_EpollClient::MAX_QQID_LEN
					."a".Watt_Net_EpollClient::MAX_MSN_EXT_MSG_LEN
					."N"
					."N"
				,$msgId
				,iconv( 'UTF-8', 'UCS-2', $msgTitle)
				,iconv( 'UTF-8', 'UCS-2', $msgLink)
				,iconv( 'UTF-8', 'UCS-2', $msg)
				,iconv( 'UTF-8', 'UCS-2', $msn)
				,iconv( 'UTF-8', 'UCS-2', $qq)
				,iconv( 'UTF-8', 'UCS-2', $extMsg)
				,$flash
				,$fromId
		);
		return parent::pack( $fromId, $toId, $data );
	}
	
	public function unpack( $data ){
		$rev = unpack( "", $data);
		return $rev;
	}
}

class Watt_Net_Epoll_LoginC{

}

class Watt_Net_Epoll_LoginS{
/*
  //// 用户登录回应
  TCSLoginS = packed record
    m_lUin: long;
    m_szUserName: array[0..MAX_USER_NAME_EX - 1] of char;    
    m_cResult: char;
    m_iIP: int;
    m_szMemo: array[0..MAX_MEMO_LEN_EX1 - 1] of char;
  end;
*/
}

class Watt_Net_Epoll_OnlineListC{
	function pack( $fromId ){
		return pack( "N", $fromId );
	}
	
	function unpack( $data ){
		return unpack( "NTotal/NPageNo/a*info/", $data );
	}
}

function strToHex( $str ){
	$outStr = '';
	$len = strlen( $str );
	for( $i=0;$i<$len;$i++ ){
		$outStr .= sprintf( '%02s', dechex( ord( substr( $str, $i, 1 ) ) ) );
	}
	return $outStr;
}

function strToWideStr( $str, $charset="UTF-8" ){
	return iconv( $charset, 'UCS-2', $str );
//	$hex = strToHex( $str );
//	for( $i=0;$i<strlen($hex);$i++ ){
//		
//	}
//	$wideHex = '';
}

function wideStrToStr( $str, $charset="UTF-8" ){
	return iconv( 'UCS-2', $charset, $str );
//	$hex = strToHex( $str );
//	for( $i=0;$i<strlen($hex);$i++ ){
//		
//	}
//	$wideHex = '';
}
?>