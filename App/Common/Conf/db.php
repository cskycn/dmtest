<?php
return array(
	//'配置项'=>'配置值'
	
// +----------------------------------------------------------------------
// | 数据库配置设定
// +----------------------------------------------------------------------		
	'DB_TYPE'               =>  'mysql',        // 数据库类型
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'dm_',       // 数据库表前缀   ！开发时配置常量 ！
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8		
// +----------------------------------------------------------------------
// | 弹性WEB服务器和数据库
// +----------------------------------------------------------------------
/*
  'DB_HOST'               =>  '172.21.0.12', // 服务器地址   127.0.0.1
  'DB_NAME'               =>  'dingma',          // 数据库名
  'DB_USER'               =>  'root',      // 用户名
  'DB_PWD'                =>  'dingma123',//'1234QWERasdf',          // 密码
  */
// +----------------------------------------------------------------------
// | 本地测试服务器和数据库
// +----------------------------------------------------------------------  	 
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'dingma',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '123456789',      //'1234QWERasdf',          // 密码

);