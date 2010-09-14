<?
/**
 * 本类实现渲染Model，模板加载，HTML的生成
 *
 * @author Terry
 * @package Pft_View
 */

class Pft_View_Ajax extends Pft_View{
	protected $_viewExt = ".html.php";
	
	function __construct( $scriptPath = "" )
	{
		parent::__construct( $scriptPath );
		//这里默认设置一些数据
		//包括 Header Title Css CharSet
		//其他一些数据等...
	}

	/**
	 * @todo 解决和dialog重复的问题
	 *
	 * @param boolean $show
	 * @return string
	 */
	
	public function render( $show=true )
	{
		ob_start();

		/**
		 * ajax 也要显示显示 Tq消息
		 * Tq消息要在 tip 之前
		 * ajax不显示消息, 此时消息存在 session 里，下次统一发送
		 */
		//echo Tpm_Message_Sender_Tq::getMsgHtml();
		
		/**
		 * 这里显示主体部分
		 */
		parent::render( true );

		$out = ob_get_clean();
		if( $show )
		{
			echo $out;
		}
		//如果输出debug信息， Ajax会不爽
		Pft_Debug::getDefaultDebug()->clearDebugInfo();		
		return $out;
	}
}
?>
