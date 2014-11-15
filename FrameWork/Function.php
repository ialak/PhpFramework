<?php

/**
 * dump  格式化输出变量程序
 * 
 * @param vars    变量
 * @param output    是否将内容输出
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
 * Access  高速数据存取程序  正常情况下使用文件系统缓存
 * 
 * @param method    存取方向，取值"w"为存入数据，取值"r"读取数据
 * @param name    标识数据的名称
 * @param value    存入数据的值
 * @param life_time    变量的生存时间
 */
function Access($method, $name, $value = NULL, $life_time = -1)
{
	if('w' == $method){ // 写数据
		$file = $GLOBALS['Global']['_cache'].'/'.md5($name);
		$life_time = ( -1 == $life_time ) ? '300000000' : $life_time;
		$value = '<?php die();?>'.( time() + $life_time ).serialize($value);
		return file_put_contents($file, $value);
	}elseif('c' == $method){ // 清除数据
		$file = $GLOBALS['Global']['_cache'].'/'.md5($name);
		return @unlink($file);
	}else{ // 读数据
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
 * GetConfig  获取系统配置，亦可作为应用程序自定义配置的存取程序
 * 
 * @param vars    配置标识名
 */
function GetConfig($vars)
{
	return $GLOBALS['Global'][$vars];
}

/**
 * SetConfig  设置系统配置，亦可作为应用程序自定义配置的存取程序
 * 
 * @param vars    配置标识名，也可以是配置文件的路径
 * @param value    值
 */
function SetConfig($vars, $value = "")
{
	$GLOBALS['Global'][$vars] = $value;
}
