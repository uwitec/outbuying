<?
include Pft_Config::getRootPath()."inc/view/header.inc.php";

//detail.html.php
/**
 * 功能：
 * 显示 products 明细的界面
 * 
 * 输入：
 * $products
 * 
 * @author 
 */

//Pft_View_Helper_Form::buildFormWithDbData( $products, "", "", false );
extract( $products );
include( "_detailtable.html.php" );

include Pft_Config::getRootPath()."inc/view/footer.inc.php"
?>