<?php
/**
 * @date 2014-11-04
 * @author tww <merry2014@vip.qq.com>
 */
if($_SERVER['DeveloperMode']){
	return array(
			# 数据库配置
			'DB_TYPE'   => 'mysqli', // 数据库类型
			'DB_PORT'   => '3306', // 端口
			'DB_PREFIX' => 'ad_', // 数据库表前缀
			'DB_HOST'   => '127.0.0.1', // 服务器地址
			'DB_NAME'   => 'myot', // 数据库名
			'DB_USER'   => 'root', // 用户名
			'DB_PWD'    => '',  // 密码
	
	);
}else{
	return array(
			# 数据库配置
			'DB_TYPE'   => 'mysqli', // 数据库类型
			'DB_PORT'   => '3306', // 端口
			'DB_PREFIX' => 'ad_', // 数据库表前缀
			'DB_HOST'   => '127.0.0.1', // 服务器地址
			'DB_NAME'   => 'myot', // 数据库名
			'DB_USER'   => 'root', // 用户名
			'DB_PWD'    => '',  // 密码
	
	);
}