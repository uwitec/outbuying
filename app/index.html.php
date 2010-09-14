<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/header.inc.php' );
?>

<div>
<form action="?do=kw_kw_search" method="POST">
<input type="text" name="s">
<input type="submit">
</form>
</div>
<div class="keywords_list">
	<h1>Hello</h1>
	<div class="cls"></div>
</div>
<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/footer.inc.php' );
?>