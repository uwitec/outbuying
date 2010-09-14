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
	const HEADER_CSS     = "tpm_css";
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
	private $_data = array();
	
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
	//protected $_varsCfg;//V2中暂时不用
	
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
	 * 整个controller(所有action)是否是public的，即不需要权限认证的
	 * @var boolean
	 */
	protected $_isPublic = false;
	
	/**
	 * public actions 的 列表
	 * 默认 indexAction 是 public 的
	 * 
	 * @var array
	 */
	private $_publicActionsList = array("index" => true
	                                     );
	/**
	 * 权限映射
	 * 
	 * @var array
	 */
	private $_privilegeMap = array();
	                                     
	/**
	 * Enter description here...
	 *
	 * @param boolean $allActionsPublic
	 * @param mix $publicActionsList
	 */
	//function __construct( $allActionsPublic = false, $publicActionsList = null )
	//function __construct( $allActionsPublic = false ){
	function __construct( $allActionsPublic = false ){
		$this->_isPublic = $allActionsPublic;	
		$this->_header[self::HEADER_TITLE] = "";
		$this->_header[self::HEADER_CSS] = "";
	}
	
	/**
	 * 这是一个很巧妙又很危险的构造函数..保留/没有被使用
	 *
	 * @param string $modelFile
	function __construct($modelFileName="")
	{
		$this->_scriptFile = $modelFileName;
		//因为在外部已经验证过文件了，所以此处不再验证
		//if(Pft::isReadable($modelFile))include($modelFile);
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
		$this->setData( $varname, $val );
    }

    /**
     * __set 对应的读取 varname 方法
     *
     * @param unknown_type $nm
     * @return unknown
     */
    protected function __get($varname)
	{
    	if( key_exists( $varname, $this->_data ) )
		{
    		return $this->_data[$varname];
    	}
		else
		{
    		//return "";
    		$e = new Pft_Exception(Pft_I18n::trans("ERR_CLASS_NO_PROPERTY"));
    		throw $e;
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

	/**
	 * 设置权限映射
	 * 子类的权限映射方法要覆盖此方法
	 */
	protected function setControllerPrivilegeMaps(){
	}
	
	/**
	 * 获得输入的参数
	 * 推荐！
	 *
	 * @param string $varname
	 * @return mix
	 */
	protected function getInputParameter( $varname )
	{
		return $this->getRequestVar( $varname );
	}

	protected function r( $varname ){
		return $this->getRequestVar( $varname );
	}
	
	protected function request( $varname )
	{
		return $this->getRequestVar( $varname );
	}
	
	/**
	 * 获得一个Request的变量
	 * 如果没有在request中定义，则返回一个null
	 * 不推荐！
	 *
	 * @param string $varname
	 * @return mix
	 */
	protected function getRequestVar( $varname )
	{
		if( isset( $_REQUEST[$varname] ) )
		{
			return $_REQUEST[$varname];
		}
		else
		{
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
	public function getData( $key = "" )
	{
		if( trim($key) == "" )
		{
			return $this->_data;
		}
		else
		{
			return isset( $this->_data[$key] )?$this->_data[$key]:null;
		}
	}
	
	/**
	 * 设置脚本文件名
	 *
	 * @param string $file
	 */
	public function setScriptFile($file)
	{
		$this->_scriptFile = $file;
	}

	/**
	 * 得到用例或功能的模块文件的文件名
	 *
	 * @return string
	 */
	public function getScriptFile()
	{
		return $this->_scriptFile;
	}
	
	/**
	 * 设置 controllerName 
	 *
	 * @param string $name
	 */
	public function setControllerName( $name ){
		$this->_controllerName = $name;
	}

	public function getControllerName(){
		return $this->_controllerName;
	}
	
	/**
	 * 获得相对 app 路径的路径+文件名
	 * 开始不带 /
	 *
	 * @return string
	 */
	public function getRelFileName()
	{
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
	protected function redirect( $uri )
	{
		header( "Location:".$uri );
		exit;
	}

	/**
	 * 转到 404 not found
	 *
	 */
	protected function redirectTo404()
	{
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	
    /**
     * 转到 自 controller 里的 action
     * 有些没想清楚。主要问题是对应的 view 的问题
     *
     * @param string $actionName
     */
    protected function redirectToSelfAction( $actionName, $params = "" )
    {
    	/*
    	$actionMethodName = $actionName."Action";
    	if( method_exists( $this, $actionMethodName ) )
    	{
    		$this->$actionMethodName();
    	}
    	*/
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
    protected function getSelfActionDo( $actionName )
    {
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
	protected function goToDo( $do )
	{
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
	 */
	public function setNeedView( $bool ){
		$this->_needView = $bool;
		return true;
	}
	
	/**
	 * 设置所有action是否为public
	 *
	 * @param boolean $v
	 */
	protected function setPublic( $v ){
		$this->_isPublic = $v;
	}
	
	/**
	 * 是否是公开的，即是否不需要权限认证
	 *
	 * @return boolean
	 */
	public function isPublic()
	{
		return $this->_isPublic;
	}
	
	/**
	 * 某个 controller 中的某个 action 是否是 public 的
	 *
	 * @param string $actionName
	 * @return boolean
	 */
	public function isActionPublic( $actionName )
	{
		if( key_exists( $actionName, $this->_publicActionsList ) 
		 && $this->_publicActionsList["$actionName"] )
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 回到上一次访问的页
	 * 应该用 session 或其他方式记录一下上次访问页
	 * 待补充
	 */
	protected function goBack()
	{
		
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
		$this->info_tip_msg = $msg;
		$this->info_tip_next_url = $nextUrl;
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
		                , self::HEADER_TIP_URL => $nextUrl );
	}
	
	/**
	 * 强行输出一些信息
	 *
	 * @param string $msg
	 */
	protected function forceOutput( $msg ){
		echo $msg;
		ob_flush();
		ob_start();
	}
	
	/**
	 * 获得action的title
	 * @return string
	 */
	public function getTitle(){
		return isset( $this->_header["sys_title"] )?$this->_header["sys_title"]:"";
	}
	/**
	 * 设置action的title
	 * @param string $title
	 */
	public function setTitle( $title ){
		$this->_header["sys_title"] = $title;
	}
	
	public function getHeader(){
		return $this->_header;
	}
	
	/**
	 * 设置公开的action
	 *
	 * @param string $actionName
	 */
	protected function _setPublicAction( $actionName )
	{
		$this->_publicActionsList[$actionName] = true;
	}
	
	public function addPrivilegeMap( $sourcePrivilege, $mappingPrivilege ){
		if( $sourcePrivilege == '*' ){
			$arrMethods = get_class_methods($this);
			foreach ( $arrMethods as $method) {
				if( preg_match( "/^([\\w]+)Action/i", $method, $match ) ){
					$this->_privilegeMap[$this->getControllerName().'_'.$match[1]] = $mappingPrivilege;
				}
			}
		}else{
			$this->_privilegeMap[$sourcePrivilege] = $mappingPrivilege;			
		}
//		print"<pre>Terry :";var_dump( $this->_privilegeMap );print"</pre>";
//		exit();
		return true;
	}
	
	public function addPrivilegeMapInThisCtrl( $sourceActionName, $mappingActionName ){
		$sourcePrivilege  = $this->getControllerName().'_'.$sourceActionName;
		$mappingPrivilege = $this->getControllerName().'_'.$mappingActionName;
		$this->addPrivilegeMap( $sourcePrivilege, $mappingPrivilege );
	}
	
	public function getMappingedPrivilege( $sourcePrivilege ){
		if( key_exists( $sourcePrivilege, $this->_privilegeMap ) ){
			return $this->_privilegeMap[$sourcePrivilege];
		}else{
			return $sourcePrivilege;
		}
	}
	
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
/**
 * =======================================
 * 以下为本系统中暂时不会用到的方法
 * /
	function getVarDef( $varName )
	{
		if( isset( $this->_varsCfg[$varName] ) )
	{
			return $this->_varsCfg[$varName];
		}else{
			return null;
		}
	}

	/**
	 * 通过传递一个ormObject引用，从该orm的ormCfg信息中得到变量定义信息
	 *
	 * @param Pft_Orm_Base $ormObj
	 * /
	function setVarFromORM( $ormObj )
	{
		$this->_varsCfg = $ormObj->getFieldsCfg();
	}
	
	/**
	 * 通过传递一个ormObject引用，将该orm的属性展开到模版可用的变量
	 *
	 * @param Pft_Orm_Base $ormObj
	 * /
	function addORMObj( $ormObj )
	{
		$this->setVarFromORM( $ormObj );
		$fieldsArr = explode( ",", $ormObj->getFieldList() );
		foreach ( $fieldsArr as $fieldName )
	{
			$this->$fieldName = $ormObj->$fieldName;
		}
	}
*/
}

function ucfirstForControl( $val ){
	if( $val == 'ctrl' ){
		return '';
	}else{
		return ucfirst( $val );
	}
}
?>