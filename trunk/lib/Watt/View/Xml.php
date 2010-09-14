<?
/**
 * Watt基础类库
 *
 * @author Terry
 * @package Watt
 */

/**
 * 本类实现渲染Model，模板加载，以XML的形式显示数据
 *
 * @author Terry
 * @package Watt_View
 */
class Watt_View_Xml extends Watt_View{
	protected $_viewExt = ".xml.php";
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
			return Watt_Util_Array::varToXml( $var );
		}
		else
		{
			return $var;
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
		//这里显示菜单
		
		//这里显示主体部分
		//parent::render( true );
		reset( $this->_data );
		echo '<?xml version="1.0" encoding="'.$this->_charSet.'"?>'."\n";
		echo Watt_Util_Array::varToXml( $this->_data );

		//这里显示底部
		$out = ob_get_clean();
		if( $show )
		{
			header( "Content-type: text/xml; charset=".$this->_charSet );
			echo $out;
		}
		//如果输出debug信息， XML会报错
		Watt_Debug::getDefaultDebug()->clearDebugInfo();
		return $out;
	}
	
}
?>