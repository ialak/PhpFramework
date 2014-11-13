<?php

/**
 * dump  ��ʽ�������������
 * 
 * @param vars    ����
 * @param output    �Ƿ��������
 */
function dump($vars, $output = TRUE)
{
	$content = "<pre>\n";
	$content .= htmlecialchars(print_r($vars, TRUE));
	$content .= "\n</pre>\n";
    if(TRUE != $output) { return $content; }
    echo $content;
    return null;
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
 * Access  �������ݴ�ȡ����  ���������ʹ���ļ�ϵͳ����
 * 
 * @param method    ��ȡ����ȡֵ"w"Ϊ�������ݣ�ȡֵ"r"��ȡ����
 * @param name    ��ʶ���ݵ�����
 * @param value    �������ݵ�ֵ
 * @param life_time    ����������ʱ��
 */
function Access($method, $name, $value = NULL, $life_time = -1)
{
	if('w' == $method){ // д����
		$file = $GLOBALS['Global']['_cache'].'/'.md5($name);
		$life_time = ( -1 == $life_time ) ? '300000000' : $life_time;
		$value = '<?php die();?>'.( time() + $life_time ).serialize($value);
		return file_put_contents($file, $value);
	}elseif('c' == $method){ // �������
		$file = $GLOBALS['Global']['_cache'].'/'.md5($name);
		return @unlink($file);
	}else{ // ������
		$file = $GLOBALS['Global']['_cache'].'/'.md5($name);
		if( !is_readable($file) )return FALSE;
		$arg_data = file_get_contents($file);
		if( substr($arg_data, 14, 11) < time() ){
			Access('c', $name);
			return FALSE;
		}
		return unserialize($arg_data, 25);
	}
}

/**
 * Class  ��ʵ��������  �ṩ�Զ������ඨ���ļ���ʵ���������ض������Ĺ���
 * 
 * @param class_name    ����
 * @param args   ���ʼ��ʱʹ�õĲ���������������ʽ����
 * @param dir    �����ඨ���ļ���·�����ļ�
 */
function Class($class_name, $args = null, $dir = '')
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

/**
 * GetConfig  ��ȡϵͳ���ã������ΪӦ�ó����Զ������õĴ�ȡ����
 * 
 * @param vars    ���ñ�ʶ��
 */
function GetConfig($vars)
{
	return $GLOBALS['Global'][$vars];
}

/**
 * SetConfig  ����ϵͳ���ã������ΪӦ�ó����Զ������õĴ�ȡ����
 * 
 * @param vars    ���ñ�ʶ����Ҳ�����������ļ���·��
 * @param value    ֵ
 */
function SetConfig($vars, $value = "")
{
	$GLOBALS['Global'][$vars] = $value;
}
