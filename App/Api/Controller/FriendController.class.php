<?php

namespace Api\Controller;
use Think\Controller;
class FriendController extends PublicController {
	//***************************
	//  首页数据接口
    //***************************
    
    public function index(){

    }

    //***************************
	//  提交好友助力接口
    //***************************
    public function doHelp(){

        //接口默认返回码
        $statusCode = 500;
        $errorMsg = '提交信息有误';

        //======================
        //提交所需信息
        //======================
        $submit["user_id"] = intval($_POST['userid']);
        $submit["activity_id"] = intval($_POST['activity_id']);

        //======================
        //判断信息是否合法
        //======================

        if($submit["user_id"] == '' || $submit["activity_id"] == ''){
            $statusCode = 301;
            $errorMsg = '助力信息不合法';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        if($this->checkHelpExisted($submit["user_id"],$submit["activity_id"])){
            $statusCode = 302;
            $errorMsg = '你已助力过了';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        if($this->doHelpAction($submit["user_id"],$submit["activity_id"])){
            $statusCode = 200;
            $errorMsg = "提交检测成功";
            echo json_encode(array('status'=> $statusCode, 'msg'=>$errorMsg));
            exit();
        }else{
            $statusCode = 303;
            $errorMsg = '好友助力失败';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }
    }


    //***************************
	//  检查该用户是不是这个活动的发起人
    //***************************
   /* public function checkOwnner(){

        //接口默认返回码
        $statusCode = 500;
        $errorMsg = '提交的信息有误';

        //======================
        //提交所需信息
        //======================
        $submit["user_id"] = intval($_REQUEST['userid']);
        $submit["activity_id"] = intval($_REQUEST['activityid']);

        if(trim($submit["user_id"])!='' && trim($submit["user_id"])!=''){
            $isOwnner = M('activity')->where($submit)->count();
            if($isOwnner == 1){
                return true;
            }else{
                return false;
            }
        }

    }*/
    //***************************
	//  获取已助力好友信息
    //***************************
    public function getFriendHelpList(){

        //接口默认返回码
        $statusCode = 500;
        $errorMsg = '提交信息有误';

        //======================
        //提交所需信息
        //======================
      /*  $submit["user_id"] = intval($_POST['userid']);
        $submit["test_id"] = intval($_POST['testid']);
        */
        $submit["activity_id"] = intval($_POST['activity_id']);
        $submit["ownner_id"] = intval($_POST['ownner_id']);

        //======================
        //判断信息是否合法
        //======================

        if(trim($submit["activity_id"]) == '' || !is_int($submit["activity_id"])){
            $statusCode = 301;
            $errorMsg = '助力信息有误';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }
        //获取活动的主人的信息
        $hander = M('user');
        $uid = $submit["ownner_id"];
        $ownner = array();

        if($res = $hander->where("id='$uid'")->find()){
            $ownner["user_id"] = $res["id"];
            $ownner["nick_name"] = $res["nick_name"];
            $ownner["avatar_url"] = $res["avatarUrl"];
        }else{
            $statusCode = 303;
            $errorMsg = '查询活动失败';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        //获取互动的好友列表
        $hander = M('friendhelp');
        $con["activity_id"] = $submit["activity_id"];
        if($res = $hander->where($con)->find()){
            if($res){
                $arr = explode(",",$res["friend_ids"]);
                if(count($arr) != 0){
                    $userList = getUserInfo($arr);
                }else{
                    $userList = array();
                }

                $con1["id"] = $submit["activity_id"];
                $testInfo = M('activity')->where($con1)->field("id,test_id,user_id")->find();

                if($testInfo){
                    $num = count($userList);
                    $statusCode = 200;
                    echo json_encode(array('status'=> $statusCode, 'meta'=> array("count"=> $num,
                                                                              "userlist"=>$userList,
                                                                              "ownner"=>$ownner,
                                                                              "test_info"=>$testInfo)));
                }else{
                    $statusCode = 305;
                    $errorMsg = '查询检测失败';
                    $this->errorMsg($statusCode,$errorMsg);
                }              
                exit();

            }else{
                $statusCode = 302;
                $errorMsg = '查询活动失败';
                $this->errorMsg($statusCode,$errorMsg);
                exit();
            }
        }else{
            $statusCode = 304;
            $errorMsg = '查询活动失败';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        

    }

    //***************************
    //  用户是否提交过这个好友助力
    //***************************
    protected function checkHelpExisted($userID,$activityID){
        if(!$userID || !$activityID){
            return false;
        }
        $con = array();
        $con['activity_id'] = $activityID;
        $res = M ('friendhelp')->where($con)->field("friend_ids")->find();
        if(count($res) == 1){
            $arr = explode(",",$res["friend_ids"]);
            if(in_array($userID,$arr)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //***************************
    //  进行好友助力
    //***************************
    private function doHelpAction($userID,$activityID){
        if(!$userID || !$activityID){
            return false;
        }
        $con = array();
        $con['activity_id'] = $activityID;
        $hander = M('friendhelp');
        $res = $hander->where($con)->field("friend_ids,status")->find();

        if(count($res) != 0){
            if($res["status"] == 1 || $res["status"] == 2){
                if($res["friend_ids"] != ''){
                    $res["friend_ids"] = $ids["friend_ids"] . "," . $userID;
                }else{
                    $res["friend_ids"] = $userID;
                }
            }else{
                return false;
            }

            if($friendCount = getExplodeCount($res["friend_ids"],",")){
                if($friendCount == 20){
                    $$res["status"] = 3;
                }
            }
        
            if($hander->where($con)->save(array("friend_ids" => $res["friend_ids"],"status" => $res["status"]))){  
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }
}