<?php

namespace Api\Controller;
use Think\Controller;
class TestController extends PublicController {
	//***************************
	//  首页数据接口
    //***************************
    
    public function index(){

    }

    

    //***************************
    //  获取这个test的详细信息
    //***************************
    public function getActivityTestInfo(){

        //接口默认返回码
        $statusCode = 500;
        $errorMsg = '提交信息有误';

        //======================
        //提交所需信息
        //======================
        $con = array();
        $con["id"] = intval(I('post.test_id'));


        if(!$con["id"]){
            return false;
        }

        if($res = M ('test')->where ($con)->find()){
            $statusCode = 200;
            echo json_encode(array('status'=> $statusCode, 'meta'=>$res));
            exit();
        }else{
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }   
    }

    //***************************
	//  获取对应活动的状态
    //***************************
    public function getStatus(){
		$aid = intval(I('post.activity_id'));
        $data['status'] = activity_to_status($aid);
        if($data['status']){
            echo json_encode(array('status'=> 200, 'meta'=>$res));
            exit();
        }else{
            echo json_encode(array('status'=> 301, 'msg'=>'获取状态失败'));
            exit();
        }
    }
    
    

}