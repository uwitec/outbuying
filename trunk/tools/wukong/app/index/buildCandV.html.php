<?php
include dirname(__FILE__).'/../../inc/header.inc.php';
?>
<?
/**
 * 功能：
 * 
 * 输入：
 * $functions
 * $inputForm
 * $inputFormDescc
 * 
 * @author Terry
 */
?>
<?
include( "_functionList.html.php" );
?>
<div>
<?
Pft_View_Helper_Form::buildFormWithDbData( $inputForm,"" ,"post" ,true , true, $inputFormDescc, 1 );
?>
</div>
<?php
include dirname(__FILE__).'/../../inc/footer.inc.php';