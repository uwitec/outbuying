<?
include Pft_Config::getRootPath()."inc/view/header.inc.php";

/**
 * 功能：
 * 显示编辑 products 的界面
 * 
 * 输入：
 * $products
 * 
 * @author 
 */

//Pft_View_Helper_Form::buildFormWithDbData( $products );
extract( $products );
include( "_editform.html.php" );

include Pft_Config::getRootPath()."inc/view/footer.inc.php"
?>