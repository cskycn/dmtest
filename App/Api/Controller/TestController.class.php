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
	//  提交检测数据接口
    //***************************
    public function submitActivity(){

        //接口默认返回码
        $statusCode = 500;
        $errorMsg = '提交信息有误';

        //======================
        //提交所需信息
        //======================

        $submit = $_REQUEST['submit'];

        
        $submit["user_id"] = $submit['userid'];
        $submit["test_id"] = intval($submit['testid']);
        $submit["activity_id"] = intval($submit['activity_id']);

        $submit["user_name"] = trim($submit['username']);
        $submit["phone"] = trim($submit['phone']);

        $submit["code"] = trim($submit['verify_code']);
        $submit["address"] = trim($submit['address']);
        $submit["room_number"] = trim($submit['roomnumber']);
        $submit["info"] = trim($submit['info']);

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

        $order_time = trim($submit['ordertime']);
        $timeArr = explode("-",$order_time);

        if(is_array($timeArr) && count($timeArr) == 3){
            if(intval($timeArr[0]) > 1970 && intval($timeArr[0]) < 2030 
               && intval($timeArr[1]) > 0 && intval($timeArr[1]) < 13
               && intval($timeArr[2]) > 0 && intval($timeArr[2]) < 32){
                   $submit["order_time"] = ($order_time + '08:00:00');
            }
        }else{
            $statusCode = 301;
            $errorMsg = '预约信息不对';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }
        

        //======================
        //判断信息是否合法
        //======================

        if($submit["user_id"] == '' || $submit["test_id"] == '' || !checkSubmitForm($submit)){
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
                $data["info"] = $submit["info"];
                $data["status"] = 4;
                if($hander->where($con)->save($data)){
                    //将user_id、activity_id、test_id传入，新生成一个物流订单
                    if($this->creatLogisticalInfo($data["user_id"],$data["activity_id"],$data["test_id"])){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
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

        //接口默认返回码
        $statusCode = 500;
        $errorMsg = '提交的信息有误';

        //======================
        //提交所需信息
        //======================
        $submit["user_id"] = $_POST['userid'];
        $submit["test_id"] = intval($_POST['testid']);

        if($submit["user_id"] == '' || $submit["test_id"] == ''){
            $statusCode = 301;
            $errorMsg = '结果信息不合法';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        if($hander = M('result')->where($submit)->find()){
            $statusCode = 200;
            echo json_encode(array('status'=> 200, 'meta' =>$hander));
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
    private function checkSmsCode($mobile,$code){
        $tmp = S('sms_' . $mobile);
        if($tmp){
            $gap = time() - (int)$tmp["time"];
            if($gap <= 60 && $tmp["code"] == trim($code)){
                return true;
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
        $con["id"] = intval($_POST['test_id']);


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
		$aid = intval($_POST["activity_id"]);
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