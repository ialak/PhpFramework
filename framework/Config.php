<?php

return array(
	'mode' => 'debug', // 调试模式
	'core_path' => FM_PATH.'/Core',

	'default_controller' => 'main', // 默认的控制器名称
	'default_action' => 'index',  // 默认的动作名称
	'url_controller' => 'c',  // 请求时使用的控制器变量标识
	'url_action' => 'a',  // 请求时使用的动作变量标识

	'controller_path' => APP_PATH.'/controller', // 用户控制器程序的路径定义
	'model_path' => APP_PATH.'/model', // 用户模型程序的路径定义

	'inst_class' => array(), // 已实例化的类名称
	'import_file' => array(), // 已经载入的文件

	'cache' => APP_PATH.'/tmp', // spAccess临时文件夹目录
	
	'db' => array(  // 数据库连接配置
		'driver' => 'mysql',
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
		'database' => '',
		'prefix' => '',
	),
	'db_driver_path' => '/Db',
	
	'dispatcher_error' => "die('控制器或动作不存在!');" // 定义处理路由错误的函数
);