<?
/**
 * 执行 Module+Action
 * 属性 _data 是关键属性，要显示的数据将存储在这里
 * 
 * @author Terry
 * @package Pft_Controller
 */
class Pft_Controller_Action{
	const HEADER_TITLE   = "sys_title";
	const HEADER_CSS     = "css";
	const HEADER_TIP     = "tip";
	const HEADER_TIP_MSG = "tip_msg";
	const HEADER_TIP_URL = "tip_url";
	
	/**
	 * 通用数据
	 *
	 * @var array
	 */
	private $_header;
	
	/**
	 * 用来记录显示数据
	 * 派生类不允许直接访问 _data
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * 记录存储controller的脚本文件 pathfilename
	 * @var string
	 */
	protected $_scriptFile;

	/**
	 * controller 名称
	 * @var string
	 */
	protected $_controllerName;
	
	/**
	 * 下一个toDo 的 do，用来衔接
	 * 这里指的是 先显示，然后用户Post的默认 do
	 * @var string
	 */
	protected $_nextToDo = "";
	protected $_nextToDoParams = "";

	/**
	 * 这是用来继续的 ToDo
	 * 这里在整个分发(dispacth)的流程里进行循环
	 */
	protected $_goToDo = "";
	
	/**
	 * 是否需要 view 
	 * @var boolean
	 */
	protected $_needView = true;
	
	/**
	 * 是否需要菜单
	 *
	 * @var boolean
	 */
	protected $_needMenu = true;
	
	/**
	 * 整个controller(所有action)是否是public的，即不需要权限认证的
	 * @var boolean
	 */
	//private $_isPublic = false;
	//private $_ctrlLevel = Pft_Rbac::LEVEL_ROLE;	//默认权限不能太低 by terry at Fri Jul 10 11:30:32 CST 2009
	private $_ctrlLevel = Pft_Rbac::LEVEL_LOGIN;
	
	/**
	 * public actions 的 列表
	 * 默认 indexAction 是 public 的
	 * 
	 * @var array
	 */
	private $_publicActionsList = array("index" => Pft_Rbac::LEVEL_PUBLIC
	                                     );
	/**
	 * 权限映射
	 * 
	 * @var array
	 */
	private $_privilegeMap = array();
	                                     
	/**
	 * @param boolean $allActionsPublic
	 */
	function __construct( $allActionsPublic = false ){
		$this->_autoGetControllerName();
		$this->setPublic( $allActionsPublic );
		$this->_header[self::HEADER_TITLE] = "";
		$this->_header[self::HEADER_CSS] = "";
		$this->addPrivilegeMapInThisCtrl('*','list'); //默认list为ctrl中最高管理权限 by terry at Fri Jul 10 12:00:47 CST 2009
	}

	/**
	 * 自动获取控制器名称...但是由于类命名不规范问题(如未分目录，确用大写字母分割单词)，所以无法保证完全正确
	 */
	private function _autoGetControllerName(){
		$className = get_class( $this );
		$matchs = array();
		preg_match_all("([A-Z]{1}[a-z0-9]*)",$className,$matchs);
		$controllerNameArray = $matchs[0];
		unset( $controllerNameArray[count($controllerNameArray)-1] ); //去掉 Controller
		$controllerName = strtolower( implode( '_', $controllerNameArray ) );
		if( $controllerName ){
			$this->setControllerName( $controllerName );
		}	
	}

	/**
	 * 这是一个很巧妙又很危险的构造函数..保留/没有被使用
	 *
	 * @param string $modelFile
	function __construct($modelFileName="")
	{
		$this->_scriptFile = $modelFileName;
		//因为在外部已经验证过文件了，所以此处不再验证
		//if(Watt::isReadable($modelFile))include($modelFile);
		if($modelFileName) include_once($modelFileName);
	}
	 */
	
