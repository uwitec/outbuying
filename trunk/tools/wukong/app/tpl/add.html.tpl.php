<?
//add.html.php
/**
 * 功能：
 * 显示增加 ${var_name} 的界面
 * 
 * 输入：
 * $${var_name}
 * 
 * @author 
 */

//Pft_View_Helper_Form::buildFormWithDbData( $tpm_${var_name} );
extract( $${var_name} );
include( "_editform.html.php" );
?>