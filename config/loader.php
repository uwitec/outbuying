<?
//session_start();
//session_start放到了 dispatcher.php 里
/**
 * 载入config文件
 * 初始化运行环境
 *
 * 有可能在开发者环境下设置了如下参数
 * MULTI_SYSPATH_MODE
 * $primaryPathCfgFile		带路径的文件名
 * $secondaryPathCfgFile	带路径的文件名
 *
 * @author Yan
 * @package 
 * @version 1.4
 */
if( !defined("MULTI_SYSPATH_MODE") ){
	define( "MULTI_SYSPATH_MODE", true );
}

define('WINDOWS', (substr(PHP_OS, 0, 3) == 'WIN'));
if( WINDOWS ){
	define( "INC_SPLIT",";" );
}else{
	define( "INC_SPLIT",":" );
}

/**
 * 载入配置文件
 * 建立运行环境
 *
 */
if( !isset( $primaryPathCfgFile ) ) $primaryPathCfgFile   =  "path.primary.conf.php";	//默认文件是同一目录下的config.conf.php
$cfg_pri = include( $primaryPathCfgFile );
$server_cfg = include( $_SESSION[MULTI_SITE_SESSION_NAME]."/server.conf.php" );
if( $server_cfg ){
	$cfg_pri = array_merge( $cfg_pri, $server_cfg );	
}

if( defined( "MULTI_SYSPATH_MODE" ) && MULTI_SYSPATH_MODE ){
	if( !isset( $secondaryPathCfgFile ) ) $secondaryPathCfgFile =  dirname(__FILE__) . "/path.secondary.conf.php";	

	$arr_path_model = array();
	$arr_path_model[] = $cfg_pri["PATH_MODEL"];	

	$arr_path_lib = array();
	$arr_path_lib[] = $cfg_pri["PATH_LIB"];

//  路径
	if( file_exists( $secondaryPathCfgFile ) )
	{
		$cfg_sec = include( $secondaryPathCfgFile );
		//var_export( $cfg_sec );
		
		$arr_path_model[] = $cfg_sec["PATH_MODEL"];
		$arr_path_model = array_unique( $arr_path_model );

		$arr_path_lib[] = $cfg_sec["PATH_LIB"];
		$arr_path_lib = array_unique( $arr_path_lib );

	}

	set_include_path(implode(INC_SPLIT,$arr_path_model).INC_SPLIT.implode(INC_SPLIT,$arr_path_lib).INC_SPLIT.get_include_path());
	//set_include_path( $cfg["PATH_LIB"].INC_SPLIT.PATH_LIB."model/" );	
}
else
{
	set_include_path($cfg_pri["PATH_LIB"].INC_SPLIT.$cfg_pri["PATH_MODEL"].INC_SPLIT.get_include_path());	
}

//echo get_include_path();

/**
 * 基本的环境管理和类管理 需PHP5
 */
require_once 'Pft.php';
//========================================
/**
* 实现在创建对象时，自动加载类定义，即不用include Class文件 Only PHP5
*/
function __autoload($class){Pft::loadClass($class);/*Pft::loadclass2($class);*/}

Pft_Config::setPrimaryConfig( $cfg_pri );
if( defined( "MULTI_SYSPATH_MODE" ) && isset( $cfg_sec ) ){
	Pft_Config::setSecondaryConfig( $cfg_sec );
}