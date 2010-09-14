<?
/**
 * 所有View的基类
 *
 * @author Terry
 * @package Pft
 */
abstract class Pft_View{
	protected $_viewPath;
	/**
	 * view文件的扩展名
	 *
	 * @var string
	 */
	protected $_viewExt;
	protected $_viewFile;
	protected $_vnameList = array();
	
	/**
	 * 记录header信息
	 *
	 * @var array
	 */
	protected $_header;
	
	/**
	 * 记录 tq cmd 信息
	 *
	 * @var array
	 */
	protected $_tqCmd;
	
	/**
	 * 用来显示的数据
	 *
	 * @var array
	 */
	protected $_data      = array();
	/**
	 * 最后一次输出的HTML结果
	 *
	 * @var string
	 */
	protected $_lastOut   = "";

	function __construct( $scriptPath = "" ){
		$this->setScriptPath( $scriptPath );
	}
	
 	/**
 	 * View 工厂
 	 * 
 	 * 只输入View的最后一个名称
 	 * 如 Pft_View_Html ，只输入 Html
 	 *
 	 * @param string $classNameLast
 	 * @return Pft_View
 	 */
 	public static final function factory( $classNameLast, $scriptPath = "" )
	{
		$className = "Pft_View_".ucfirst( $classNameLast );
 		return new $className( $scriptPath );
 	}
	
	public function setScriptPath($path)
	{
		$this->_viewPath = $path;
	}
	
	public function setViewExt( $viewExt )
	{
		$this->_viewExt = $viewExt;
	}
	
	public function setViewFile( $viewFile )
	{
		$this->_viewFile = $viewFile;
	}
	
	/**
	 * 通过名称自动寻找Model对应的Template
	 * 可以直接显示，也可以返回render后的字符串
	 * render后的字符串可以用来cache
	 *
	 * @param Pft_Controller_Action $theCtrl
	 * @param String $viewFile 相对View目录的路径+文件名
	 * @param Boolean $show 默认显示render的结果
	 */
	
	public function renderModel( $theCtrl, $viewFile="", $show=true )
	{
		if( !$theCtrl )return "";
		
		//此处为渲染模板展开 Model 中的数据变量
		$this->_data = $theCtrl->getData();

		//查找模版文件
		if( $viewFile == "" )
		{
			$this->_viewFile = $this->getDefaultModelViewFile( $theCtrl );
		}else{
			$this->_viewFile = $this->_getAbsViewPathFilename( $viewFile.$this->_viewExt );
		}
		
		return $this->render( $show );
	}

	/**
	 * 渲染一个指定的 view 文件
	 * view文件是相对于 view Path 的相对路径+文件名，开始不带 / 或 \
	 * 
	 *
	 * @param Pft_Data_Schema $data
	 * @param String $viewFile 不带扩展名的viewFile
	 * @param Boolean $show
	 * @return String
	 */
	public function renderView($data, $viewFile, $show=True)
	{
		//$this->_data = $data;
		$this->addData( $data );
		$this->_viewFile = $this->_getAbsViewPathFilename( $viewFile.$this->_viewExt );
		Pft_Debug::addInfoToDefault(__FILE__." ".__LINE__, "use view: ".$this->_viewFile);
		return $this->render( $show );
	}
	
