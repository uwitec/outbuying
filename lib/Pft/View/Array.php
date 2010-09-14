<?
/**
 * 本类实现渲染Model，模板加载，以 PhpArray 的形式显示数据
 *
 * @author Terry
 * @package Pft_View
 */

class Pft_View_Array extends Pft_View{
	protected $_viewExt = "";
	protected $_charSet = "UTF-8";
	
	function __construct( $scriptPath = "" )
	{
		parent::__construct( $scriptPath );
		//这里默认设置一些数据
		//包括 Header Title Css CharSet
		//XSLT等
		//其他一些数据等...
	}
	
	/**
	 * 以PhpArray的方式渲染数据并输出
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

		echo var_export( $this->_data );

		//这里显示底部
		$out = ob_get_clean();
		if( $show )
		{
			//echo "<pre>\n";
			echo $out;
			//echo "\n</pre>";
		}
		Pft_Debug::getDefaultDebug()->clearDebugInfo();
		return $out;
	}
	
}
?>