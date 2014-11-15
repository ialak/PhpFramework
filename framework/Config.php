<?php

return array(
	'mode' => 'debug', // ����ģʽ
	'core_path' => FM_PATH.'/Core',

	'default_controller' => 'main', // Ĭ�ϵĿ���������
	'default_action' => 'index',  // Ĭ�ϵĶ�������
	'url_controller' => 'c',  // ����ʱʹ�õĿ�����������ʶ
	'url_action' => 'a',  // ����ʱʹ�õĶ���������ʶ

	'controller_path' => APP_PATH.'/controller', // �û������������·������
	'model_path' => APP_PATH.'/model', // �û�ģ�ͳ����·������

	'inst_class' => array(), // ��ʵ������������
	'import_file' => array(), // �Ѿ�������ļ�

	'cache' => APP_PATH.'/tmp', // spAccess��ʱ�ļ���Ŀ¼
	
	'db' => array(  // ���ݿ���������
		'driver' => 'mysql',
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
		'database' => '',
		'prefix' => '',
	),
	'db_driver_path' => '/Db',
	
	'dispatcher_error' => "die('����������������!');" // ���崦��·�ɴ���ĺ���
);