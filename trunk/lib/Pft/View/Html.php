<?
/**
 * 本类实现渲染Model，模板加载，HTML的生成
 *
 * @author Terry
 * @package Pft_View
 */

class Pft_View_Html extends Pft_View{
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
		$haveHeaderInfo = ($this->_header && is_array( $this->_header ));
		
		/**
		 * 是否输出主体,默认输出
		 */
		$outputBody = true;
		
		//ob_start();
		//echo "Html Header";
		
		//echo "Body Header";
		if( $haveHeaderInfo ){
			//这里展开了 sys_title 和 tpm_css
			extract( $this->_header );
		}

		/*
		if( !isset( $sys_title ) || $sys_title=="" ) $sys_title = "TPM";
		$sys_title .= "[".Pft_Session::getSession()->getRoleName().".".Pft_Session::getSession()->getUserName()."]";
		
		include( $this->_getAbsViewPathFilename( "inc/header.html.php" ) );
		*/

/**
 * 基础js函数
 */

		if( $haveHeaderInfo )
		{
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
//					echo "function onloadTip(){Ext.Msg.alert('TPM', '".addslashes($tip[Pft_Controller_Action::HEADER_TIP_MSG])."');}";
					echo "function onloadTip() {top.alert('".addslashes($tip[Pft_Controller_Action::HEADER_TIP_MSG])."');}";
				}
				echo "window.onload = onloadTip;";				
				echo "</script>";
			}
		}

		if( $outputBody )
		{
			//没有设置不显示主体
			//这里显示菜单 有true 是因为 菜单数据目前是在Pft_View_Helper_Menu里的
			if( isset( $this->_header["menu"] ) )
			{
				//var_dump( $this->_header );
				$menu = new Pft_View_Helper_Menu();
				$menu->buildMenu( $this->_header["menu"] );
			}

			/**
			 * 这里显示主体部分
			 */
			parent::render( true );
		}

//</div>
//<!--End Of Mainbody-->
		
		/**
		 * 这里显示底部
		 */
		//include( $this->_getAbsViewPathFilename( "inc/footer.html.php" ) );
		
//		$out = ob_get_clean();
//		if( $show )
//		{
//			echo $out;
//		}
//		return $out;
	}
}
?>
