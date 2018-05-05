<?php

namespace Api\Controller;
use Think\Controller;
class LogisticalController extends PublicController {
	//***************************
	//  首页数据接口
	//***************************
    public function index(){
    	exit();
    }

    //***************************
    //  获取用户留言信息
    //***************************
    public function getStatus(){
        //这里需要做管理员权限判断
        if(1==1){
            $res = $this->getAllStatus();
            $statusCode = 200;
            echo json_encode(array('status'=> $statusCode, 'meta'=>$res));
        }else{
            $statusCode = 301;
            $errorMsg = '没有权限查看信息';
            $this->errorMsg($statusCode,$errorMsg);
        }
        exit();
    }

    //***************************
    //  获取用户留言对应的信息
    //***************************
    private function getAllStatus($user_id = false){
        if(!$user_id){
            //获取全部留言
            $hander = M('logistical')->select();
        }else{
            //只获取对应用户的留言
            $con["user_id"] = $user_id;
            $hander = M('logistical')->where($con)->select();
        }
        return $hander;
    }
}