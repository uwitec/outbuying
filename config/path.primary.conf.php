<?
$cfg["PATH_ROOT"]		=	realpath( dirname(__FILE__)."/../" )."/";	//系统根目录
$cfg["PATH_CONFIG"]		=	$cfg["PATH_ROOT"]."config/";	//配置文件所在目录 这是不带站点的，具体的站点会在 Watt_Config 里增加上
//$cfg["PATH_CONFIG"]		=	realpath( dirname(__FILE__) ).'/';	//配置文件所在目录 设为当前目录 不要改动
$cfg["PATH_LIB"]		=	$cfg["PATH_ROOT"]."lib/";		//库/类所在目录
$cfg["PATH_APP"]		=	$cfg["PATH_ROOT"]."app/";		//应用（模块）所在目录
$cfg["PATH_VIEW"]		=	$cfg["PATH_ROOT"]."view/";		//View所在目录
$cfg["PATH_MODEL"]		=	$cfg["PATH_LIB"]."model/";		//OM 业务逻辑对象所在目录
$cfg["PATH_LANGUAGE"]	=	$cfg["PATH_ROOT"]."language/";	//语言文件所在目录
$cfg["PATH_LOG"]		=	$cfg["PATH_ROOT"]."log/";		//log文件所在目录
$cfg["PATH_INC"]		=	$cfg["PATH_ROOT"]."inc/";		//包含文件目录

$pos = strpos( $_SERVER["PHP_SELF"], 'htdocs/' );
if( $pos ){				// /~terry/trunk/htdocs/info.php
	$cfg["SITE_ROOT"]		=	substr( $_SERVER["PHP_SELF"], 0, $pos +7 );							//站点根目录
}else{
	$cfg["SITE_ROOT"]		=	"/";							//站点根目录
}

$cfg["SITE_INC"]		=	$cfg["SITE_ROOT"]."inc/";		//包含文件目录
$cfg["SITE_THEME"]		=	$cfg["SITE_ROOT"]."theme/";		//主题文件目录
$cfg["SITE_CHARSET"]	=	"utf-8";						//页面编码

$cfg["SITE_DOWNLOAD"]	=	"upload/";							//web下载文件的地址

$cfg["PATH_UPLOAD"]     =  $cfg["PATH_ROOT"]."htdocs/upload/";	//web上传文件的保存地址

return $cfg;
?>