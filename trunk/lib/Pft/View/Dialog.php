<?
/**
 * 本类实现渲染Model，模板加载，HTML的生成
 *
 * @author Terry
 * @package Pft_View
 */

class Pft_View_Dialog extends Pft_View{
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
 * 基础js函数
 */
?>
<!--日历选择时间时调用服务器时间 jute 20071122-->
<script src="<?=Pft_Config::getHttpHost()?>js/calendar/tpmsystemdate.php"></script>
<script src="<?=Pft_Config::getHttpHost()?>js/common.js"></script>
<script src="<?=Pft_Config::getHttpHost()?>js/john/ajax.js"></script>
<script src="<?=Pft_Config::getHttpHost()?>js/prototype_1_5_0.js"></script>
<!--ext-->
<!--link rel="stylesheet" type="text/css" href="<?=Pft_Config::getHttpHost()?>js/ext-1.0/resources/css/ext-all.css" /-->
<!-- GC -->
<?if( @$_REQUEST['ext-all'] ){?>
<!-- LIBS -->
<script type="text/javascript" src="<?=Pft_Config::getHttpHost()?>js/ext-1.0/adapter/yui/yui-utilities.js"></script>
<script type="text/javascript" src="<?=Pft_Config::getHttpHost()?>js/ext-1.0/adapter/yui/ext-yui-adapter.js"></script>
<!-- ENDLIBS -->
<script type="text/javascript" src="<?=Pft_Config::getHttpHost()?>js/ext-1.0/ext-all.js"></script>
<?}else{?>
<!--Used in Tpm-->
<!--script type="text/javascript" src="<?=Pft_Config::getHttpHost()?>js/ext.js"></script-->
<?}?>
<!--END Ext-->

<script src="<?=Pft_Config::getHttpHost()?>js/john/jsdialog/dialog.js"></script>
<link rel='stylesheet' href='./js/john/jsdialog/dialog.css'>
<script src="<?=Pft_Config::getHttpHost()?>js/john/sendMsg.js"></script>
<div id="popup_div_msg" style="position:absolute;display:none">
</div>
<?
		
		/**
		 * 在top显示loading
		 */
//		echo "<script>if(top.controlProgressBar)top.controlProgressBar();</script>";
		
		/**
		 * 这里显示 Tq消息
		 * Tq消息要在 tip 之前
		 */
		echo Tpm_Message_Sender_Tq::getMsgHtml();
		
		/**
		 * 处理在 ctrl 里设置的tip提示信息
		 */
		if( key_exists( Pft_Controller_Action::HEADER_TIP, $this->_header ) )
		{
			$tip = $this->_header[Pft_Controller_Action::HEADER_TIP];
			echo "<script>";
			if( $nextUrl = $tip[Pft_Controller_Action::HEADER_TIP_URL] )
			{
					$matchs = null;
					if( preg_match( "/^javascript:(.*)/", $nextUrl, $matchs ) ){
//						echo "function onloadTip(){Ext.Msg.alert('TPM', '".addslashes($tip[Pft_Controller_Action::HEADER_TIP_MSG])."', function(){{$matchs[1]};return false;});}";
						echo "function onloadTip() {top.alert('".addslashes($tip[Pft_Controller_Action::HEADER_TIP_MSG])."');{$matchs[1]}}";
					}else{
//						echo "function onloadTip(){Ext.Msg.alert('TPM', '".addslashes($tip[Pft_Controller_Action::HEADER_TIP_MSG])."', function(){location.href='".$nextUrl."';return false;});}";
						echo "function onloadTip() {top.alert('".addslashes($tip[Pft_Controller_Action::HEADER_TIP_MSG])."');location.href='".$nextUrl."'}";						
					}
				//如果有转向，则不输出主体
				$outputBody = false;
			}
			else
			{
//				echo "function onloadTip(){top.Ext.Msg.alert('TPM', '".addslashes($tip[Pft_Controller_Action::HEADER_TIP_MSG])."');}";
				echo "function onloadTip(){top.alert('".addslashes($tip[Pft_Controller_Action::HEADER_TIP_MSG])."');}";
			}
			echo "window.onload = onloadTip;";
			//echo "Ext.onready( onloadTip );";
			echo "</script>";
		}

		if( $outputBody )
		{
			//没有设置不显示主体

			//这里显示菜单 有true 是因为 菜单数据目前是在Pft_View_Helper_Menu里的
//			if( isset( $this->_header["menu"] ) )
//			{
//				//var_dump( $this->_header );
//				$menu = new Pft_View_Helper_Menu();
//				$menu->buildMenu( $this->_header["menu"] );
//				//$menu->buildMenu( null );
//			}

			/**
			 * 这里显示主体部分
			 */
			parent::render( true );
		}

		/**
		 * 结束loading
		 */
//		echo "<script>if(top.Element)top.Element.hide('floatProgress_backgroup')//top.controlProgressBar('1');</script>";
//		echo "<script>if(top.Element)top.Element.hide('floatProgress')//top.controlProgressBar('1');</script>";
		
		/**
		 * 这里显示底部
		 */
		//echo "Body Footer";
		//include( $this->_getAbsViewPathFilename( "inc/footer.dailog.html.php" ) );
		
		$out = ob_get_clean();
		if( $show )
		{
			echo $out;
		}
		return $out;
	}
}
?>
