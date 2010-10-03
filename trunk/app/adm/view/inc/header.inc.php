<?
if( !isset( $title ) ){
	$title = '方便快捷的订餐平台';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/pft.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=i18ntrans('后台管理')?>-<?=$title?></title>
<link href="css/default/admin.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.min.js"></script>
</head>
<body>
<div id="admin_top">
	<div id="admin_logo"></div>
</div>
<div id="admin_menu">
	<ul>
	<li><a href="?do=admin_index_index">首页</a></li>
	<li><a href="?do=ps_user_index">用户中心</a></li>
	<li>
		<a href="?do=ps_user_index">处理订单</a>
		<ul>
			<li><a href="#">当前订单</a></li>
			<li><a href="#">历史订单</a></li>
		</ul>
	</li>
	</ul>
</div>
<div id="admin_mainbody">