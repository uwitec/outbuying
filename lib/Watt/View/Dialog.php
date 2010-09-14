<?
/**
 * 本类实现渲染Model，模板加载，HTML的生成
 *
 * @author Terry
 * @package Watt_View
 */

class Watt_View_Dialog extends Watt_View{
	protected $_viewExt = ".html.php";
	
	function __construct( $scriptPath = "" )
	{
		parent::__construct( $scriptPath );
		//这里默认设置一些数据
		//包括 Header Title Css CharSet
		//其他一些数据等...
	}

	/**
	 * 职责变了，这个方法没有用了
	 *
	 * @param boolean $show
	 * @return string
	 */
	
	public function render( $show=true )
	{
		/**
		 * 是否输出主体
		 */
		$outputBody = true;
		
		ob_start();
		//echo "Html Header";
		
		//echo "Body Header";
		if( $this->_header && is_array( $this->_header ) ){
			//这里展开了 sys_title 和 tpm_css
			extract( $this->_header );
		}
		include( $this->_getAbsViewPathFilename( "inc/header.dailog.html.php" ) );
		
		/**
		 * 这里显示 Tq消息
		 * Tq消息要在 tip 之前
		 */
		echo Tpm_Message_Sender_Tq::getMsgHtml();
		
		/**
		 * 处理在 ctrl 里设置的tip提示信息
		 */
		if( is_array($this->_header) && key_exists( Watt_Controller_Action::HEADER_TIP, $this->_header ) ){
			$tip = $this->_header[Watt_Controller_Action::HEADER_TIP];
			echo "<script>";
			$msg = $tip[Watt_Controller_Action::HEADER_TIP_MSG];
			if( $nextUrl = $tip[Watt_Controller_Action::HEADER_TIP_URL] ){
				$matchs = null;
				if( preg_match( "/^javascript:(.*)/", $nextUrl, $matchs ) ){
//						echo "function onloadTip(){Ext.Msg.alert('TPM', '".addslashes($tip[Watt_Controller_Action::HEADER_TIP_MSG])."', function(){{$matchs[1]};return false;});}";
					echo "function onloadTip() {".($msg?"alert('".addslashes($msg)."');":'')."{$matchs[1]}}";						
				}else{
//						echo "function onloadTip(){Ext.Msg.alert('TPM', '".addslashes($tip[Watt_Controller_Action::HEADER_TIP_MSG])."', function(){location.href='".$nextUrl."';return false;});}";
					echo "function onloadTip() {".($msg?"alert('".addslashes($msg)."');":'')."location.href='".$nextUrl."'}";						
				}
				
				//如果有转向，则不输出主体
				$outputBody = false;
			}
			else
			{
//					echo "function onloadTip(){Ext.Msg.alert('TPM', '".addslashes($tip[Watt_Controller_Action::HEADER_TIP_MSG])."');}";
				echo "function onloadTip() {".($msg?"alert('".addslashes($msg)."');":'')."}";
			}
			echo "window.onload = onloadTip;";				
			echo "</script>";
		}

		if( $outputBody )
		{
			//没有设置不显示主体

			//这里显示菜单 有true 是因为 菜单数据目前是在Watt_View_Helper_Menu里的
//			if( isset( $this->_header["menu"] ) )
//			{
//				//var_dump( $this->_header );
//				$menu = new Watt_View_Helper_Menu();
//				$menu->buildMenu( $this->_header["menu"] );
//				//$menu->buildMenu( null );
//			}

			/**
			 * 这里显示主体部分
			 */
			parent::render( true );
		}
		
		/**
		 * 这里显示底部
		 */
		//echo "Body Footer";
		include( $this->_getAbsViewPathFilename( "inc/footer.dailog.html.php" ) );
		
		$out = ob_get_clean();
		if( $show )
		{
			echo $out;
		}
		return $out;
	}
}
?>
