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
	$content .= htmlecialchars(print_r($vars, TRUE));
	$content .= "\n</pre>\n";
    if(TRUE != $output) { return $content; }
    echo $content;
    return null;
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
 * Class  类实例化程序  提供自动载入类定义文件，实例化并返回对象句柄的功能
 * 
 * @param class_name    类名
 * @param args   类初始化时使用的参数，请以数组形式输入
 * @param dir    载入类定义文件的路径或文件
 */
function Class($class_name, $args = null, $dir = '')
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
		$GLOBALS['Global']["inst_class"][$class_name] = & new $class_name($args);
		return $GLOBALS['Global']["inst_class"][$class_name];
	}
	die("不能寻找到类定义: ".$class_name);
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
