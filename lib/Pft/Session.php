<?
/**
 * 用户会话管理
 * session 里可记录的信息显式定义。
 * 如自己直接使用 $_SESSION 获取信息，不保证信息的正确性
 * 
 * 需要在此类实例中保存对象，请使用 $_obj_对象名 的规则，以确保正确的序列化。如：
 * private $_obj_real_user;
 *
 * @version 1.0.1
 * @author Terry
 * @package Pft
 */
/**
 * 增加获取部门ID
 * @author terry
 * Wed Jun 06 14:27:31 CST 2007
 */
class Pft_Session{
	const SESSION_KEY_PREFIX = "SESS_";
	
	private static $_ses = null;

	private $_obj_real_user;
	private $_userId;
	private $_userName;
	private $_roleId;
	private $_roleName;
	private $_roleShortname;
	private $_roleCount = 0;
	private $_language;				//Pft_I18n::I18N_LANGUAGE_*
	private $_lastVisitPage;		//记录的最后访问页
	private $_lastRefererPage;		//记录的最后的引用页
	private $_groupId  = null;
	private $_departmentId = 0;		//部门ID
	private $_subDepartmentIds = array();
	private $_isTq;
	private $_tqVersion;
	private $_sessionData = array(); //其他session数据

	private $_mobilePhone;		//自己的手机
	private $_eMail;			//自己的Email
	
	private $_preProcessOrderChecker = false;
	private $_lastestCheckTimestamp  = 0;
	
	private $_userAutoId;
	
	private $_oldUserId;	//原始用户ID，用于保存用户上岗后的处理

	public function setOldUserId( $v ){$this->_oldUserId = $v;}
	public function getOldUserId(){return $this->_oldUserId;}	
	
	//public function setUserAutoId( $v ){$this->_userAutoId = $v;}
	public function getUserAutoId(){return $this->_userAutoId;}
	
	public function setPreProcessOrderChecker( $v ){
		$this->_preProcessOrderChecker = $v;
		if( $v ){
			//这是为了登录后就开始检查一遍
			$this->_lastestCheckTimestamp = 0;			
		}
	}
	public function getPreProcessOrderChecker(){return $this->_preProcessOrderChecker;}
	public function checkedPreProcessOrder(){ $this->_lastestCheckTimestamp = time();}
	public function getLastestCheckTimestamp(){return $this->_lastestCheckTimestamp;}
	//public function check
	
	public function setEMail( $v ){$this->_eMail = $v;}
	public function getEMail(){return $this->_eMail;}
	
	public function setMobilePhone( $v ){$this->_mobilePhone = $v;}
	public function getMobilePhone(){return $this->_mobilePhone;}
	
	public function setTqVersion( $v ){
		$this->_tqVersion = $v;
	}
	/**
	 * @return string
	 */
	public function getTqVersion(){
		return $this->_tqVersion;
	}
	
	/**
	 *  1 | >0  current version is greater than $toCompareVersion
	 *  0 =     current version is equare to $toCompareVersion
	 * -1 | <0  current version is less than $toCompareVersion
	 * 
	 * @return >0 | 0 | <0
	 * @author terry
	 * Sat Jun 23 12:52:34 CST 2007
	 */
	public function compareCurrentVersionToTqVersion( $toCompareVersion ){
		if( $this->getTqVersion() == $toCompareVersion ){
			return 0;
		}else{
			if( !$this->getTqVersion() ){
				//如果没有注册TqVersion， currentVersion < 任何有值的 version $toCompareVersion
				return -1;
			}else{
				$toVersionArr      = explode( '.', $toCompareVersion );
				$currentVersionArr = explode( '.', $this->getTqVersion() );
				for ( $i = 0; $i < count( $toVersionArr ); $i++ ){
					if( $currentVersionArr[$i] != $toVersionArr[$i] ){
						return intval( $currentVersionArr[$i] ) - intval( $toVersionArr[$i] ) ;
					}
				}
				return 0;	//依次比较完成，说明相等
			}
		}
	}
	
	/**
	 * 获取session对象
	 *
	 * @return Pft_Session
	 */
	public static final function getSession()
	{
		if( !self::$_ses )
		{
			self::$_ses = new Pft_Session();				
		}
		return self::$_ses;
	}
	
	function __construct()
	{
		$this->_loadSessionInfo();
		$lang = r('lang');
		if( $lang ){
			$this->setLanguage( $lang );
		}
	}
	
