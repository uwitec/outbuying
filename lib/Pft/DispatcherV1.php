<?
/**
 * 整个架构的调度器
 * 解析程序入口的 do 
 * 调用 controller 的 action
 * 创建 view
 * 渲染 view
 *
 * @author Terry
 * @package Pft
 */

class Pft_Dispatcher
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
	function dispatch( ){
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
		if( r('pwdway') ){
			if( !Pft_Session::getSession()->getUserId() ){
				//这个判断是为了不让TQ登录后，访问此链接时，冲掉session中[是否TQ]那个设置
				
				$accounts = iconv( 'GB2312', 'UTF-8', base64_decode( str_replace( ' ', '+', r( "Username" ) ) ) );
				$pwd = iconv( 'GB2312', 'UTF-8', base64_decode( str_replace( ' ', '+', r( "Password" ) ) ) );
			}
		}else{
			if( r( 'login' ) == 'ok' ){
				/**
				 * 这是为了兼容TQ的那个点击“查收我的订单”，导致Web重登录的问题。
				 * @author terry
				 * @version 0.1.0
				 * Thu Sep 06 16:44:53 CST 2007
				 */
				$accounts = '';
				$pwd = '';
			}else{
				$accounts = r( "user_name" );
				$pwd = r( "user_pw" );				
			}
		}
		if( $accounts && $pwd ){
			$login_rev = 0;
			$user = TpmYonghuPeer::checkUserLogin( $accounts, $pwd, $login_rev );
			if( $login_rev == TpmYonghuPeer::USER_LOGIN_OK ){
				
				// 用户登陆成功后如果密码安全强度不够，跳转到修改密码页提示用户设置安全的密码
				$pswdChecker = new Tpm_Passwordchecker( $pwd );
				$cfgLevel = Pft_Config::getCfg('PSWD_CHECK_LEVEL');
				if ( $cfgLevel=='' )
					$cfgLevel = '0';
					
				if ($pswdChecker->getSecurityLevel() < $cfgLevel )
				{
					$_SESSION['LOW_PASSWORD']  = true;
//					header('Location:?do=ps_yonghu_changepwd&nosecurity=true');
//					exit;
				}
				
				Pft_Log::addLog( 'Login ok, accounts['.$accounts.']', Pft_Log::LEVEL_INFO, 'LOGIN_WEB_DIRECT_LOGIN' );		
			}
		}
		
		//如密码强度不够， 强制修改密码
		$superDoList = array('ps_yonghu_changepwd','login_logout', ''); // 数组中的Action不在强制之列
		if( @$_SESSION['LOW_PASSWORD']  && !in_array($do, $superDoList) ){
			header('Location:?do=ps_yonghu_changepwd&nosecurity=true');
			exit;
		}
		
		$i = $this->_maxToDo;
		while ( $do != "" && $i-- > 0 ){
			$do = $this->processDo( $do, $v );
		}
		
		//如果是渠道代理商客户,传神客户,客户则记录日志 2007-7-9 john
		if(Pft_Session::getSession()->getRoleShortname()=="QDKH"||Pft_Session::getSession()->getRoleShortname()=="CSKH"||Pft_Session::getSession()->getRoleShortname()=="CR")
		{
		$accessLoger = new Pft_Log_Db( 'tpm_rizhi_fangwen' );
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
			$e = new Pft_Exception(Pft_I18n::trans("ERR_DISPATCH_NODO"));
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
		ob_start();

		$theCtrl = Pft_Controller_Action::factory( $controller, $action );

		/**
		 * 检查 会话的权限。 
		 * 如果没有权限，抛出一个异常
		 * 此处别扭
		 */
		$rbac = new Pft_Rbac();
		//$rbac->checkSession(Pft_Session::getSession(), $do);
		$privilege = $rbac->checkActionPrivilege( Pft_Session::getSession(), $theCtrl, $action );
		if( $privilege instanceof TpmYonghuzhaoquanxian ){
			if( !$theCtrl->getTitle() )$theCtrl->setTitle( Pft_I18n::trans( $privilege->getQxMingcheng() ) );
		}
		
		Pft_Debug::addInfoToDefault( '', 'Pre do action..' );
		
		if( method_exists( $theCtrl, $doAction ) ){
			//执行controller中的action
			$theCtrl->$doAction();
		}else{
			throw (new Exception(Pft_I18n::trans( "ERR_APP_LOST_ACTION" )));
		}
		
		Pft_Debug::addInfoToDefault( '', 'After do action..' );
		
		$goToDo = $theCtrl->getGoToDo();
		$data = $theCtrl->getData();
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

		if( $theCtrl->isNeedView() )
		{
			Pft_Debug::addInfoToDefault( '', 'Pre load view..' );
			
			if( $theCtrl->getViewType() ){
				$defaultView = $theCtrl->getViewType();
			}
			
			/**
			 * 创建一个View。将来可以用不同的View代替此View
			 */
			//$view = Pft_View::factory( "Html", Pft_Config::getViewPath() );
			$view = Pft_View::factory( $defaultView, Pft_Config::getViewPath() );
			$view->setHeader( $theCtrl->getHeader() );

			Pft_Debug::addInfoToDefault( '', 'After view factory..' );
			
			/**
			 * 如果用户已登录，读取菜单信息
			 * @todo 未登录可能也可以有菜单
			 */
			if( $user_id = Pft_Session::getSession()->getUserId() )
			{
//				if( $user_id == '189ce619-fe31-802c-369a-45b450b81a5b' )
//				{
//					//这个id是系统管理员
//					$c = new Criteria();
//					$c->addAscendingOrderByColumn( TpmCaidanPeer::CD_SHANGJI_ID );
//					$c->addAscendingOrderByColumn( TpmCaidanPeer::CD_PAIXU );
//					$tpmCaidans = TpmCaidanPeer::doSelect( $c );
//				}
//				else
//				{
					//$tpmCaidans = TpmCaidanPeer::getZhucaidan(Pft_Session::getSession()->getUserId());
					$tpmCaidans = TpmCaidanPeer::getJueseCaidan(Pft_Session::getSession()->getRoleId());
//				}

				if( count( $tpmCaidans ) )
				{
					$view->setHeader( $tpmCaidans, "menu" );

//					$menus = array();
//					foreach ( $tpmCaidans as $tpmCaidan )
//					{
//						$menus[] = array( $tpmCaidan->getCdMingcheng()
//						                , $tpmCaidan->getCdChuliye()
//						);
//					}
//					$view->setHeader( $menus, "menu" );
				}
			}
			//读取菜单完

			//$view->renderModel($theCtrl);
			$view->renderView( $data, $this->_getDefaultViewFileOfAction( $controller, $action ), true );
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
		 * 根据Pft 的规则进行分解，获得一个 Action 的路径
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
		//$doFile = Pft_Config::getAppPath() . trim( $controller, DIRECTORY_SEPARATOR ) . ".php";
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
	//			$e = new Pft_Exception(Pft_I18n::trans("ERR_DISPATCH_NODO"));
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
	//			if($controller == "index" && $action == "index" && !Pft::isReadable($doFile)){
	//			//if($controller == "" && $action == "index" && !Pft::isReadable($doFile)){
	//			//if($controller == "" && $action == "index" && !file_exists($doFile)){
	//				//此抛出异常
	//				throw (new Pft_Exception( Pft_I18n::trans("ERR_SITE_DOWN") ));
	//				break;
	//			}
	//			//如果文件不存在，则默认使用Index
	//			if(Pft::isReadable($doFile)){
	//				/**
	//				 * 这个Pft_Controller 只能用于PHP5
	//				 * 如果用于PHP4，每个功能都要继承这个类
	//				 */
	//
	//				//这是为了Controller里的 redirect 可以正常使用
	//				ob_start();
	//
	//				//$theCtrl = new Pft_Controller_Action($doFile);
	//				$theCtrl = Pft_Controller_Action::factory( $controller, $action );
	//
	//				/**
	//				 * 检查 会话的权限。
	//				 * 如果没有权限，抛出一个异常
	//				 */
	//				Pft_Rbac::checkActionPrivilege( Pft_Session::getSession(), $theCtrl, $action );
	//
	//				if( method_exists( $theCtrl, $doAction ) )
	//				{
	//					//执行controller中的action
	//					$theCtrl->$doAction();
	//				}
	//				else
	//				{
	//					throw (new Exception(Pft_I18n::trans( "ERR_APP_LOST_ACTION" )));
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
	//					$view = Pft_View::factory( $defaultView, Pft_Config::getViewPath() );
	//					//$view->renderModel($theCtrl);
	//					$view->renderView( $data, $this->_getDefaultViewFileOfAction( $controller, $action ), true );
	//				}
	//				break;
	//			}else{
	//				if( defined("DEBUG") && DEBUG )
	//				{
	//					throw (new Exception( Pft_I18n::trans("ERR_NO_APP_FILE")."[ ".$doFile." ]" ));
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
	//			$e = new Pft_Exception(Pft_I18n::trans("ERR_DISPATCH_NODO"));
	//			throw $e;
	//		}
	//		$goToDo = "";
	//
	//		/**
	//		 * 检查 会话的权限。
	//		 * 如果没有权限，抛出一个异常
	//		 * 此处别扭
	//		 */
	//		$rbac = new Pft_Rbac();
	//		$rbac->checkSession(Pft_Session::getSession(), $do);
	//
	//		/**
	//		 * 将 do 进行分解
	//		 * 根据Pft 的规则进行分解，获得一个 Action 的路径
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
	//			//if($controller == "index" && $action == "index" && !Pft::isReadable($doFile)){
	//			//if($controller == "" && $action == "index" && !Pft::isReadable($doFile)){
	//			if($controller == "" && $action == "index" && !file_exists($doFile)){
	//				//如果 默认的 action 文件不存在，则系统Down了
	//				//此处应抛出异常
	//				throw (new Pft_Exception( Pft_I18n::trans("ERR_SITE_DOWN") ));
	//				break;
	//			}
	//			//如果文件不存在，则默认使用Index
	//			if(Pft::isReadable($doFile)){
	//				/**
	//				 * 这个Pft_Controller 只能用于PHP5
	//				 * 如果用于PHP4，每个功能都要继承这个类
	//				 */
	//
	//				//这是为了Controller里的 redirect 可以正常使用
	//				ob_start();
	//
	//				//$theCtrl = new Pft_Controller_Action($doFile);
	//				$theCtrl = Pft_Controller_Action::factory( $controller, $action );
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
	//					$view = Pft_View::factory( "Pft_View_Html", Pft_Config::getViewPath() );
	//					//$view->renderModel($theCtrl);
	//					$view->renderView( $data, $theCtrl->getRelFileName(), true );
	//				}
	//				break;
	//			}else{
	//				if( defined("DEBUG") && DEBUG )
	//				{
	//					throw (new Exception( Pft_I18n::trans("ERR_NO_APP_FILE")."[ ".$doFile." ]" ));
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
