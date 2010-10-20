<?
include Pft_Config::getRootPath()."inc/view/header.inc.php";

/**
 * 功能：
 * 显示编辑 ${var_name} 的界面
 * 
 * 输入：
 * $${var_name}
 * 
 * @author 
 */

//Pft_View_Helper_Form::buildFormWithDbData( $${var_name} );
extract( $${var_name} );
include( "_editform.html.php" );

include Pft_Config::getRootPath()."inc/view/footer.inc.php"
?>