<?
include( Pft_Config::getCfg('PATH_INC').'view/header.inc.php' );

extract( $kw_keywords )

?>

<div>
比较关于 <b><?=$kws_words?></b> 的内容
</div>
<!--div><a href="?do=kw_content_addContent&kws_id=<?=$kws_id?>">我要增加</a></div-->
<div>
<?
if( is_array( $rel_contents ) ){
	foreach ($rel_contents as $content) {
?>
	<div class="content">
	<div class="button_bar"><?=$content["ct_adduser"]?$content["ct_adduser"]:'某人'?> 留笔于 <?=Pft_DateTime_Util::getTimeDiffString( time(), $content["created_at"] )?></div>
	<?=nl2br(h($content['ct_content']))?>
	<div class="button_bar">
		<span>(<?=$content['ct_agree']?>)人支持 (<?=$content['ct_oppose']?>)人反对</span>
		<a href="?do=kw_content_mark&ct_id=<?=$content['ct_id']?>&mk=3">精品</a>
		<a href="?do=kw_content_mark&ct_id=<?=$content['ct_id']?>&mk=1">有用</a>
		<a href="?do=kw_content_mark&ct_id=<?=$content['ct_id']?>&mk=-1">没用</a>
		<a href="?do=kw_content_mark&ct_id=<?=$content['ct_id']?>&mk=-3">垃圾</a>
	</div>
	</div>
		<?
	}
}else{
	?>
	<div class="content">暂时没有内容。</div>
	<?
}
?>
</div>
<?
include(dirname(__FILE__).'/../content/addForm.inc.php');

include( Pft_Config::getCfg('PATH_INC').'view/footer.inc.php' );
?>