	/**
	 * 通过 $this->varname 来给 _data 增加数据，然后传递给调度
	 *
	 * @param string $nm
	 * @param mix $val
	 */
    protected function __set($varname, $val)
	{
		//$this->_data[$varname] = $val;
		if( '_isPublic' == $varname ){
			//这是为了兼容老版本的 _isPublic
			$this->setPublic( $val );
		}else{
			$this->setData( $varname, $val );			
		}
    }

    /**
     * __set 对应的读取 varname 方法
     *
     * @param unknown_type $nm
     * @return unknown
     */
    protected function __get($varname)
	{
    	if( key_exists( $varname, $this->_data ) ){
    		return $this->_data[$varname];
    	}else{
			/**
			 * 还是改为null比较好
			 * @author terry
			 * @version 0.1.0
			 * Thu Mar 13 12:43:36 CST 2008
			 */
			return null;
			/*
    		$e = new Pft_Exception(Pft_I18n::trans("ERR_CLASS_NO_PROPERTY"));
    		throw $e;
    		*/
    	}
    }

	/**
	 * 设置权限映射
	 * 子类的权限映射方法要覆盖此方法
	 */
	protected function setControllerPrivilegeMaps(){}
	
	/**
	 * 获得输入的参数
	 * in 的别名
	 * @param string $varname
	 * @return mix
	 */
	protected function getInputParameter( $varname ){
		return $this->in( $varname );
	}

	/**
	 * 获得输入的参数
	 * 推荐！
	 * @param string $varname
	 * @return mix
	 */
	protected function in( $varname ){
		return $this->getRequestVar( $varname );
	}
	
	/**
	 * @param string $varname
	 * @return mix
	 */
	protected function request( $varname ){
		return $this->getRequestVar( $varname );
	}
	
	/**
	 * 取得get数据
	 * @param string $varname
	 * @return mix
	 * @author terry
	 * Mon Feb 16 11:09:31 CST 2009
	 */
	protected function get( $varname ){
		return isset($_GET[$varname])?$_GET[$varname]:null;
	}
	
	/**
	 * 取得post数据
	 * @param string $varname
	 * @return mix
	 * @author terry
	 * Mon Feb 16 11:09:31 CST 2009
	 */
	protected function post( $varname ){
		return isset($_POST[$varname])?$_POST[$varname]:null;
	}
	
	/**
	 * 获得一个Request的变量
	 * 如果没有在request中定义，则返回一个null
	 * 不推荐直接使用，尽量使用in
	 *
	 * @param string $varname
	 * @return mix
	 */
	protected function getRequestVar( $varname ){
		if( isset( $_REQUEST[$varname] ) ){
			/**
			 * 容不得考虑仔细了，先满足了再说
			 * 系统耦合度已经越来越高了..要坏掉了
			 * 此处可以过滤指定的一些输入词，例如David
			 * @author terry
			 * @version 0.1.0
			 * Thu Feb 14 10:26:10 CST 2008
			 */
			/*if( Pft_Session::getSession()->getUserId() ){
				return $_REQUEST[$varname];
			}else{
				//仅过滤未登录用户输入的信息
				$filterList = Tpm_Config::getUserConfig(Pft_Config::getDefaultZuId(),'filter_words');
				return Pft_Util_String::filterString( $_REQUEST[$varname], $filterList );				
			}*/
			return $_REQUEST[$varname];
		}else{
			return null;
		}
	}
	
    /**
     * 设置要显示的数据
     *
     * @param string $key
     * @param string $val
     */
    protected function setData( $key, $val ){
    	$this->_data[$key] = $val;
    }
	
	/**
	 * 返回需要显示的数据
	 *
	 * @return Array Pft_Data_Schema
	 */
	public function getData( $key = "" ){
		if( trim($key) == "" ){
			return $this->_data;
		}else{
			return isset( $this->_data[$key] )?$this->_data[$key]:null;
		}
	}
	
	/**
	 * 设置脚本文件名
	 *
	 * @param string $file
	 */
	public function setScriptFile($file){
		$this->_scriptFile = $file;
	}

