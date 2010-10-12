<?
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

//Watt_View_Helper_Form::buildFormWithDbData( $${var_name}, "", "", false );
extract( $${var_name} );
include( "_detailtable.html.php" );
?>