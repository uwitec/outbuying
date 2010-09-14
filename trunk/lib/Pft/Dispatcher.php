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
	protected $_defaultControlPath    = "ctrl";
	protected $_defaultViewPath       = "view";
	protected $_defaultModelPath      = "model";

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

		//如密码强度不够， 强制修改密码
		/*
		$superDoList = array('ps_yonghu_changepwd','login_logout', ''); // 数组中的Action不在强制之列
		if( @$_SESSION['LOW_PASSWORD']  && !in_array($do, $superDoList) ){
			header('Location:?do=ps_yonghu_changepwd&nosecurity=true');
			exit;
		}
		*/

		$i = $this->_maxToDo;
		while ( $do != "" && $i-- > 0 ){
			$do = $this->processDo( $do, $v );
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
		}else{
			$posOfFirstSeparator = strpos( $controller, DIRECTORY_SEPARATOR );
			if( $posOfFirstSeparator !== false ){
				$controller = substr( $controller, 0 , $posOfFirstSeparator ).DIRECTORY_SEPARATOR.$this->_defaultControlPath.substr( $controller, $posOfFirstSeparator );
			}
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
}
