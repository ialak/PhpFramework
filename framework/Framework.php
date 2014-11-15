<?php
//----------------------------------
//公共入口文件
//----------------------------------

// 记录开始运行时间

$GLOBALS['time_start'] = microtime(TRUE);

//开启SESSION
session_start();

// 定义系统路径
if(!defined('FM_PATH')) define('FM_PATH', dirname(__FILE__));
if(!defined('APP_PATH')) define('APP_PATH', dirname(FM_PATH).'/Home');

// 载入配置文件
if(isset($Config)){
	$GLOBALS['Global'] = array_merge(require(FM_PATH."/Config.php"),$Config);
}else{
	$GLOBALS['Global'] = require(FM_PATH."/Config.php");
}

/**
 * import  载入包含文件
 * 
 * @param file    需要载入的文件路径
 */
function import($file)
{
	if(isset($GLOBALS['Global']["import_file"][md5($file)]))return TRUE;
	if( is_readable($file) ){
		require($file);
		$GLOBALS['Global']['import_file'][md5($file)] = TRUE;
		return TRUE;
	}else{
		return FALSE;
	}
}

/**
 * Class  类实例化程序  提供自动载入类定义文件，实例化并返回对象句柄的功能
 * 
 * @param class_name    类名
 * @param args   类初始化时使用的参数，请以数组形式输入
 * @param dir    载入类定义文件的路径或文件
 */
function loadClass($class_name, $args = null, $dir = '')
{
	// 检查是否该类已经实例化，直接返回已实例对象，避免再次实例化
	if(isset($GLOBALS['Global']["inst_class"][$class_name])){
		return $GLOBALS['Global']["inst_class"][$class_name];
	}
	// 如果$dir不能读取，则测试是否仅路径
	if('' != $dir && !is_file($dir))$dir = $dir.'/'.$class_name.'.php';
	if('' != $dir && !import($dir))return FALSE;

	$has_define = FALSE;
	// 类定义存在
	if(class_exists($class_name, false) || interface_exists($class_name, false)){
		$has_define = TRUE;
	}else{
		if( TRUE == import($GLOBALS['Global']['model_path'].'/'.$class_name.'.php')){
			$has_define = TRUE;
		}
	}
	if(FALSE != $has_define){
		$GLOBALS['Global']["inst_class"][$class_name] = new $class_name($args);
		return $GLOBALS['Global']["inst_class"][$class_name];
	}
	die("不能寻找到类定义: ".$class_name);
}

// 载入核心MVC架构文件
import(FM_PATH."/Function.php");
import($GLOBALS['Global']['core_path']."/Actoin.php");
import($GLOBALS['Global']['core_path']."/Model.php");
import($GLOBALS['Global']['core_path']."/View.php");


// 根据配置文件进行一些全局变量的定义
if('debug' == $GLOBALS['Global']['mode']){
	define("DEBUG",TRUE); // 当前正在调试模式下
}else{
	define("DEBUG",FALSE); // 当前正在部署模式下
}

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