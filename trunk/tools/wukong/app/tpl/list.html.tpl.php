<?
//list.html.php
/**
 * 功能：
 * 显示 ${var_name} 列表的界面
 * 
 * 输入：
 * $${var_name}s
 * 
 * @author 
 */

?>
<div class="toolbar">
<a href="?do=${package_name}${ctrl_name}_add" class="btn add"><?=Pft_I18n::trans("${VAR_NAME}_ADD")?></a>
</div>
<?
//$${var_name}s_grid = new Pft_Util_Grid( $${var_name}s );
$${var_name}s_grid->addCol(" ","${pk_name}",true
                  ,'"<input type=checkbox value=\"".$row["${pk_name}"]."\">"');
${gridCols}
$${var_name}s_grid->addCol(Pft_I18n::trans("Opration"),"",true,'"<a href=\"?do=${package_name}${ctrl_name}_edit&${pk_name}=".$row["${pk_name}"]."\" class=\"btn\">".Pft_I18n::trans("EDIT")."</a> <a href=\"?do=${package_name}${ctrl_name}_delete&${pk_name}=".$row["${pk_name}"]."\" class=\"btn\" onclick=\"return confirm(\'".Pft_I18n::trans("CONFIRM_OPRATION")."\')\">".Pft_I18n::trans("DELETE")."</a>"');
                  
$${var_name}s_grid->showMe();
?>