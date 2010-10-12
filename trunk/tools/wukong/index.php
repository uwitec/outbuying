<?
define( 'APP_START_TIME', microtime( true ) );

/**
 * @author terry
 * @version 1.0.1
 */

/*是否开启Debug mode*/
if( !defined("DEBUG") )define("DEBUG",false);

define("TQ_33",true);			//很重要，如不正确，消息会乱

ini_set("memory_limit","16M");	//提高内存限制
//ini_set("memory_limit","-1");	//提高内存限制
set_magic_quotes_runtime(0);	//关闭魔术引号

if( defined("DEBUG") && DEBUG ){
	//error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors', 'on');
	error_reporting( E_ALL );
}else{
	//error_reporting( E_ERROR );
	ini_set('display_errors', 'off');
	error_reporting( E_NONE );
}

//===重新设置SessionId=====================
$post_sid	= @$_POST[session_name()];
$get_sid	= @$_GET[session_name()];
//$cookie_sid	= @$_COOKIE[session_name()];
/**
 * 设定 session_id 获取的优先级
 * @todo 将 session_id 写入浏览器 cookie
 */
if( $post_sid ){
	session_id($post_sid);
}elseif ( $get_sid ){
	session_id($get_sid);
}
//-------------------

//session_cache_limiter('private');	//后退后保持上一次form的输入值	//很多情况下又引起数据不应该保留的保留，暂时先取消了
session_start();

//去除魔术引号 - 效率不高，应从服务器设置上处理
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value){
        $value = is_array($value) ?
                    array_map('stripslashes_deep', $value) :
                    stripslashes($value);

        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}

//========================================

/**
 * 载入应用程序配置
 */
$config_path = dirname(__FILE__)."/../../config/";
//========================================

//===多站点支持=====================================
//为了安全，防止越权访问其他站点，site 在第一次产生会话时启用，每次只有在第一次开启浏览器时才生效
define( 'MULTI_SITE_SESSION_NAME', 'TPM_MULTI_SITE' );
//Pft_Config 中用到 $_SESSION[MULTI_SITE_SESSION_NAME]
if( isset( $_SESSION[MULTI_SITE_SESSION_NAME] ) ){
	$site = $_SESSION[MULTI_SITE_SESSION_NAME];
}else{
	$site = @$_REQUEST['site'];
	if( !$site )$site = $_SERVER['HTTP_HOST'];
	if( !is_dir( $config_path.$site ) )$site = 'default';
	$_SESSION[MULTI_SITE_SESSION_NAME] = $site;
}

require_once $config_path."/loader.php";

Pft_Config::setCfg('PATH_APP', Pft_Config::getCfg('PATH_APP'), 1);
Pft_Config::setCfg('PATH_APP', dirname(__FILE__).'/app/');

//========================================
/**
 * 环境准备完毕 程序开始
 */
Pft_Debug::addInfoToDefault('Pre dispatch.');
try{
	/**
	 * 分发
	 */
	$dispatch = new Pft_Dispatcher();
	$dispatch->dispatch();
}catch (Exception $e){
	$code = $e->getCode();
	$errorInfo = $e->getMessage() . " at (" . $e->getFile() . " | Line:" . $e->getLine() . ") Code [".$code."]";
	Pft_Log::addLog( 'Cache exception : '.$errorInfo );
	if( defined('DEBUG') && DEBUG ){
		echo "<pre>".$errorInfo."</pre>";
		echo "<pre>".debug_print_backtrace()."</pre>";
		//todo: 此处转入debug 错误代码处理
		switch ( $code ){

		}
	}else{
		$_REQUEST['msg']  = $e->getMessage();
		$_REQUEST['code'] = $code;
		include('error.php');
		//header( "Location:?do=error&code=$code&msg=".urlencode($e->getMessage()) );
		//header( "Location:?do=error&code=$code&msg=".$e->getMessage() );
	}
}
Pft_Debug::addInfoToDefault('After dispatch.');

if( defined("DEBUG") && DEBUG ){
	/**
	 * 程序结束 输出调试信息
	 */
	Pft_Debug::getDefaultDebug()->output();
}

/**
 * Record Process Time
 * @author terry
 * @version 0.1.0
 * Sat Aug 04 13:54:36 CST 2007
 */
$endTime = microtime( true );
$processTimeLogger = new Pft_Log_File( 'processTime' );
$processTimeLogger->log( ( $endTime - APP_START_TIME ) * 1000, 0, 'ProcessTimeAt MS', "", "", @$_REQUEST['do'] );