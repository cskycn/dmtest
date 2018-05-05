<?php

namespace Api\Controller;
use Think\Controller;
class PublicController extends Controller {
    
    //构造函数
    public function _initialize(){
	    //php 判断http还是https
    	$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://'; 
    	//所有图片路径
	    define(__DATAURL__, $http_type.$_SERVER['SERVER_NAME'].__DATA__.'/');
	    define(__PUBLICURL__, $http_type.$_SERVER['SERVER_NAME'].__PUBLIC__.'/');
		define(__HTTP__, $http_type);
		/*
		if(!is_weixin()){
			return false;
			exit();
		}*/
	}
	
	protected function errorMsg($code,$msg){
		if(is_integer($code) && $msg) {
			$arr = [];
			$arr['status'] = $code;
			$arr['msg'] = $msg;
			echo json_encode($arr);
			exit();
		}else{
			return false;
		}
	}
}