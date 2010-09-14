<?
/**
 * Watt基础类库
 *
 * @author Terry
 * @package Watt
 */

/**
 * 本类实现渲染Model，模板加载，以Json的形式显示数据
 *
 * @author Terry
 * @package Watt_View
 */
class Watt_View_Json extends Watt_View{
	protected $_viewExt = ".json.php";
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
	 * 以XML的方式渲染数据并输出
	 *
	 * @param boolean $show
	 * @return string
	 */
	
	public function render( $show=true )
	{
		ob_start();
		echo json_encode ( $this->_data );
		$out = ob_get_clean();
		if( $show )
		{
			echo $out;
		}
		//如果输出debug信息， XML会报错
		Watt_Debug::getDefaultDebug()->clearDebugInfo();
		return $out;
	}
	
}
?>