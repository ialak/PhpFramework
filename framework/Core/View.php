<?php

/**
 * View 基础视图类
 */
class View {
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		if(FALSE != $GLOBALS['Global']['view']['auto_ob_start'])ob_start();
	}
}

