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
	$content .= htmlspecialchars(print_r($vars, TRUE));
	$content .= "\n</pre>\n";
    if(TRUE != $output) { return $content; }
    echo $content;
    return null;
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
