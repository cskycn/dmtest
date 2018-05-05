<?php

namespace Api\Controller;
use Think\Controller;
class DetailController extends PublicController {
	//***************************
	//  访问检测详情页面
	//***************************
    public function index(){

        //======================
        //获取检测信息
        //======================
        $testID = intval($_REQUEST['test_id']);
        $userID = intval($_REQUEST['user_id']);

        $status = checkActivityStatus($testID, $userID);

        if(!$status) {
            $status = creatNewActivity($testID, $userID);
        }

        $status = is_array($status)? $status : 
        array("status" => 1, 
              "id" => 0, 
              "user_id" => 0,
              "user_name" => '',
              "phone" => '',
              "province" => '',
              "city" => '',
              "area" => '',
              "address" => '',
              "room_number" => 0,
              "order_time" => 0,
              "info" => '');

        //  status 0.错误 1.未开始 2.有好友助力 3.完成助力 4.完成提交检测需求 5.可查看报告 
        switch ($status["status"]){
            case 2:
                $url = "../friend-help/friend-help";
                break;

             case 3:
                $url = "../booking-detail/booking-detail";
                break;

            case 5:
                $url = "../Test/seeReport";
                break;

            default:
                $url = "../detail/detail";
        }
        echo json_encode(array('status'=> 200, 'meta' =>array(
                                               'url'=>$url,
                                               'user_id'=>$userID,
                                               'test_id'=>$testID,
                                               'activity_id'=>$status["id"],
                                               'ownner_id'=>$status["user_id"],
                                               'user_name' => $status["user_name"],
                                               'phone' => $status["phone"],
                                               'province' => $status["province"],
                                               'city' => $status["city"],
                                               'area' => $status["area"],
                                               'address' => $status["address"],
                                               'room_number' => $status["room_number"],
                                               'order_time' => $status["order_time"],
                                               'info' => $status["info"])));
        exit();       
    }

    //***************************
	//  查看检测详情页
    //***************************
    public function getTestInfo(){
        $testID = intval($_REQUEST['test_id']);
        //======================
        //获取检测信息
        //======================
        $detail = $this->getTestDetail($testID);
        
        if($detail){
            $statusCode = 200;
            echo json_encode(array('status'=> $statusCode, 'meta' =>array('detail'=>$detail)));
        }else{
            $this->errorMsg($statusCode = 500,$errorMsg = "获取检测失败");
        }    	
    	exit();
    }



    //***************************
    //  获取测试详情
    //***************************
    private function getTestDetail($id){
        if(!$id){
            return false;
        }
        $con = array();
        $con['id'] = $id;
        $detail = M ('test')->where ($con)->find();
        return $detail;
    }

}