	/**
	 * 得到用例或功能的模块文件的文件名
	 *
	 * @return string
	 */
	public function getScriptFile(){
		return $this->_scriptFile;
	}
	
	/**
	 * 设置 controllerName 
	 * @param string $name
	 */
	private function setControllerName( $name ){
		$this->_controllerName = $name;
	}

	/**
	 * 获取 controllerName 
	 * @return string
	 */
	public function getControllerName(){
		return $this->_controllerName;
	}
	
	/**
	 * 获得相对 app 路径的路径+文件名
	 * 开始不带 /
	 *
	 * @return string
	 */
	public function getRelFileName(){
		return ltrim( substr($this->_scriptFile,strlen(PATH_APP)
		                    ,strlen($this->_scriptFile)-4-strlen(PATH_APP))
		             ,DIRECTORY_SEPARATOR);
	}


	/**
	 * 使用 header 转到另一个 do 的地方
	 * 调用本方法后，会exit
	 * 示例:
	 * $this->redirect( "user_detail", "&user_id=10" );
	 *
	 * @param string $do
	 * @param string $params 附加的参数，也就是queryString后面的参数,带上&
	 */
	protected function redirectToDo( $do, $params = "" ){
		if( $params && ( strpos( "&", $params ) != 0 ) ) $params = "&" . $params;
		$uri = "?do=".$do.$params;
		$this->redirect( $uri );
	}
	
	/**
	 * 转到指定的 uri
	 *
	 * @param string $uri
	 */
	protected function redirect( $uri ){
		header( "Location:".$uri );
		exit;
	}

