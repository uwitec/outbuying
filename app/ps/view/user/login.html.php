<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/header.inc.php' );
?>

<div>
	<form method="post" action="">
	登录:
	<dl><dt>用户名</dt><dd><input name="username"></dd>
	<dt>密码</dt><dd><input name="password" type="password"></dd></dl>
	<input name="op" type="submit" value="登录">
	</form>
</div>
<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/footer.inc.php' );
?>