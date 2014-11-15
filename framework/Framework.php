<?php
//----------------------------------
//��������ļ�
//----------------------------------

// ��¼��ʼ����ʱ��

$GLOBALS['time_start'] = microtime(TRUE);

//����SESSION
session_start();

// ����ϵͳ·��
if(!defined('FM_PATH')) define('FM_PATH', dirname(__FILE__));
if(!defined('APP_PATH')) define('APP_PATH', dirname(FM_PATH).'/Home');

// ���������ļ�
if(isset($Config)){
	$GLOBALS['Global'] = array_merge(require(FM_PATH."/Config.php"),$Config);
}else{
	$GLOBALS['Global'] = require(FM_PATH."/Config.php");
}

/**
 * import  ��������ļ�
 * 
 * @param file    ��Ҫ������ļ�·��
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
 * Class  ��ʵ��������  �ṩ�Զ������ඨ���ļ���ʵ���������ض������Ĺ���
 * 
 * @param class_name    ����
 * @param args   ���ʼ��ʱʹ�õĲ���������������ʽ����
 * @param dir    �����ඨ���ļ���·�����ļ�
 */
function loadClass($class_name, $args = null, $dir = '')
{
	// ����Ƿ�����Ѿ�ʵ������ֱ�ӷ�����ʵ�����󣬱����ٴ�ʵ����
	if(isset($GLOBALS['Global']["inst_class"][$class_name])){
		return $GLOBALS['Global']["inst_class"][$class_name];
	}
	// ���$dir���ܶ�ȡ��������Ƿ��·��
	if('' != $dir && !is_file($dir))$dir = $dir.'/'.$class_name.'.php';
	if('' != $dir && !import($dir))return FALSE;

	$has_define = FALSE;
	// �ඨ�����
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
	die("����Ѱ�ҵ��ඨ��: ".$class_name);
}

// �������MVC�ܹ��ļ�
import(FM_PATH."/Function.php");
import($GLOBALS['Global']['core_path']."/Actoin.php");
import($GLOBALS['Global']['core_path']."/Model.php");
import($GLOBALS['Global']['core_path']."/View.php");


// ���������ļ�����һЩȫ�ֱ����Ķ���
if('debug' == $GLOBALS['Global']['mode']){
	define("DEBUG",TRUE); // ��ǰ���ڵ���ģʽ��
}else{
	define("DEBUG",FALSE); // ��ǰ���ڲ���ģʽ��
}

// ����ǵ���ģʽ���򿪾������
if (DEBUG) {
    error_reporting(error_reporting(0) & ~E_STRICT);
} else {
    error_reporting(0);
}


// ת���������ִ���û�������
$__controller = isset($_GET[$GLOBALS['Global']['url_controller']]) ? 
	$_GET[$GLOBALS['Global']['url_controller']] : 
	$GLOBALS['Global']['default_controller'];
$__action = isset($_GET[$GLOBALS['Global']['url_action']]) ? 
	$_GET[$GLOBALS['Global']['url_action']] : 
	$GLOBALS['Global']['default_action'];
$handle_controller = loadClass($__controller, null, $GLOBALS['Global']['controller_path'].'/'.$__controller.".php");
// ���ÿ�������������·�ɴ�������
if(!is_object($handle_controller) || !method_exists($handle_controller, $__action)){
	eval($GLOBALS['Global']['dispatcher_error']);
	exit;
}
// ������ͼ����
$handle_controller->v = loadClass('View');

// ִ���û�����
$handle_controller->$__action();