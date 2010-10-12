<div class="toolbar">
<ul class="submenu">
<?
foreach ( $functions as $function )
{
	//$name => $do
	echo "<li><a href='?do=".$function["do"]."'>".$function["name"]."</a></li>";
}
?>
</ul>
<div class="cls">&nbsp;</div>
</div>