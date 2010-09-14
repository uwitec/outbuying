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
class Watt_View_Csv extends Watt_View{
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
		//ob_start();

		reset( $this->_data );

		/**
		 * 这里显示主体部分
		 */
		//parent::render( true );
		
		/**
		 * 这里约定，csv 模式必须使用 result 键值来存储
		 */
		$csvData = $this->_data['result'];
		$str = Watt_Util_Export::ArrayToCsv( $csvData );
		
		//$str = Watt_Util_Export::ArrayToCsv( $this->_data );
		
		header("Accept-Ranges: bytes");
		header("Content-Length: ".strlen( $str ));
		header("Expires: 0");
		header("Cache-Control: private");
		header("Content-type: application/csv" );
		header("Content-Disposition: attachment; filename=\"".date('Ymd-His').".csv\"" );

//		header('Content-type: application/csv');
//		header('Content-Disposition: attachment; filename="xxx.csv"');
		
		echo $str;
		
		//这里显示底部
		/*
		$out = ob_get_clean();
		if( $show )
		{
			echo $out;
		}
		//如果输出debug信息， XML会报错
		
		return $out;
		*/
		Watt_Debug::getDefaultDebug()->clearDebugInfo();
		return '';
	}
}
?>