	function __destruct()
	{
		$this->_saveSessionInfo();
	}
	
	/**
	 * 获得用户Id
	 *
	 * @return int
	 */
	public function getUserId()
	{
		//return isset( $_SESSION["userId"] )?$_SESSION["userId"]:null;
		return $this->_userId;
	}
	
	/**
	 * 获得部门ID
	 *
	 * @return int
	 */
	public function getDepartmentId(){
		return $this->_departmentId;
	}
	
	/**
	 * 获取Session用户对应的用户对象
	 * @return TpmYonghu
	 */
	public function getUserObj(){
		return TpmYonghuPeer::retrieveByPK( $this->_userId );
	}
	
	/**
	 * 获取当前Session的组ID
	 *
	 */
	public function getGroupId()
	{
		//return "189ce619-fe31-802c-369a-45b450b81a5b";
		$rev = $this->_groupId;
		if( !$rev ){
			$rev = r('zu_id');							//如果当前会话没有组ID，使用 URL 指定的组ID
			if( !$rev ){
				$rev = Pft_Config::getDefaultZuId();	//如果 URL 也没有组ID，使用默认组ID
			}
			$this->_groupId = $rev;						//将当前会话用户归入之前选定的组
		}
		return $rev;
	}

	private $_groupIdStack = array();		//进入组的ID堆栈

	/**
	 * 临时进入其他组，这样会使当前会话进入其他组..有些危险的行为
	 *
	 * @param string $groupId
	 */
	public function enterToOtherGroup( $groupId ){
		array_push( $this->_groupIdStack, $this->_groupId );
		$this->_groupId = $groupId;
		Pft_Log::addLog( 'Enter group ['.$groupId.']' );
	}
	
	/**
	 * 回到之前所在组
	 * 如果组列表只有一个元素，则保持在该组
	 * @param int $backStep 回退步数，如果为0，退到初始状态
	 */
	public function backToPrevGroup( $backStep = 1 ){
		if( $backStep > 0 ){
			for( ; $backStep > 0; $backStep-- ){
				$prevGroupId = array_pop( $this->_groupIdStack );
				if( $prevGroupId ){
					$this->_groupId = $prevGroupId;
					Pft_Log::addLog( 'Back to group ['.$prevGroupId.']' );
				}			
			}			
		}else{
			//如果 步数 <= 0 回退到最起点
			$prevGroupId = array_pop( $this->_groupIdStack );
			while( $prevGroupId ){
				if( $prevGroupId ){
					$this->_groupId = $prevGroupId;
				}
				$prevGroupId = array_pop( $this->_groupIdStack );
			}
		}
	}
	
	/**
	 * 获得用户帐户名
	 *
	 * @return string
	 */
	public function getUserName()
	{
		//return isset( $_SESSION["userName"] )?$_SESSION["userName"]:null;
		$username = $this->_userName;
		
		if ( ($this->_obj_real_user) && ($this->_obj_real_user->getYhZhanghu() != $this->_userName) )	// 当存在岗位时将岗位的用户名和真实用户的用户名拼接返回
			$username .= '→' . $this->_obj_real_user->getYhZhanghu();
			
		return $username;
	}
	
	/**
	 * 获得用户角色id
	 */
	public function getRoleId()
	{
		return $this->_roleId;
	}

	/**
	 * 获得用户角色名称
	 */
	public function getRoleName()
	{
		return $this->_roleName;
	}

	/**
	 * 返回角色英文简称 如 CR
	 *
	 * @return string
	 */
	public function getRoleShortname()
	{
		return $this->_roleShortname;
	}
	
	public function getRoleCount()
	{
		return $this->_roleCount;
	}
	
	/**
	 * @return array
	 */
	public function getSubDepartmentIds(){
		return $this->_subDepartmentIds;
	}
	
	/**
	 * 设置实际用户（当将岗位设置到用户中后，缺少对真实用户的信息保存，在此作为补充）
	 *
	 * @param TpmYonghu $user
	 * @param GUID $roleid
	 */
	public function setRealUser( $user, $roleid="" )
	{
		$this->_obj_real_user = $user;
	}
	
	/**
	 * 获取实际用户
	 *
	 * @return TpmYonghu
	 */
	public function getRealUser()
	{
		return $this->_obj_real_user;
	}
	