	/**
	 * 渲染
	 *
	 * @param boolean $show
	 * @return string
	 */
	public function render( $show = true )
	{
		ob_start();
		if( Pft::isReadable($this->_viewFile) )
		{
			//如果存在 view file， 则提供给他一个 $data 变量
			if( is_array( $this->_data) ) reset( $this->_data );
			$data = $this->_data;
	
			//在新的分离的目标下,这段分解变量没有什么太大意义了
			/*
			foreach ( $data as $key => $val )
			{
				//此处根据变量定义render变量
				//$$key = $this->renderVar( $val , $theCtrl->getVarDef( $key ));
				$$key = $val;
				
				//初始化 $this->_vnameList[]
				//设置该名称变量的vname 在 renderVar() 里
				$this->_vnameList[$key] = $key;
			}
			*/

			//view file 中，将只看到一个 $data 的输入
			extract($this->_data);
			include($this->_viewFile);
		}else{
			if( DEBUG )
			{
				//此处使用默认方法render modal
				echo "<pre>";
				echo var_export( $this->_data );
				/*
				reset( $this->_data );
				echo htmlspecialchars( Pft_Util_Array::varToXml( $this->_data ) ); 
				*/
				/*
				while ( list( $key ) = each( $this->_data ) )
				{
					echo $$key;
				}
				*/	
				echo "</pre>";
			}
		}
		$this->_lastOut = ob_get_clean();
		if($show)
		{
			echo $this->_lastOut;
		}
		return $this->_lastOut;		
	}
 	
 	public function renderVar($var, $vDef="")
	{
		return $var;
	}
 	
 	/**
 	 * 返回一个变量在定义中的显示名称(VName)
 	 * 如没有定义，则返回变量名称
 	 *
 	 * @param String $varName
 	 * @return String
 	 */
 	function getVName($varName)
	{
 		if( isset( $this->_vnameList[$varName] ) )
		{
 			return $this->_vnameList[$varName];
 		}
		else
		{
 			return $varName;
 		}
 	}
 
 	function setHeader( $header, $key=null ){
 		if( $key )
 		{
 			$this->_header[$key] = $header;
 		}
 		else 
 		{
 			//$this->_header = $header;
 			if( is_array($header) )
 			{
 				foreach ( $header as $key1=>$val )
 				{
 					$this->_header[$key1] = $val;
 				}
 			}
 			else
 			{
 				
 			}
 		}

 	}
 	
 	/**
 	 * 获取一个action对应的view的文件
 	 *
 	 * @param Pft_Controller_Action $theCtrl
 	 * @return string
 	 */
 	function getDefaultModelViewFile( $theCtrl )
	{
 		return $this->_getAbsViewPathFilename( $theCtrl->getRelFileName().$this->_viewExt );
 	}
 	
 	/**
 	 * __get
 	 *
 	 * @param string $nm
 	 * @return mix
 	 */
 	function __get($nm)
	{
        if (isset($this->_data[$nm])) {
            $r = $this->_data[$nm];
            return $r;
        }
		else
		{
            $e = new Pft_Exception(Pft_I18n::trans("ERR_VIEW_NODATA"));
            throw($e);
            return "";
        }
 	}
 	
 	
 	/**
 	 * 将一些需要显示的数据合并到view中
 	 * 要求数据是 在 Pft:Data 根下的数据
 	 *
 	 * @param array $data Pft_Data_Schema
 	 */
 	public function addData( $data )
 	{
 		$this->_data = array_merge( $this->_data, $data );
 	}
 	
 	/**
 	 * 获得 view 的文件名
 	 * 输入的参数是相对于 view 根目录的 PathFilename
 	 * 自动获得绝对ViewPathFilename
 	 * 
 	 * 单独写一个方法是为了对付外部设置view路径的时的情况。
 	 * 那时就在这里修改就好了。
 	 *
 	 * @param string $relFilename
 	 * @return string
 	 */
 	protected function _getAbsViewPathFilename( $relPathFilename )
 	{
 		//return Pft_Config::getAbsPathFilename( "PATH_VIEW", $relPathFilename );
 		/**
 		 * 改为在子系统下放置view的模式
 		 * @author y31
 		 * Mon Dec 10 23:32:01 CST 2007
 		 */
 		return Pft_Config::getAbsPathFilename( "PATH_APP", str_replace( "/ctrl", "/view", str_replace( "\\", "/", $relPathFilename) ) );
 	}
 	
 	/**
 	 * 获得当前viewtype的 UrlParam
 	 *
 	 * @return string
 	 */
 	public static function getCurrentViewTypeUrlParam(){
 		$vt = (r('v'));
 		return $vt?'&v='.$vt:'';
 	}
}
?>
