<?
include Pft_Config::getRootPath()."inc/view/header.inc.php";

//detail.html.php
/**
 * 功能：
 * 显示 ${var_name} 明细的界面
 * 
 * 输入：
 * $${var_name}
 * 
 * @author 
 */

//Pft_View_Helper_Form::buildFormWithDbData( $${var_name}, "", "", false );
extract( $${var_name} );
include( "_detailtable.html.php" );

include Pft_Config::getRootPath()."inc/view/footer.inc.php"
?>