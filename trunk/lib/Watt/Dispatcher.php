<?
/**
 * 整个架构的调度器
 * 解析程序入口的 do 
 * 调用 controller 的 action
 * 创建 view
 * 渲染 view
 *
 * @author Terry
 * @package Watt
 */

class Watt_Dispatcher
{
	protected $_defaultControllerName = "index";
	protected $_defaultActionName     = "index";
	protected $_defaultActionPostfix  = "Action";

	/**
	 * 用来存储渲染的 data
	 *
	 * @var array
	 */
	protected $_data;

	/**
	 * 用来保存所有的渲染后的输出结果
	 *
	 * @var string
	 */
	protected $_show;

	/**
	 * 最大可执行的Do
	 *
	 * @var int
	 */
	protected $_maxToDo = 100;

	/**
	 * 循环进行 do 的处理
	 * 
	 */
	function dispatch(){
		
		/**
		 * 因为Tq是用Post传递参数过来的，所以不能用$_GET
		 */
		$do = empty($_REQUEST["do"])?"index":trim($_REQUEST["do"]);
		
		/**
		 * 这是为了兼容 do=xxx&action=yyy 的形式
		 */
		$a  = empty($_REQUEST["action"])?"":trim($_REQUEST["action"]);
		if( $a )$do .= "_".$a;
		
		/**
		 * 获取view的type
		 */
		$v  = empty($_REQUEST["v"])?"Html":trim($_REQUEST["v"]);

		//TQ任务LINK
		//http://testtpm.transn.net/index.php?do=if_renwu_detail&sj_id=35c55571-80bb-c18b-6078-465a87c329bd&Username=dGVzdC1wcjE=&Password=MjAyY2I5NjJhYzU5MDc1Yjk2NGIwNzE1MmQyMzRiNzA=&pwdway=md5
		
		/**
		 * 获取用户名和密码进行快速登录
		 */
		$accounts = '';
		$pwd = '';
		if( r('pwdway') ){
			if( !Watt_Session::getSession()->getUserId() ){
				//这个判断是为了不让TQ登录后，访问此链接时，冲掉session中[是否TQ]那个设置
				
				$accounts = iconv( 'GB2312', 'UTF-8', base64_decode( str_replace( ' ', '+', r( "Username" ) ) ) );
				$pwd = iconv( 'GB2312', 'UTF-8', base64_decode( str_replace( ' ', '+', r( "Password" ) ) ) );
			}
		}else{
			//if( r( 'login' ) == 'ok' ){
			if( r( 'login' ) ){
				/**
				 * 这是为了兼容TQ的那个点击“查收我的订单”，导致Web重登录的问题。
				 * @author terry
				 * @version 0.1.0
				 * Thu Sep 06 16:44:53 CST 2007
				 */
				$accounts = '';
				$pwd = '';
			}else{
//				if(r('yh_xiaoshou_id')){
//					$wkh_id = r( "yh_waibukehu_id" );
//					$yh_xiaoshou_id = r( "yh_xiaoshou_id" );
//					$yh_xiaoshou_name = TpmYonghuPeer::getYhZhanghuByYhId($yh_xiaoshou_id);
//					$nkh_id = TpmKehufromkehuPeer::getNkIdByWkId($wkh_id,$yh_xiaoshou_name);
//					$accounts =  TpmYonghuPeer::getYhZhanghuByYhId($nkh_id) ;
//					$pwd = r( "user_pw" );
//					if($pwd==''){
//						$pwd = r('yh_xiaoshou_id');
//					}
//				}else{
					$accounts = r( "user_name" );
					$pwd = r( "user_pw" );				
				//}
			}
		}
		//var_dump();
		//exit;	
		
		if( $accounts && $pwd ){
			$login_rev = 0;
			$user = TpmYonghuPeer::checkUserLogin( $accounts, $pwd, $login_rev );
			
			if( $login_rev == TpmYonghuPeer::USER_LOGIN_OK ){
				
				// 用户登陆成功后如果密码安全强度不够，跳转到修改密码页提示用户设置安全的密码
				$pswdChecker = new Tpm_Passwordchecker( $pwd );
				$cfgLevel = Watt_Config::getCfg('PSWD_CHECK_LEVEL');
				if ( $cfgLevel=='' )
					$cfgLevel = '0';
					
				if ($pswdChecker->getSecurityLevel() < $cfgLevel )
				{
					$_SESSION['LOW_PASSWORD']  = true;
//					header('Location:?do=ps_yonghu_changepwd&nosecurity=true');
//					exit;
				}
				
				Watt_Log::addLog( 'Login ok, accounts['.$accounts.'],['.session_name().'='.session_id().']', Watt_Log::LEVEL_INFO, 'LOGIN_WEB_DIRECT_LOGIN' );		
			}else if($login_rev == TpmYonghuPeer::USER_LOGIN_SHOUQUANOK){//授权密码登录 jute 20071220
				Watt_Log::addLog( 'Authorizepwd Login ok, accounts['.$accounts.']', Watt_Log::LEVEL_INFO, 'LOGIN_WEB_DIRECT_LOGIN' );
			}
			
		}
		
		//如密码强度不够， 强制修改密码
		$superDoList = array('ps_yonghu_changepwd','login_logout', ''); // 数组中的Action不在强制之列
		if( @$_SESSION['LOW_PASSWORD']  && !in_array($do, $superDoList) ){
			header('Location:?do=ps_yonghu_changepwd&nosecurity=true');
			exit;
		}
		
		/**
		 * 除了译员和客户，只能从内部登录
		 * @author terry
		 * @version 0.1.0
		 * Mon Mar 31 23:24:00 CST 2008
		 */
		if( Watt_Session::getSession()->getUserName()){
			if( !Watt_Util_Net::isLANIp( $_SERVER['REMOTE_ADDR'] ) && r('do') != 'main_home'){
				if( !( Watt_Session::getSession()->getYhShifouWaibuDenglu() || Watt_Session::getSession()->getJsShifouWaibuDenglu() ) ){
					echo '您没有外部访问权限，请联系企业管理员开通';
					Watt_Session::getSession()->clearUserSessionInfo();
					exit();
				}
			}
		}

		$i = $this->_maxToDo;
		while ( $do != "" && $i-- > 0 ){
			$do = $this->processDo( $do, $v );
		}
		
		//如果是渠道代理商客户,传神客户,客户则记录日志 2007-7-9 john
		if(Watt_Session::getSession()->getRoleShortname()=="QDKH"||Watt_Session::getSession()->getRoleShortname()=="CSKH"||Watt_Session::getSession()->getRoleShortname()=="CR")
		{
		$accessLoger = new Watt_Log_Db( 'tpm_rizhi_fangwen' );
		$accessLoger->log("",0,$_REQUEST["do"]);
		}
	}

