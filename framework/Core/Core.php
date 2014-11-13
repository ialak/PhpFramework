<?php

/**
 * loadClass  ��ʵ��������  �ṩ�Զ������ඨ���ļ���ʵ���������ض������Ĺ���
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
		$GLOBALS['Global']["inst_class"][$class_name] = & new $class_name($args);
		return $GLOBALS['Global']["inst_class"][$class_name];
	}
	die("����Ѱ�ҵ��ඨ��: ".$class_name);
}