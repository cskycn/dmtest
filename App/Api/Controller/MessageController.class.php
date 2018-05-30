<?php

namespace Api\Controller;
use Think\Controller;
class MessageController extends PublicController {
	//***************************
	//  首页数据接口
	//***************************
    public function index(){
    }

    //***************************
    //  提交留言信息
    //***************************
    public function submitMessage(){
        $con["user_id"] = $_REQUEST['user_id'];
        $con["content"] = trim(clearhtml($_REQUEST['content']));
        $con["create_time"] = time();

        if(M ('customer')->add ($con)){
            $statusCode = 200;
            $errorMsg = "提交留言成功";
            echo json_encode(array('status'=> $statusCode, 'msg'=>$errorMsg));
        }else{
            $statusCode = 301;
            $errorMsg = '提交留言失败';
            $this->errorMsg($statusCode,$errorMsg);
        }
        exit();
    }

    //***************************
    //  获取用户留言信息
    //***************************
    public function getMessage(){
        //这里需要做管理员权限判断
        if(1==1){
            $res = $this->getAllMessage();
            $statusCode = 200;
            echo json_encode(array('status'=> $statusCode, 'meta'=>$res));
        }else{
            $statusCode = 301;
            $errorMsg = '无权查看信息';
            $this->errorMsg($statusCode,$errorMsg);
            
        }
        exit();
    }

    //***************************
    //  获取用户留言对应的信息
    //***************************
    private function getAllMessage($user_id = false){
        if(!$user_id){
            //获取全部留言
            $hander = M('customer')->select();
        }else{
            //只获取对应用户的留言
            $con["user_id"] = $user_id;
            $hander = M('customer')->where($con)->select();
        }
        return $hander;
    }


    //***************************
    //  用户是否有未读消息
    //***************************
    public function checkRead(){
        $con["user_id"] = $_REQUEST['user_id'];

        $con["is_read"] = '0';
        $con['reply'] = array('NEQ','');  



        /////////////这里需要再增加判断，用户在前端打开我的消息的时间最新的，和最新的系统消息对比

        if($count = M('customer')->where($con)->count()){
            if($count != 0){
                $statusCode = 200;
                $data["is_read"] = 0;
                
            }else{
                $statusCode = 200;
                $data["is_read"] = 1;
            }
            echo json_encode(array('status'=> $statusCode, 'meta'=>$data));
            exit();
        }else{
            $statusCode = 301;
            $errorMsg = '查询失败';
            $this->errorMsg($statusCode,$errorMsg);
        }
    }

    //***************************
    //  获取全部系统消息
    //***************************
    public function getAllMyNotice(){
        $con["user_id"] = $_REQUEST['user_id'];
        $con['reply'] = array('NEQ','');  

        $noticeCon["status"] = 1;

        $customer = M('customer')->where($con)->select();
        $notice = M('notice')->where($noticeCon)->select();
        $c_constomer = count($customer);
        $c_notice = count($notice);

        $data = array();
        if($c_constomer!=0){
            for($i = 0; $i < $c_constomer; $i++){
                $tmp = array();
                $tmp["content"] = $customer[$i]["reply"];
                $tmp["time"] = $customer[$i]["reply_time"];
                $tmp["title"] = '管理员的回复';
                array_push($data,$tmp);
            }
        }

        if($c_notice!=0){
            for($i = 0; $i < $c_notice; $i++){
                $tmp = array();
                $tmp["content"] = $notice[$i]["content"];
                $tmp["time"] = $notice[$i]["ctime"];
                $tmp["title"] = $notice[$i]["title"];;
                array_push($data,$tmp);
            }
        }

        usort($data, function($a, $b) {
            return ($a['time'] < $b['time'])?1:-1;
        });

        echo json_encode(array('status'=> '200', 'meta'=>$data));
        exit();
    }

    //***************************
    //  更新用户读消息时间
    //***************************
    public function freshMessageReadTime(){ 
        $uid = $_REQUEST['user_id'];
        $data["time"] = time();
        M('isread')->where("uid='$uid'")->save($data);
    }


    //***************************
    //  更新用户读消息时间
    //***************************
    public function getNewMessageIsRead(){ 
        $statusCode = 200;
        $uid = $_REQUEST['user_id'];
        if($res = M('isread')->where("uid='$uid'")->find()){
            $notice = M('notice') ->order('ctime desc') -> find();

            $con["uid"]= $uid;
            $con["reply"] = array('NEQ','');
            $message = M('customer') ->where($con) -> order('reply_time desc')->find();

            $newTime = ($message["reply_time"] > $notice["ctime"]) ? $message["reply_time"] : $notice["ctime"];

            if($newTime > $res["time"]){
                //有未读消息
                $data["isRead"] = 0;
                echo json_encode(array('status'=> $statusCode, 'meta'=>$data));
            }else{
                $data["isRead"] = 1;
                echo json_encode(array('status'=> $statusCode, 'meta'=>$data));
            }
        }else{
            $data["isRead"] = 0;
            echo json_encode(array('status'=> $statusCode, 'meta'=>$data));
        }
    }

    
}