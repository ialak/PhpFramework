<?php

//----------------------------------
//公共入口文件
//----------------------------------

// 记录开始运行时间

$GLOBALS['time_start'] = microtime(TRUE);

// 定义系统路径
if(!defined('FM_PATH')) define('FM_PATH', dirname(__FILE__));
if(!defined('APP_PATH')) define('APP_PATH', dirname(FM_PATH).'Home');

// 载入配置文件
if(isset($Config)){
	$GLOBALS['Global'] = array_merge(require(FM_PATH."/Config.php"),$Config);
}else{
	$GLOBALS['Global'] = require(FM_PATH."/Config.php");
}

// 载入核心MVC架构文件
import(FM_PATH."/Function.php");
import($GLOBALS['Global']['core_path']."/Core.php");
import($GLOBALS['Global']['core_path']."/Actoin.php");
import($GLOBALS['Global']['core_path']."/Model.php");
import($GLOBALS['Global']['core_path']."/View.php");

// 根据配置文件进行一些全局变量的定义
if('debug' == $GLOBALS['Global']['mode']){
	define("DEBUG",TRUE); // 当前正在调试模式下
}else{
	define("DEBUG",FALSE); // 当前正在部署模式下
}

//开启SESSION
session_start();

// 如果是调试模式，打开警告输出
if (DEBUG) {
    error_reporting(error_reporting(0) & ~E_STRICT);
} else {
    error_reporting(0);
}


// 转向控制器，执行用户级代码
$__controller = isset($_GET[$GLOBALS['Global']['url_controller']]) ? 
	$_GET[$GLOBALS['Global']['url_controller']] : 
	$GLOBALS['Global']['default_controller'];
$__action = isset($_GET[$GLOBALS['Global']['url_action']]) ? 
	$_GET[$GLOBALS['Global']['url_action']] : 
	$GLOBALS['Global']['default_action'];
$handle_controller = loadClass($__controller, null, $GLOBALS['Global']['controller_path'].'/'.$__controller.".php");
// 调用控制器出错将调用路由错误处理函数
if(!is_object($handle_controller) || !method_exists($handle_controller, $__action)){
	eval($GLOBALS['Global']['dispatcher_error']);
	exit;
}
// 加载视图对象
$handle_controller->v = loadClass('View');

// 执行用户代码
$handle_controller->$__action();