	/**
	 * 设置用户信息
	 * 考虑用一个用户对象最为参数
	 * 
	 * @param int $userId
	 * @param string $userName
	 */
	/**
	 * 设置用户信息
	 *
	 * @param TpmYonghu $user
	 */
	//public function setUser( $userId, $userName )
	public function setUser( $user, $roleid = "" )
	{
		$this->_obj_real_user = $user;	// 每次setUser时都将将用户对象设置到 real_user 中，因此如果存在岗位时需要在 setUser 之后再次设置 real_user
		$this->_userId   = $user->getYhId();
		$this->_userName = $user->getYhZhanghu();
		$this->_groupId  = $user->getZuId();
		$this->_userAutoId = $user->getYhAutoId();
		$this->_departmentId = $user->getBmId();
		$this->_subDepartmentIds = TpmBumenPeer::getSubDepartmentIdsByBmId( $this->_departmentId );
		
		//print"<pre>Terry :";var_dump( $user );print"</pre>";
		//exit();
		
		$this->setEMail( $user->getYhYouxiang() );
		$this->setMobilePhone( TpmYonghuPeer::getYhShoujiByYhId( $this->_userId ) );
		
		$juese_rels = $user->getTpmYonghu2juesesJoinTpmJuese();
		$to_sel_id = "";
		if( $juese_rels && count( $juese_rels ) )
		{
			$this->_roleCount = count( $juese_rels );
			//$juese = new TpmJuese();			
			
			// 选择角色 如果存在首要角色，则使用首要角色，否则使用第一个角色 jute 20070813			
			$shouyao_juese = false;
			foreach ($juese_rels as $key =>$val){
				if ($val->getShifouShouyao() == 'y'){
					$shouyao_juese = $val;
				}
			}		
			reset($juese_rels);//将数组的内部指针指向第一个单元,为了正确使用current函数 jute 20071106
			if($shouyao_juese ){
				$juese = $shouyao_juese->getTpmJuese();
			}else {					
				/**
				 * 默认使用第一个角色
				 */
				$shouyao_juese = current( $juese_rels );			
				
//				$shouyao_juese = current( $juese_rels );
				$juese = $shouyao_juese?$shouyao_juese->getTpmJuese():null;
			}
			// 选择角色结束
			
			if( $juese ){
				$this->_roleName = $juese->getJsMingcheng();
				$this->_roleShortname = $juese->getJsJiancheng();
				$to_sel_id       = $juese->getJsId();
	
				if( $roleid != "" )
				{
					foreach ( $juese_rels as $juese_rel )
					{
						if( $roleid	== $juese_rel->getTpmJuese()->getJsId() )
						{
							$to_sel_id = $roleid;
							$this->_roleName = $juese_rel->getTpmJuese()->getJsMingcheng();	
							$this->_roleShortname = $juese_rel->getTpmJuese()->getJsJiancheng();
							break;
						}
					}				
				}
			}
		}
		$this->_roleId = $to_sel_id;
		
		/**
		 * 超时订单检测
		 * select yh_id from tpm_yonghuzhaoquanxian
		 * where qx_id = '55df2b32-88c3-9367-d3ba-45fb6dd80782'
		 */
		$chaoshidingdan_qx_id = '55df2b32-88c3-9367-d3ba-45fb6dd80782';
		if( TpmJuesePeer::existJueseQuanxian( $this->_roleId, $chaoshidingdan_qx_id ) ){
			$this->setPreProcessOrderChecker( true );
		}
	}
	
	/**
	 * 获得会话的语言
	 *
	 * @return Pft_I18n::I18N_LANGUAGE_*
	 */
	public function getLanguage()
	{
		return $this->_language?$this->_language:Pft_I18n::I18N_LANGUAGE_DEFAULT;
	}
	
	public function getLanguageTqCode(){
		$arrLangCode2TqLang = array(
			'cn_jt' => 2052,
			'en' => 1033
		);
		if( key_exists( $this->_language, $arrLangCode2TqLang ) ){
			return $arrLangCode2TqLang[$this->_language];	
		}else{
			return 2052;
		}
	}
	
	/**
	 * 设置会话的语言
	 *
	 * @param Pft_I18n::I18N_LANGUAGE_* $langCode
	 */
	public function setLanguage( $langCode )
	{
		if( intval( $langCode ) > 0 ){
			$tqLang2LangCode = array(
				2052 => 'cn_jt',
				1033 => 'en'
			);
			if( key_exists( $langCode, $tqLang2LangCode ) ){
				$this->_language = $tqLang2LangCode[$langCode];	
			}else{
				$this->_language = 'cn_jt';
			}
		}else{
			$this->_language = $langCode;
		}
	}
	