	/**
	 * 转到 404 not found
	 *
	 */
	protected function redirectTo404(){
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	
    /**
     * 转到 自 controller 里的 action
     * @param string $actionName
     */
    protected function redirectToSelfAction( $actionName, $params = "" ){
    	$this->redirectToDo( $this->_controllerName."_".$actionName, $params );
    }

    /**
     * 获得同一contrller内的指定action的do的名称
     * 主要用来为 tip 的nexturl使用，防止因为ctrlname的改变而导致url实效
     * 
     * <code>
     * $this->addTip( Pft_I18n::trans("MSG_SEND_SUCCESS"), "?do=".$this->getSelfActionDo("list") );
     * </code>
     *
     * @param string $actionName
     * @return string
     */
    protected function getSelfActionDo( $actionName ){
    	return $this->_controllerName."_".$actionName;
    }
    
	/**
	 * 转到另一个 do 的地方
	 * 这是在本次循环处理内继续进行一个 do
	 * 示例:
	 * $this->goToDo( "user_detail", "&user_id=10" );
	 * 这里有一个问题，那就是 原有的 Request 变量会带到 nextToDo
	 * 可能会有非期望的结果出现...
	 * 是否 unset $_GET 和 $_POST ?
	 * 或许 nextToDo 正想利用 $_GET 和 $_POST?
	 * 为了防止这么混淆的概念，暂时屏蔽掉了 $params ...
	 * 仅是 goToDo 
	 * 
	 * @param string $do
	 * @param string $params 附加的参数，也就是queryString后面的参数,带上&
	 */
	//public function goToDo( $do, $params = "" )
	protected function goToDo( $do ){
		$this->_goToDo = $do;
		//$this->_nextToDoParams = $params;
		return $do;
	}

	/**
	 * 获得下一个要做的 do
	 * 这是在分发(调度)循环中作的do
	 *
	 * @return string
	 */
	public function getGoToDo(){
		return $this->_goToDo;
	}

	/**
	 * 这里是设置本次完成后，用户Post的do
	 *
	 * @param string $do
	 * @return string
	 
	protected function nextToDo( $do )
	{
		$this->_nextToDo = $do;
		//$this->_nextToDoParams = $params;
		return $do;
	}
	*/

	/**
	 * 获得下一个要做的 do
	 * 这是显示给用户，然后Post的do
	 * 
	 * @return string
	 */
	/*暂时注释 防止有地方调用了此方法
	public function getNextToDo(){
		return $this->_nextToDo;
		//return $this->_nextToDo . $this->_nextToDoParams;
	}
	*/
	
	/**
	 * 是否需要 view
	 *
	 * @return boolean
	 */
	public function isNeedView(){
		return $this->_needView;
	}
	
	/**
	 * 设置是否需要显示
	 *
	 * @param boolean $bool
	 * @return true
	 */
	public function setNeedView( $bool ){
		$this->_needView = $bool;
		return true;
	}
	/**
	 * 是否需要 menu
	 *
	 * @return boolean
	 */
	public function isNeedMenu(){
		return $this->_needMenu;
	}
	
	/**
	 * 设置是否需要Menu
	 *
	 * @param boolean $bool
	 * @return true
	 */
	public function setNeedMenu( $bool ){
		$this->_needMenu = $bool;
		return true;
	}
	
	/**
	 * 设置所有action是否为public
	 *
	 * @param boolean $v
	 */
	protected function setPublic( $v ){
		if( $v ){
			$this->_ctrlLevel = Pft_Rbac::LEVEL_PUBLIC;
		}
	}
	
	/**
	 * 是否是公开的，即是否不需要权限认证
	 *
	 * @return boolean
	 */
	public function isPublic(){
		//return $this->_isPublic;
		return ( Pft_Rbac::LEVEL_PUBLIC == $this->_ctrlLevel );
	}
	
	/**
	 * @param Pft_Rbac::LEVEL_* $v
	 */
	public function setCtrlLevel( $v ){
		$this->_ctrlLevel = $v;
	}
	
	/**
	 * @return Pft_Rbac::LEVEL_*
	 */
	public function getCtrlLevel(){
		return $this->_ctrlLevel;
	}
	
	/**
	 * 某个 controller 中的某个 action 是否是 public 的
	 *
	 * @param string $actionName
	 * @return boolean
	 */
	public function isActionPublic( $actionName ){
		if( key_exists( $actionName, $this->_publicActionsList ) 
		 && $this->_publicActionsList[$actionName] == Pft_Rbac::LEVEL_PUBLIC )
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 设置 action 访问权限
	 * 
	 * @param string $actionName
	 * @param Pft_Rbac::LEVEL_* $level
	 * @author terry
	 * @version 0.1.0
	 * Thu May 22 14:55:09 CST 2008
	 */
	public function setActionLevel( $actionName, $level ){
		$this->_publicActionsList[$actionName] = $level;
	}
	
	/**
	 * 返回action访问权限，如未设置权限，则默认返回角色级
	 * @return Pft_Rbac::LEVEL_
	 * @author terry
	 * @version 0.1.0
	 * Thu May 22 14:57:02 CST 2008
	 */
	public function getActionLevel( $actionName ){
		if( key_exists( $actionName, $this->_publicActionsList ) ){
			return $this->_publicActionsList[$actionName];
		}else{
			/**
			 * @todo 将来可以配置框架默认action级别
			 */
			//return Pft_Rbac::LEVEL_ROLE;
			return $this->getCtrlLevel();
		}
	}
	
	/**
	 * 回到上一次访问的页
	 * 应该用 session 或其他方式记录一下上次访问页
	 * 待补充
	 */
	protected function goBack(){
		
	}

	const INFO_TYPE_TIP   = 0;
	const INFO_TYPE_WARN  = 1;
	const INFO_TYPE_ERROR = 2;
	
	/**
	 * 转到消息提示
	 *
	 * @param string $msg
	 * @param string|array $nextUrl
	 */
	protected function redirectToInfoTip( $msg, $nextUrl="", $info_type = self::INFO_TYPE_TIP ){
		if( self::INFO_TYPE_TIP == $info_type ){
			$this->info_tip_msg = $msg;
			$this->info_tip_next_url = $nextUrl;			
		}
	}

	/**
	 * 在页面内增加一个提示
	 * 如果输入了 nextUrl， 则转到另一个页面
	 *
	 * @param 提示信息 $msg
	 * @param string $nextUrl
	 */
	protected function addTip( $msg, $nextUrl = "" )
	{
		$this->_header[self::HEADER_TIP] = 
		           array( self::HEADER_TIP_MSG => $msg
		                , self::HEADER_TIP_URL => $nextUrl);
	}
	
	/**
	 * 强行输出一些信息
	 *
	 * @param string $msg
	 */
	protected function forceOutput( $msg=null ){
		echo $msg;
		ob_flush();
		ob_start();
	}
	
	/**
	 * 获得action的title
	 * @return string
	 */
	public function getTitle(){
		return isset( $this->_header[self::HEADER_TITLE] )?$this->_header[self::HEADER_TITLE]:"";
	}

	/**
	 * 设置action的title
	 * @param string $title
	 */
	public function setTitle( $title ){
		$this->_header[self::HEADER_TITLE] = $title;
	}
	
	public function getHeader(){
		return $this->_header;
	}
	
	/**
	 * 设置公开的action
	 *
	 * @param string $actionName
	 */
	protected function _setPublicAction( $actionName ){
		$this->_publicActionsList[$actionName] = Pft_Rbac::LEVEL_PUBLIC;
	}
	
	/**
	 * 将ctrl内action1的映射到action2的权限上，即action1权限级别与action2的权限级别相同
	 * 此功能仅影响角色权限级别和用户权限级别
	 * @author terry
	 * @version 0.1.0
	 * Thu May 22 15:05:13 CST 2008
	 *
	 * @param string $sourcePrivilege ctrl+action名
	 * @param string $mappingPrivilege ctrl+action名
	 * @return boolean
	 */
	public function addPrivilegeMap( $sourcePrivilege, $mappingPrivilege ){
		if( $sourcePrivilege == '*' ){
			$arrMethods = get_class_methods($this);
			$match = null;
			foreach ( $arrMethods as $method) {
				if( preg_match( "/^([^_][\\w]+)Action$/i", $method, $match ) ){
					$this->_privilegeMap[$this->getControllerName().'_'.$match[1]][$mappingPrivilege] = $mappingPrivilege;
				}
			}
		}else{
			$this->_privilegeMap[$sourcePrivilege][$mappingPrivilege] = $mappingPrivilege;			
		}
		return true;
	}
	
	/**
	 * 将ctrl内的action1的映射到ctrl内action2的权限上，即action1权限级别与action2的权限级别相同
	 * $this->addPrivilegeMapInThisCtrl('*','list');
	 * @author terry
	 * @version 0.1.0
	 * Fri May 23 10:28:59 CST 2008
	 * 
	 * @param string $sourceActionName action名
	 * @param string $mappingActionName action名
	 */
	public function addPrivilegeMapInThisCtrl( $sourceActionName, $mappingActionName ){
		if( !$this->getControllerName() ){
			$this->_autoGetControllerName();
		}
		$sourcePrivilege  = $sourceActionName=='*'?$sourceActionName:$this->getControllerName().'_'.$sourceActionName;
		//$sourcePrivilege = $sourceActionName;
		$mappingPrivilege = $this->getControllerName().'_'.$mappingActionName;
		$this->addPrivilegeMap( $sourcePrivilege, $mappingPrivilege );
	}

	/**
	 * 返回权限映射表,需要输入完整的ctrl_action名称
	 *
	 * @param string $sourceActionName
	 * @return string|array
	 */
	public function getMappingedPrivilege( $sourcePrivilege ){
		if( key_exists( $sourcePrivilege, $this->_privilegeMap ) ){
			//return $this->_privilegeMap[$sourcePrivilege];
			$arrMapp = $this->_privilegeMap[$sourcePrivilege];
			/**
			* 说明：这是为了解决映射了以后就无法单独分配权限的问题
			* 作者：Terry
			* 时间：Mon Nov 10 11:15:28 CST 2008
			*/
			$arrMapp[$sourcePrivilege] = $sourcePrivilege;
			return $arrMapp;
		}else{
			return $sourcePrivilege;
		}
	}
	
	/**
	 * 返回ctrl内的权限映射表,只需要输入action名称,ctrl名称自动增加
	 *
	 * @param string $sourceActionName
	 * @return string|array
	 */
	public function getMappingedPrivilegeByAction( $sourceActionName ){
		$sourcePrivilege = $this->getControllerName().'_'.$sourceActionName;
		return $this->getMappingedPrivilege( $sourcePrivilege );
	}
	
	private $_viewType;
	/**
	 * 指定自己的ViewType
	 * Html | Xml | Ajax | Dialog ...
	 * @param string $v
	 */
	public function setViewType( $v ){$this->_viewType = $v;}
	/**
	 * 指定自己的ViewType
	 * Html | Xml | Ajax | Dialog ...
	 * @param string $v
	 */
	public function getViewType(){return $this->_viewType;}

	private $_isCache   = false;
	private $_cacheTime = 0;
	private $_cacheActionList = array();
	/**
	 * 在构造函数中设置缓存的action和缓存的时间(秒)
	 *
	 * @param string $action
	 * @param int $cacheTime
	 */
	protected function _setActionCacheTime( $action, $cacheTime ){
		$this->_cacheActionList[$action] = $cacheTime;
	}
	
	/**
	 * 判断一个Action是否可以被缓存
	 *
	 * @param string $action
	 * @return int
	 */
	public function getActionCacheTime( $action ){
		if( key_exists( $action, $this->_cacheActionList ) ){
			return $this->_cacheActionList[$action];
		}else{
			return 0;
		}
	}

	/**
	 * 根据controllerName 和 actionName 生产一个 controller
	 *
	 * @param String $controllerName
	 * @param String $actionName
	 * @return Pft_Controller_Action
	 */
	public static final function factory($controllerName, $actionName)
	{
		//$toFile = Pft_Config::getAppPath() . $controllerName . ".php";
		$toFile = Pft_Config::getAbsPathFilename( "PATH_APP", $controllerName . ".php" );
		//$arrTmp = array_map("ucfirst", explode( DIRECTORY_SEPARATOR, $controllerName) );
		$arrTmp = array_map("ucfirstForControl", explode( DIRECTORY_SEPARATOR, $controllerName) );
		$className = ucfirst(implode("",$arrTmp))."Controller";
		
		/**
		 * 去掉了 Ctrl
		 * @author y31
		 * Mon Dec 10 23:19:08 CST 2007
		 */

/*
这里要适应变化
通过上面的代码改成一个controller多个action的形式

		//这种形式要求 conntroller
		$toFile = PATH_APP . $controllerName . DIRECTORY_SEPARATOR . $actionName . ".php";
		
		$arrTmp = array_map("ucfirst", explode( DIRECTORY_SEPARATOR, $controllerName) );
		$className = ucfirst(implode("",$arrTmp)).ucfirst($actionName)."Controller";
*/
		
		if( DEBUG ){
			include_once( $toFile );
		}
		else
		{
			@include_once($toFile);
		}
		
		//Pft::loadFile( $toFile );
		if( class_exists( $className ) )
		{
			$class = new $className();
			$class->setScriptFile($toFile);
			$class->setControllerName( str_replace( DIRECTORY_SEPARATOR, "_", $controllerName ) );
			$class->setControllerPrivilegeMaps();
			return $class;
		}
		else
		{
			throw (new Exception(Pft_I18n::trans("ERR_APP_LOST_CONTROLLER") . "[ ".$className." ]" ));
			return null;
		}

	}
}

function ucfirstForControl( $val ){
	if( $val == 'ctrl' ){
		return '';
	}else{
		return ucfirst( $val );
	}
}
?>