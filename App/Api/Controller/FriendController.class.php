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

        $submit["user_id"] = I('post.userid');
        $submit["activity_id"] = intval(I('post.activity_id'));
        $submit["openid"] = I('post.openid');

        if($submit["user_id"] == '' || $submit["activity_id"] == ''){
            $statusCode = 301;
            $errorMsg = '助力信息不合法';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        if($this->checkIsComplete($submit["activity_id"])){
            $statusCode = 302;
            $errorMsg = '助力已完成';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        if(!$this->isWatchedGongZhongHao($submit["openid"])){
            $statusCode = 303;
            $errorMsg = "未关注公众号";
            echo json_encode(array('status'=> $statusCode, 'msg'=>$errorMsg));
            exit();
        }

        if($this->checkHelpExisted($submit["user_id"],$submit["activity_id"])){
            $statusCode = 304;
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
            $statusCode = 305;
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

        $submit["activity_id"] = intval(I('request.activity_id'));
        $submit["ownner_id"] = I('request.ownner_id');

        if(trim($submit["activity_id"]) == '' || !is_int($submit["activity_id"])){
            $statusCode = 301;
            $errorMsg = '助力信息有误';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }
        //获取活动的主人的信息
        $hander = M('user');
        $con["open_id"] = array('EQ',trim($submit["ownner_id"]));
        $ownner = array();

        if($res = $hander->where($con)->find()){

            $ownner["user_id"] = $res["open_id"];
            $ownner["nick_name"] = $res["nick_name"];
            $ownner["avatar_url"] = $res["avatarurl"];
        }else{
            $statusCode = 303;
            $errorMsg = '查询活动失败';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        //获取互动的好友列表
        $hander = M('friendhelp');
        $con["activity_id"] = $submit["activity_id"];
        if(!$res = $hander->where($con)->find()){
            $res = $this->addNewFriendHelp($con["activity_id"],$submit["ownner_id"]);
        }

        $arr = explode(",",$res["friend_ids"]);

        
        if(count($arr) != 0 && trim($arr[0]) != ''){
            $userList = getUserInfo($arr);
        }else{
            $userList = array();
        }

      //  $con1["id"] = $submit["activity_id"];
      //  $testInfo = M('activity')->where($con1)->field("id,test_id,user_id")->find();

        $testInfo = M()->table('dm_activity a, dm_test b')
							   ->where('a.id = '.$submit["activity_id"] . ' && a.test_id = b.id' )
							   ->field('a.id,a.test_id,a.user_id,b.person_count,b.group_count,a.order_type')
							   ->order('b.id desc')
							   ->find(); 

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
        
    }

    //***************************
    //  新建一个friendHelp记录
    //***************************
    protected function addNewFriendHelp($aid,$uid){
        $Model = M();
        $sql = "INSERT INTO `dm_friendhelp` (`activity_id`,`count`,`is_complete`,`creat_time`,`user_id`) VALUES ('".$aid."','0','0','".time()."','".$uid."')";
		$Model->execute($sql);
        $hander = M('friendhelp');
        $con["activity_id"] = $submit["activity_id"];
        $res = $hander->where($con)->find();
        return $res;
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
        if($res && is_array($res)){
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
        $res = $hander->where($con)->field("friend_ids,status,count")->find();
        $order = M('activity')->where(array('id'=>$activityID))->find();
        $order_type = $order["order_type"];

        if($res){
            if($res["status"] == 1 || $res["status"] == 2){
                if($res["friend_ids"] != ''){
                    $res["friend_ids"] = $ids["friend_ids"] . "," . $userID;
                }else{
                    $res["friend_ids"] = $userID;
                }
                $res["count"] += 1;
            }else{
                return false;
            }

            if($hander->where($con)->save(array("friend_ids" => $res["friend_ids"],"count" => $res["count"]))){ 

                //检查是不是达到了助力标准
                $afterRes = $hander->where($con)->field("count")->find();
                $total = $this->getTestFriendTargetCount($activityID,$order_type);

                if(/*$total != 0  &&*/ $afterRes["count"] >=  $total){
                    $res["status"] = 3;
                }else if($afterRes["count"] > 0 && $afterRes["count"] < $total){
                    $res["status"] = 2;
                }
                
                $hander->where($con)->save(array("status" => $res["status"]));
                return true; //助力成功
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    //***************************
    //  获取对应检测需要的目标好友数
    //***************************
    private function getTestFriendTargetCount($activityID,$order_type){
        $activityID = intval($activityID);
        $res = M()->table('dm_activity a, dm_test b')
        ->where("b.id = a.test_id && a.id=" . $activityID)
        ->field('a.id,b.friend_count')
        ->find(); 
        if($res){
            return $res["friend_count"];
        }else{
            return 0;
        }
    }

    //***************************
    //  检测助力是否已经完成
    //***************************
    private function checkIsComplete($activityID){
        $activityID = intval($activityID);

        $res = M('activity')->where(array("id"=>$activityID))->find();
       /* $res = M()->table('dm_activity a, dm_test b')
        ->where("b.id = a.test_id && a.id=" . $activityID)
        ->field('a.count,b.friend_count')
        ->find(); */
        if($res){
            if($res["status"] >= 3){
                return true;
            }
        }
        return false;
    }



    protected function isWatchedGongZhongHao($userID){
        $access_token = $this->_getAccessToken($userID);
        $subscribe_msg = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$userID;
        $subscribe = get_http_array("get",$subscribe_msg);
        if($subscribe["subscribe"]){
            $zyxx = $subscribe["subscribe"];
            if ($zyxx !== 1) {
                return false;
              }else{
                  return true;
              }
        }
        return false;
    }

    private function _getAccessToken($userID) {

        $appID = '';
        $appSecret = '';

        $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appID.'&secret='.$secret;
        $json = get_http_array("get",$url_get);
        if($json["access_token"]){
            return $json["access_token"];
        }
        return false;
    }
}