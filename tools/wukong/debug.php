<?
/**
 * debug.php能否用 &debug 参数代替？
 * 否。debug.php是有意义的，因为在页面切换时，debug.php会一直存在，即系统一直运行在debug模式，debug参数则无法传递
 * @author terry
 * @version 0.1.0
 * Sat Nov 10 15:38:29 CST 2007
 */
/*是否开启Debug mode*/
define("DEBUG",true);

include 'index.php';