	/**
	 * 设置是否是 tq 
	 *
	 * @param boolean $isTq
	 */
	public function setIsTq( $isTq )
	{
		$this->_isTq = $isTq;
	}
	
	/**
	 * 是否使用TQ登录
	 * @return boolean
	 */
	public function isTq()
	{
		return $this->_isTq;
	}
	
	/**
	 * 记录最近访问页
	 *
	 */
	public function recordCurrentVisitPage()
	{
		//$_SESSION["lastVisitPage"] = $_SERVER["REQUEST_URI"];
		$this->_lastVisitPage = $_SERVER["REQUEST_URI"];
	}

	public function recordRefererPage()
	{
		$this->_lastRefererPage = @$_SERVER["HTTP_REFERER"];
	}
	
	/**
	 * 获得最近的引用页
	 *
	 * @return string
	 */
	public function getLastRefererPage(){
		return $this->_lastRefererPage;
	}
	
	/**
	 * 转到最近访问页
	 * 转向后会exit
	 * 不会return
	 *
	 * @return false
	 */
	public function restoreLastVisitPage()
	{
		//if( isset( $_SESSION["lastVisitPage"] ) && trim( $_SESSION["lastVisitPage"] ) != "" )
		if( trim( $this->_lastVisitPage ) != "" )
		{
//			$to_uri = $_SESSION["lastVisitPage"];
//			unset( $_SESSION["lastVisitPage"] );

			$to_uri = $this->_lastVisitPage;
			$this->_lastVisitPage = null;
			header( "Location:".$to_uri );
			exit();
		}
		return false;
	}

	/**
	 * 活动一下，记录 session 的活动信息
	 *
	 */
	public function active()
	{
		//这里考虑用数据库存储
	}
	
	/**
	 * 清除用户会话信息
	 * 仅是清除与用户会话相关的信息，而不销毁Session
	 * 
	 */
	public function clearUserSessionInfo()
	{
		// lastVisitPage 不能unset
//		
//		$this->_userId = null;
//		$this->_userName = null;
//		$this->_roleId = null;
//		$this->_roleName = null;
		
		$lastVisitPage = $this->_lastVisitPage;
		$arrVars = get_class_vars(get_class($this));
		foreach ( $arrVars as $key => $val )
		{
			$this->$key = $val;
		}
		$this->_lastVisitPage = $lastVisitPage;

		//清除存在内存中的tq消息
		Tpm_Message_Sender_Tq::clearTqMsg();
	}
	
	public function setData( $key, $data )
	{
		$this->_sessionData[$key] = $data;
	}
	
	public function getData( $key )
	{
		if( key_exists( $key , $this->_sessionData ) )
		{
			return $this->_sessionData[$key];
		}
		else
		{
			return null;
		}
	}
	
	public function delData( $key )
	{
		if( key_exists( $key , $this->_sessionData ) )
		{
			unset($this->_sessionData[$key]);
		}
	}
	
	/**
	 * 销毁会话信息
	 *
	 */
	public function destory()
	{
		session_destroy();
	}
	
	private function _loadSessionInfo()
	{
/*这里考虑用复制信息的方式从 session 中读取数据
		if( isset( $_SESSION["theSession"] )
		 && is_object( $_SESSION["theSession"] )
		 && is_a( $_SESSION["theSession"], "Pft_Session" ) )
		{
			$this = $_SESSION["theSession"];
		}*/
		if( !isset($_SESSION) )session_start();
		$arrVars = get_object_vars($this);
		foreach ( $arrVars as $key => $val )
		{
			if( key_exists( self::SESSION_KEY_PREFIX.$key, $_SESSION ) )
			{
				if ( ereg('^_obj_', $key) )
					$this->$key = unserialize($_SESSION[self::SESSION_KEY_PREFIX.$key]);
				else
					$this->$key = $_SESSION[self::SESSION_KEY_PREFIX.$key];
			}
		}
	}

	private function _saveSessionInfo()
	{
		$arrVars = get_object_vars($this);
		foreach ( $arrVars as $key => $val )
		{
			if ( ereg('^_obj_', $key) )
				$_SESSION[self::SESSION_KEY_PREFIX.$key] = serialize($val);
			else
				$_SESSION[self::SESSION_KEY_PREFIX.$key] = $val;
		}
	}
}