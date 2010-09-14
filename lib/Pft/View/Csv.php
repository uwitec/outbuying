<?
/**
 * Pft基础类库
 *
 * @author Terry
 * @package Pft
 */

/**
 * 本类实现渲染Model，模板加载，以XML的形式显示数据
 *
 * @author Terry
 * @package Pft_View
 */
class Pft_View_Csv extends Pft_View{
	protected $_viewExt = ".html.php";
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
	 * 以XML的方式渲染数据并输出
	 *
	 * @param boolean $show
	 * @return string
	 */
	
	public function render( $show=true )
	{
//		header( "Content-type: application/octetstream" );
//		header( "Content-Disposition: attachment; filename=".date('Y-m-d H:i:s').".csv" );

//		header('Content-type: application/csv');
//		header('Content-Disposition: attachment; filename="xxx.csv"');
		
		ob_start();
		//这里显示菜单
		
		//这里显示主体部分
		//parent::render( true );
		reset( $this->_data );

		/**
		 * 这里显示主体部分
		 */
		parent::render( true );
		
		//这里显示底部
		$out = ob_get_clean();
		if( $show )
		{
			echo $out;
		}
		//如果输出debug信息， XML会报错
		Pft_Debug::getDefaultDebug()->clearDebugInfo();
		return $out;
	}
}
?>