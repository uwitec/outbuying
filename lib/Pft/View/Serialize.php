<?
/**
 * Pft基础类库
 *
 * @author Terry
 * @package Pft
 */

/**
 * 本类实现渲染Model，模板加载，以Json的形式显示数据
 *
 * @author Terry
 * @package Pft_View
 */
class Pft_View_Serialize extends Pft_View{
	protected $_viewExt = ".serialize.php";
	protected $_charSet = "UTF-8";
	
	function __construct( $scriptPath = "" )
	{
		parent::__construct( $scriptPath );
		//这里默认设置一些数据
		//包括 Header Title Css CharSet
		//XSLT等
		//其他一些数据等...
	}
	
	public function renderVar($var, $vDef="")
	{
		if( is_array($vDef) )
		{
			return json_encode( $var );
		}
		else
		{
			return json_encode( $var );
		}
	}
	
	/**
	 * 以Json的方式渲染数据并输出
	 *
	 * @param boolean $show
	 * @return string
	 */
	
	public function render( $show=true )
	{
		ob_start();
		echo serialize ( $this->_data );
		$out = ob_get_clean();
		if( $show )
		{
			echo $out;
		}
		//如果输出debug信息， Json会不正确
		Pft_Debug::getDefaultDebug()->clearDebugInfo();
		return $out;
	}
}
?>