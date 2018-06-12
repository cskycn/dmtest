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
        $testID = intval(I('request.test_id'));
        $userID = I('request.user_id');
        $orderType = (int)I('request.order_type');

        //echo strlen(trim($userID));
        if($userID == '' || strlen($userID)!= 28 || $testID == 0){
            exit();
        }

        $status = checkActivityStatus($testID, $userID,$orderType);

        if(!$status["id"]) {
            $status = creatNewActivity($testID, $userID,$orderType);
        }

        $options = array();

        //  status 0.错误 1.未开始 2.有好友助力 3.完成助力 4.完成提交检测需求 5.可查看报告 
        switch ($status["status"]){
            case 2:
                $url = "../friend-help/friend-help";
                break;

             case 3:
                $url = "../booking-detail/booking-detail";
                $options = array('need_form'=>C("TEST_SUBMIT_FORM")[$testID]);
                break;

            case 4:
                $url = "../wait-result/wait-result";
                break;

            case 5:
                $url = "../result-detail/result-detail";
                break;

            default:
                $url = "../friend-help/friend-help";
        }
        echo json_encode(array('status'=> 200, 'meta' =>array(
                                               'url'=>$url,
                                               'open_id'=>$userID,
                                               'test_id'=>$testID,
                                               'activity_id'=>$status["id"],//////////
                                               'ownner_id'=>$status["user_id"],//////////
                                               'user_name' => $status["user_name"],
                                               'phone' => $status["phone"],
                                               'province' => $status["province"],
                                               'city' => $status["city"],
                                               'area' => $status["area"],
                                               'address' => $status["address"],
                                               'room_number' => $status["room_number"],
                                               'order_time' => $status["order_time"],
                                               'info' => $status["info"],
                                               'options'=>$options)));
        exit();       
    }

    //***************************
	//  查看检测详情页
    //***************************
    public function getTestInfo(){
        $testID = intval(I('request.test_id'));
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


    //***************************
	//  提交检测数据接口
    //***************************
    public function submitActivity(){

        //接口默认返回码
        $statusCode = 500;
        $errorMsg = '提交信息有误';

        $submit = $_REQUEST["submit"];
        $testID  = I('request.test_id');

        $submit["user_id"] = trim($submit['userid']);
        $submit["test_id"] = intval($submit['testid']);
        $submit["activity_id"] = intval($submit['activity_id']);

        if($submit["user_id"] == '' || $submit["test_id"] == 0 ){//|| !checkSubmitForm($submit)){
            $statusCode = 301;
            $errorMsg = '预约信息不对';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        if($this->checkResultExisted($submit["activity_id"])){
            $statusCode = 302;
            $errorMsg = '你已提交过';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        if($this->checkSmsCode($submit["phone"],$submit["code"])){
            $statusCode = 304;
            $errorMsg = '短信验证码不对';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        if($testID){
            $needFiledsArray = C("TEST_SUBMIT_FORM")[$testID];
            if(in_array('user_name',$needFiledsArray)){
                $submit["user_name"] = trim($submit['username']);
                if(!checkUserName($submit)){
                    $statusCode = 301;
                    $errorMsg = '预约信息不对';
                    $this->errorMsg($statusCode,$errorMsg);
                    exit();
                }
            }

            if(in_array('phone',$needFiledsArray)){
                $submit["phone"] = trim($submit['phone']);
                if(!checkPhone($submit)){
                    $statusCode = 301;
                    $errorMsg = '预约信息不对';
                    $this->errorMsg($statusCode,$errorMsg);
                    exit();
                }
            }

            if(in_array('address',$needFiledsArray)){
                $submit["address"] = trim($submit['address']);
                $region = trim($submit['region']);
                if(is_array($region) && count($region) == 3){
                    $submit["province"] = region[0];
                    $submit["city"] = region[1];
                    $submit["area"] = region[2];
                }else{
                    $statusCode = 301;
                    $errorMsg = '预约信息不对';
                    $this->errorMsg($statusCode,$errorMsg);
                    exit();
                }
                if(!checkLocation($submit)){
                    $statusCode = 301;
                    $errorMsg = '预约信息不对';
                    $this->errorMsg($statusCode,$errorMsg);
                    exit();
                }
            }

            if(in_array('room_number',$needFiledsArray)){
                $submit["room_number"] = trim($submit['room_number']);
                if(!checkRoomNumber($submit)){
                    $statusCode = 301;
                    $errorMsg = '预约信息不对';
                    $this->errorMsg($statusCode,$errorMsg);
                    exit();
                }
            }

            if(in_array('ordertime',$needFiledsArray)){
                $order_time = trim($submit['ordertime']);
                $submit["order_time"] = checkOrderTime($order_time);
                if($submit["order_time"]){
                    $statusCode = 301;
                    $errorMsg = '预约信息不对';
                    $this->errorMsg($statusCode,$errorMsg);
                    exit();
                }
            }

            if(in_array('objects',$needFiledsArray)){
                $submit["objects"] = trim($submit['objects']);
                if($submit["objects"] == ''){
                    $statusCode = 301;
                    $errorMsg = '预约信息不对';
                    $this->errorMsg($statusCode,$errorMsg);
                    exit();
                }
            }

            if(in_array('object_num',$needFiledsArray)){
                $submit["object_num"] = trim($submit['object_num']);
                if(!is_int($submit["object_num"]) || !$submit["object_num"] > 0){
                    $statusCode = 301;
                    $errorMsg = '预约信息不对';
                    $this->errorMsg($statusCode,$errorMsg);
                    exit();
                }
            }
        }
        

        if($this->doUserSubmit($submit)){
            $statusCode = 200;
            $errorMsg = "提交检测成功";
            echo json_encode(array('status'=> $statusCode, 'msg'=>$errorMsg));    
        }else{
            $statusCode = 303;
            $errorMsg = '未完成好友助力';
            $this->errorMsg($statusCode,$errorMsg);
        }
        exit();
    }

    //***************************
	//  写入数据表单
    //***************************
    protected function doUserSubmit($submit){
        $hander = M('activity');
        $con['activity_id'] = $submit['activity_id'];
        if($res = $hander->where($con)->find()){
            if($res["status"] == 3){
                $data["user_name"] = $submit["user_name"];
                $data["phone"] = $submit["phone"];
                $data["province"] = $submit["province"];
                $data["city"] = $submit["city"];
                $data["area"] = $submit["area"];
                $data["address"] = $submit["address"];
                $data["room_number"] = $submit["room_number"];
                $data["order_time"] = $submit["order_time"];
                $data["objects"] = $submit["objects"];
                $data["object_num"] = $submit["object_num"];
                $data["info"] = $submit["info"];
                $data["status"] = 4;
                if($hander->where($con)->save($data)){
                    //将user_id、activity_id、test_id传入，新生成一个物流订单
                    if($this->creatLogisticalInfo($data["user_id"],$data["activity_id"],$data["test_id"])){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    //用户提交信息后生成一个物流信息
    protected function creatLogisticalInfo($user_id,$activity_id, $test_id){
        $hander = M('logistical');
        $data["user_id"] = $user_id;
        $data["activity_id"] = $activity_id;
        $data["test_id"] = $test_id;
        if(!$hander ->add($data)){
            return false;
        }
    }
    //***************************
	//  提交检测结果报告
    //***************************
/*    public function submitResult(){

        //接口默认返回码
        $statusCode = 500;
        $errorMsg = '提交的信息有误';

        //======================
        //提交所需信息
        //======================
        $submit["user_id"] = intval($_POST['userid']);
        $submit["test_id"] = intval($_POST['testid']);

        $submit["submit_rooms"] = $_POST['submitrooms'];
        $submit["upload_img_url"] = $_POST['uploadimgurl'];
        $submit["decorate_time"] = trim($_POST['decoratetime']);
        $submit["message"] = trim($_POST['info']);

        //======================
        //判断信息是否合法
        //======================

        if($submit["user_id"] == '' || $submit["test_id"] == '' || !checkSubmitResult($submit)){
            $statusCode = 301;
            $errorMsg = '提交的检测结果信息不合法';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        if($this->checkResultExisted($submit["user_id"],$submit["test_id"])){
            $statusCode = 302;
            $errorMsg = '你已经提交过这个检测结果的信息';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }


        $statusCode = 200;
        $errorMsg = "提交检测结果成功";
        echo json_encode(array('status'=> $statusCode, 'msg'=>$errorMsg));
        exit();
    }
*/

    //***************************
	//  查看报告
    //***************************
    public function seeReport(){
        if(isset($_REQUEST['result_id'])){
            $result_id = I('request.result_id');
            if($result_id == ''){
                $statusCode = 301;
                $errorMsg = '结果信息不合法';
                $this->errorMsg($statusCode,$errorMsg);
                exit();
            }
            $res = M()->table('dm_test a,dm_result b')
				  ->where('a.id = b.test_id AND b.id = \''.$result_id.'\'' )
                  ->field('a.name,b.test_1,b.test_2,b.test_3,b.test_4,b.test_5
                          ,b.result_1,b.result_2,b.result_3,b.result_4,b.result_5
                          ,b.upload_img_urls,b.decorate_time')//status,a.name,b.send_id,b.update_time,b.status')
                  ->find(); 
        }else if(isset($_REQUEST['activity_id'])){
            $activity_id = I('request.activity_id');
            if($activity_id == ''){
                $statusCode = 301;
                $errorMsg = '结果信息不合法';
                $this->errorMsg($statusCode,$errorMsg);
                exit();
            }
            $res = M()->table('dm_test a,dm_result b')
				  ->where('a.id = b.test_id AND b.activity_id = \''.$activity_id.'\'' )
                  ->field('a.name,b.test_1,b.test_2,b.test_3,b.test_4,b.test_5
                          ,b.result_1,b.result_2,b.result_3,b.result_4,b.result_5
                          ,b.upload_img_urls,b.decorate_time')//status,a.name,b.send_id,b.update_time,b.status')
                  ->find(); 
        }

        if($res){

            if(trim($res["upload_img_urls"] != '')){
                
                $res["upload_img_urls"] = explode(",",$res["upload_img_urls"]);
            }

           // print_r($res);
            $statusCode = 200;
            echo json_encode(array('status'=> 200, 'meta' =>$res));
            exit();
        }else{
            $statusCode = 302;
            $errorMsg = '没有找到报告';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }
    }



    //***************************
    //  用户是否提交过这个检测
    //***************************
    private function checkResultExisted($activity_id){
        if(!$activity_id){
            return false;
        }
        $con = array();
        $con['activity_id'] = $activity_id;
        $res = M ('activity')->where ($con)->find();
        if($res["user_name"]!='' && $res["phone"]!='' ){
            return true;
        }else{
            return false;
        }
    }

    //***************************
    //  检测短信验证码对不对
    //***************************
    public function checkSmsCode($mobile,$code){

        if(isset($_POST["mobile"]) && isset($_POST["code"])){
            $mobile = I('post.mobile');
            $code = I('post.code');
        }

        if(preg_match("/^1[34578]{1}\d{9}$/",$mobile)){
            $tmp = S('sms_' . $mobile);
            if($tmp){
                $gap = time() - (int)$tmp["time"];
                if($gap <= 60 && $tmp["code"] == trim($code)){
                    return true;
                }
            }
        }
        return false;
    }
    
/*
    //***************************
    //  这个检测是否有结果
    //***************************
    private function checkResultExisted($uid,$testid){
        if(!$uid || !$testid){
            return false;
        }
        $con = array();
        $con['user_id'] = $uid;
        $con['test_id'] = $testid;
        $num = M ('result')->where ($con)->count();
        if($num != 0){
            return true;
        }else{
            return false;
        }
    }*/

}