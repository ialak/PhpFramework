<?php

//----------------------------------
//��������ļ�
//----------------------------------

// ��¼��ʼ����ʱ��

$GLOBALS['time_start'] = microtime(TRUE);

// ����ϵͳ·��
if(!defined('FM_PATH')) define('FM_PATH', dirname(__FILE__));
if(!defined('APP_PATH')) define('APP_PATH', dirname(FM_PATH).'Home');

// ���������ļ�
if(isset($Config)){
	$GLOBALS['Global'] = array_merge(require(FM_PATH."/Config.php"),$Config);
}else{
	$GLOBALS['Global'] = require(FM_PATH."/Config.php");
}

// �������MVC�ܹ��ļ�
import(FM_PATH."/Function.php");
import($GLOBALS['Global']['core_path']."/Core.php");
import($GLOBALS['Global']['core_path']."/Actoin.php");
import($GLOBALS['Global']['core_path']."/Model.php");
import($GLOBALS['Global']['core_path']."/View.php");

// ���������ļ�����һЩȫ�ֱ����Ķ���
if('debug' == $GLOBALS['Global']['mode']){
	define("DEBUG",TRUE); // ��ǰ���ڵ���ģʽ��
}else{
	define("DEBUG",FALSE); // ��ǰ���ڲ���ģʽ��
}

//����SESSION
session_start();

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