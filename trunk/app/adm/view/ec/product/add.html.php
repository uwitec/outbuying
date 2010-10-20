<?
include Pft_Config::getRootPath()."inc/view/header.inc.php";

//add.html.php
/**
 * 功能：
 * 显示增加 products 的界面
 * 
 * 输入：
 * $products
 * 
 * @author 
 */

//Pft_View_Helper_Form::buildFormWithDbData( $tpm_products );
extract( $products );
include( "_editform.html.php" );

include Pft_Config::getRootPath()."inc/view/footer.inc.php"
?>