	/**
	 * 处理 do
	 * 有可能会返回 goToDo
	 * 
	 * 只有一个单词的do，对应的action是 index
	 * 
	 *
	 * @param string $do
	 * @return string $goToDo
	 */
	protected function processDo( $do, $defaultView = "Html" )
	{
		if( $do == "" ){
			$e = new Watt_Exception(Watt_I18n::trans("ERR_DISPATCH_NODO"));
			throw $e;
		}
		$goToDo = "";

		$arrCtrlAndAction = $this->_analyzeDoToControllerAndAction( $do );
		$controller = $arrCtrlAndAction[0];
		$action = $arrCtrlAndAction[1];
		$doFile = $arrCtrlAndAction[2];
		$doAction = $arrCtrlAndAction[3];
		//exit( $doFile . "|" . $doAction );

		//使用 ob_start 是为了Controller里的 redirect 可以正常使用
		if( defined( 'ENABLE_CTRL_BUFFER' ) && ENABLE_CTRL_BUFFER ){
			/**
			 * 为了不让服务器过长等待时间，改为不启用Ctrl Buffer
			 * @author terry
			 * @version 0.1.0
			 * Mon Jan 14 14:41:39 CST 2008
			 */
			ob_start();			
		}
		
		/**
		 * 增加了对页面缓存的支持
		 */
		$cache = null;
		Watt_Debug::addInfoToDefault('Begin create action ['.$controller.'] ['.$action.']');
		$theCtrl = Watt_Controller_Action::factory( $controller, $action );
		Watt_Debug::addInfoToDefault('After create action');
		
		$viewMenu = isset($_REQUEST["view_menu"])?((trim($_REQUEST["view_menu"])=='0')?false:true):true;		
		$theCtrl->setNeedMenu($viewMenu);	
		$actionCacheTime = $theCtrl->getActionCacheTime( $action );
		if( $actionCacheTime > 0 ){
			$cache=new Watt_Cache($actionCacheTime);
			if( $cache->cacheCheck() ){
				//如果符合缓存条件，则会读取缓存文件，并 exit.
				/**
				 * 改为退出处理，为了记录页面执行时间。
				 * 这里一定不能 return true.
				 * @author terry
				 * @version 0.1.0
				 * Mon Jan 14 14:30:43 CST 2008
				 */
				return '';
			}
		}
		
		/**
		 * 检查 会话的权限。 
		 * 如果没有权限，抛出一个异常
		 * 此处别扭
		 */
		$rbac = new Watt_Rbac();
		
		//$rbac->checkSession(Watt_Session::getSession(), $do);
		$privilege = $rbac->checkActionPrivilege( Watt_Session::getSession(), $theCtrl, $action );
		if( is_object ( $privilege ) && $privilege instanceof TpmYonghuzhaoquanxian ){
			if( !$theCtrl->getTitle() )$theCtrl->setTitle( Watt_I18n::trans( $privilege->getQxMingcheng() ) );
		}
		
		Watt_Debug::addInfoToDefault( '', 'Pre do action..' );
		
		if( method_exists( $theCtrl, $doAction ) ){
			//执行controller中的action
			$theCtrl->$doAction();
		}else{
			throw (new Exception(Watt_I18n::trans( "ERR_APP_LOST_ACTION" )));
		}
		
		Watt_Debug::addInfoToDefault( '', 'After do action..' );
		
		$goToDo = $theCtrl->getGoToDo();
		$data = $theCtrl->getData();
		
		/**
		 * 改为对 Ctrl 不进行 Buffer 的处理
		 * @author terry
		 * @version 0.1.0
		 * Mon Jan 14 15:05:28 CST 2008
		 */
		if( defined( 'ENABLE_CTRL_BUFFER' ) && ENABLE_CTRL_BUFFER ){
			if( defined("DEBUG") && DEBUG )
			{
				//调试阶段才显示Controller里输出的信息
				echo ob_get_clean();
			}
			else
			{
				//用户使用阶段不允许 action 里输出显示数据
				ob_clean();
			}
		}

		if( $theCtrl->isNeedView() )
		{
			
			Watt_Debug::addInfoToDefault( '', 'Pre load view..' );
			
			if( $theCtrl->getViewType() ){
				$defaultView = $theCtrl->getViewType();
			}
			
			/**
			 * 创建一个View。将来可以用不同的View代替此View
			 */
			//$view = Watt_View::factory( "Html", Watt_Config::getViewPath() );
			$view = Watt_View::factory( $defaultView, Watt_Config::getViewPath() );
			$view->setHeader( $theCtrl->getHeader() );

			Watt_Debug::addInfoToDefault( '', 'After view factory..' );

			/**
			 * 读取菜单应该由View来判断
			 * @author terry
			 * Thu Jul 22 10:46:07 CST 2010
			 */			
			if( $theCtrl->isNeedMenu() && strtolower($defaultView) == 'html' ){
				//$theCtrl->isNeedCaidan();
				/**
				 * 如果用户已登录，读取菜单信息
				 * @todo 未登录可能也可以有菜单
				 */
				if( $user_id = Watt_Session::getSession()->getUserId() ){
					$tpmCaidans = TpmCaidanPeer::getJueseCaidan(Watt_Session::getSession()->getRoleId());
					if( count( $tpmCaidans ) ){
						$view->setHeader( $tpmCaidans, "menu" );
					}
				}
				//读取菜单完
			}

			//$view->renderModel($theCtrl);
			$view->renderView( $data, $this->_getDefaultViewFileOfAction( $controller, $action ), true );
		}
		
		/**
		 * 与开始的Cache对应
		 */
		if( $actionCacheTime > 0 && $cache instanceof Watt_Cache ){
			$cache->caching();
		}
		return $goToDo;
	}
	/**
	 * 将一个do分解为 controller and action
	 *
	 * 
	 * @param string $do
	 * @return array array[0] = controller,array[1] = action
	 */
	protected function _analyzeDoToControllerAndAction( $do )
	{
		/**
		 * 将 do 进行分解
		 * 根据Watt 的规则进行分解，获得一个 Action 的路径
		 * 此处可以扩展为多个策略.规则的策略。
		 */
		$arrDo = explode( "_", $do );
		$action = array_pop($arrDo);
		// $controller 是一个带 路径的String
		$controller = implode( DIRECTORY_SEPARATOR , $arrDo );
		if( trim($controller) == "" )
		{
			//这是只有一个单词的do的情况
			//那么这时 这个单词是 controller
			//把 action 里的值转给 controller action 变为index
			$controller = $action;
			$action = $this->_defaultActionName;
		}

		//do的file对应到 controller
		//$doFile = Watt_Config::getAppPath() . trim( $controller, DIRECTORY_SEPARATOR ) . ".php";
		$doFile = trim( $controller, DIRECTORY_SEPARATOR ) . ".php";
		$doAction = $action . $this->_defaultActionPostfix;
		//exit($doFile);

		$arrRev[0] = $controller;
		$arrRev[1] = $action;
		$arrRev[2] = $doFile;
		$arrRev[3] = $doAction;

		return $arrRev;
	}

