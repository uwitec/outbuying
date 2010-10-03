<?php
include dirname(__FILE__).'/../inc/header.inc.php';
?>
<div id="login_area">
<form method="post">
	<div><label for="user_name"><?=i18ntrans('用户名')?></label><input type="text" class="input" name="uname" id="user_name"/></div>
	<div><label for="password"><?=i18ntrans('密码')?></label><input type="password" class="input" name="pwd" id="password"/></div>
	<div><input type="submit"/></div>
</form>
</div>
<?php
include dirname(__FILE__).'/../inc/footer.inc.php';