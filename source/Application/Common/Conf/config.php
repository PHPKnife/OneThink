<?php
return array(
		
	'LOAD_EXT_CONFIG'	 => 'db',
    'DEFAULT_MODULE'     => 'Admin',	
    'MODULE_DENY_LIST'   => array('Common','User','Install'),
    'MODULE_ALLOW_LIST'  => array('Home','Admin','Gzh'),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => '7w5fSD*Nz_9sg/d[E@361tI>L#OX]oJ^,A<4B:CZ', //默认数据加密KEY

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数
    
    
    'SMS_ERROR_CODE' => array( 	'SUCCESS' => '000000'),
);