	/**
	 * 获得controller 和 action 对应的默认 viewfile
	 *
	 * @param string $controller
	 * @param string $action
	 */
	protected function _getDefaultViewFileOfAction( $controller, $action )
	{
		return ltrim( $controller, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $action;
	}

	//	protected function processDo_v2( $do, $defaultView = "Html" )
	//	{
	//		if( $do == "" ){
	//			$e = new Watt_Exception(Watt_I18n::trans("ERR_DISPATCH_NODO"));
	//			throw $e;
	//		}
	//		$goToDo = "";
	//
	//		/**
	//		 * 返回的结果为
	//		 * $arrRev[0] = $controller;
	//		 * $arrRev[1] = $action;
	//		 * $arrRev[2] = $doFile;
	//		 * $arrRev[3] = $doAction;
	//		 */
	//		$arrCtrlAndAction = $this->_analyzeDoToControllerAndAction( $do );
	//		$controller = $arrCtrlAndAction[0];
	//		$action = $arrCtrlAndAction[1];
	//		$doFile = $arrCtrlAndAction[2];
	//		$doAction = $arrCtrlAndAction[3];
	//		//exit( $doFile . "|" . $doAction );
	//
	//		/**
	//		 * 此处有点儿隐患。
	//		 */
	//		//如果没有controller，则寻找Index
	//		$i = 0;
	//		while ( $i++ < $this->_maxToDo ){
	//			//如果 默认的 index_index action 文件不存在，则系统Down了
	//			if($controller == "index" && $action == "index" && !Watt::isReadable($doFile)){
	//			//if($controller == "" && $action == "index" && !Watt::isReadable($doFile)){
	//			//if($controller == "" && $action == "index" && !file_exists($doFile)){
	//				//此抛出异常
	//				throw (new Watt_Exception( Watt_I18n::trans("ERR_SITE_DOWN") ));
	//				break;
	//			}
	//			//如果文件不存在，则默认使用Index
	//			if(Watt::isReadable($doFile)){
	//				/**
	//				 * 这个Watt_Controller 只能用于PHP5
	//				 * 如果用于PHP4，每个功能都要继承这个类
	//				 */
	//
	//				//这是为了Controller里的 redirect 可以正常使用
	//				ob_start();
	//
	//				//$theCtrl = new Watt_Controller_Action($doFile);
	//				$theCtrl = Watt_Controller_Action::factory( $controller, $action );
	//
	//				/**
	//				 * 检查 会话的权限。
	//				 * 如果没有权限，抛出一个异常
	//				 */
	//				Watt_Rbac::checkActionPrivilege( Watt_Session::getSession(), $theCtrl, $action );
	//
	//				if( method_exists( $theCtrl, $doAction ) )
	//				{
	//					//执行controller中的action
	//					$theCtrl->$doAction();
	//				}
	//				else
	//				{
	//					throw (new Exception(Watt_I18n::trans( "ERR_APP_LOST_ACTION" )));
	//				}
	//				$goToDo = $theCtrl->getGoToDo();
	//				$data = $theCtrl->getData();
	//				if( defined("DEBUG") && DEBUG )
	//				{
	//					//调试阶段才显示Controller里输出的信息
	//					echo ob_get_clean();
	//				}
	//				else
	//				{
	//					ob_clean();
	//				}
	//
	//				if( $theCtrl->isNeedView() )
	//				{
	//					/**
	//					 * 创建一个View。将来可以用不同的View代替此View
	//					*/
	//					$view = Watt_View::factory( $defaultView, Watt_Config::getViewPath() );
	//					//$view->renderModel($theCtrl);
	//					$view->renderView( $data, $this->_getDefaultViewFileOfAction( $controller, $action ), true );
	//				}
	//				break;
	//			}else{
	//				if( defined("DEBUG") && DEBUG )
	//				{
	//					throw (new Exception( Watt_I18n::trans("ERR_NO_APP_FILE")."[ ".$doFile." ]" ));
	//				}
	//				else
	//				{
	//					$do = "index";
	//					$arrCtrlAndAction = $this->_analyzeDoToControllerAndAction( $do );
	//					$controller = $arrCtrlAndAction[0];
	//					$action = $arrCtrlAndAction[1];
	//					/*
	//					$controller = "";
	//					$action = "index";
	//					$doFile = PATH_APP.$controller.DIRECTORY_SEPARATOR.$action.".php";
	//					*/
	//				}
	//			}
	//		}
	//		return $goToDo;
	//	}

	/**
	 * 处理 do
	 * 有可能会返回 goToDo
	 * 第一个版本 一个action一个文件
	 * 需要合成
	 * 
	 * @param string $do
	 * @return string $goToDo
	 */
	//	protected function processDo_V1( $do )
	//	{
	//		if( $do == "" ){
	//			$e = new Watt_Exception(Watt_I18n::trans("ERR_DISPATCH_NODO"));
	//			throw $e;
	//		}
	//		$goToDo = "";
	//
	//		/**
	//		 * 检查 会话的权限。
	//		 * 如果没有权限，抛出一个异常
	//		 * 此处别扭
	//		 */
	//		$rbac = new Watt_Rbac();
	//		$rbac->checkSession(Watt_Session::getSession(), $do);
	//
	//		/**
	//		 * 将 do 进行分解
	//		 * 根据Watt 的规则进行分解，获得一个 Action 的路径
	//		 * 此处可以扩展为多个策略.规则的策略。
	//		 */
	//		$arrDo = explode( "_", $do );
	//		$action = array_pop($arrDo);
	//		// $controller 是一个带 路径的String
	//		$controller = implode( DIRECTORY_SEPARATOR , $arrDo );
	//
	//		$doFile = PATH_APP . ltrim( $controller . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR ) . $action . ".php";
	//		//exit($doFile);
	//
	//		/**
	//		 * 此处有点儿隐患。
	//		 */
	//		//如果没有controller，则寻找Index
	//		$i = 0;
	//		while ( $i++ < $this->_maxToDo ){
	//			//if($controller == "index" && $action == "index" && !Watt::isReadable($doFile)){
	//			//if($controller == "" && $action == "index" && !Watt::isReadable($doFile)){
	//			if($controller == "" && $action == "index" && !file_exists($doFile)){
	//				//如果 默认的 action 文件不存在，则系统Down了
	//				//此处应抛出异常
	//				throw (new Watt_Exception( Watt_I18n::trans("ERR_SITE_DOWN") ));
	//				break;
	//			}
	//			//如果文件不存在，则默认使用Index
	//			if(Watt::isReadable($doFile)){
	//				/**
	//				 * 这个Watt_Controller 只能用于PHP5
	//				 * 如果用于PHP4，每个功能都要继承这个类
	//				 */
	//
	//				//这是为了Controller里的 redirect 可以正常使用
	//				ob_start();
	//
	//				//$theCtrl = new Watt_Controller_Action($doFile);
	//				$theCtrl = Watt_Controller_Action::factory( $controller, $action );
	//				$goToDo = $theCtrl->getGoToDo();
	//				$data = $theCtrl->getData();
	//				if( defined("DEBUG") && DEBUG )
	//				{
	//					//调试阶段才显示Controller里输出的信息
	//					echo ob_get_clean();
	//				}
	//				else
	//				{
	//					ob_clean();
	//				}
	//
	//				if( $theCtrl->isNeedView() )
	//				{
	//					/**
	//					 * 创建一个View。将来可以用不同的View代替此View
	//					*/
	//					$view = Watt_View::factory( "Watt_View_Html", Watt_Config::getViewPath() );
	//					//$view->renderModel($theCtrl);
	//					$view->renderView( $data, $theCtrl->getRelFileName(), true );
	//				}
	//				break;
	//			}else{
	//				if( defined("DEBUG") && DEBUG )
	//				{
	//					throw (new Exception( Watt_I18n::trans("ERR_NO_APP_FILE")."[ ".$doFile." ]" ));
	//				}
	//				else
	//				{
	//					$controller = "";
	//					$action = "index";
	//					$doFile = PATH_APP.$controller.DIRECTORY_SEPARATOR.$action.".php";
	//				}
	//			}
	//		}
	//		return $goToDo;
	//	}
}
