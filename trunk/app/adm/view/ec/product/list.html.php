<?
include Pft_Config::getRootPath()."inc/view/header.inc.php";

//list.html.php
/**
 * 功能：
 * 显示 products 列表的界面
 * 
 * 输入：
 * $productss
 * 
 * @author 
 */

?>
<div class="toolbar">
<a href="?do=adm_ec_product_add" class="btn add"><?=Pft_I18n::trans("PRODUCTS_ADD")?></a>
</div>
<?
//$productss_grid = new Pft_Util_Grid( $productss );
$productss_grid->addCol(" ","p_id",true
                  ,'"<input type=checkbox value=\"".$row["p_id"]."\">"');
$productss_grid->addCol(Pft_I18n::trans("p_id"),"p_id");
$productss_grid->addCol(Pft_I18n::trans("k_id"),"k_id");
$productss_grid->addCol(Pft_I18n::trans("p_name"),"p_name");
$productss_grid->addCol(Pft_I18n::trans("p_price"),"p_price");
$productss_grid->addCol(Pft_I18n::trans("p_info"),"p_info");
$productss_grid->addCol(Pft_I18n::trans("p_img_link"),"p_img_link");
$productss_grid->addCol(Pft_I18n::trans("p_unit"),"p_unit");
$productss_grid->addCol(Pft_I18n::trans("created_at"),"created_at");
$productss_grid->addCol(Pft_I18n::trans("updated_at"),"updated_at");
$productss_grid->addCol(Pft_I18n::trans("is_del"),"is_del");

$productss_grid->addCol(Pft_I18n::trans("Opration"),"",true,'"<a href=\"?do=adm_ec_product_edit&p_id=".$row["p_id"]."\" class=\"btn\">".Pft_I18n::trans("EDIT")."</a> <a href=\"?do=adm_ec_product_delete&p_id=".$row["p_id"]."\" class=\"btn\" onclick=\"return confirm(\'".Pft_I18n::trans("CONFIRM_OPRATION")."\')\">".Pft_I18n::trans("DELETE")."</a>"');
                  
$productss_grid->showMe();

include Pft_Config::getRootPath()."inc/view/footer.inc.php"
?>