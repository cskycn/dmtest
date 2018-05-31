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
	//  获取一个用户所有的物流信息
	//***************************
    public function getUserLogistical(){
        $con["user_id"] = trim($_POST["user_id"]);

        $hander = M('logistical');
        if($res = $hander->where($con)->select()){
            /*
            $timer = time();
            for($i = 0; $i <= count($res)-1; $i++) {
                if($res[$i]["send_id"] != '0' && $timer - $res[$i]["update_time"] > 3600 * 24 && !strpos($res[$i]["msg"], '已签收')){
                    if($new = $this->getLogisticalApi($res[$i]["send_id"]) && $new != false){
                        //$res[$i]["update_time"] == time();
                        $res[$i]["msg"] == $new;
                    }
                }
            }
            */
            if($res !='' && count($res) != 0){
                for($i = 0; $i <= count($res)-1; $i++) {
                    if($res[$i]["send_id"] != '0' && $res[$i]["status"] == 0){
                        $this->getLogisticalApi($res[$i]["send_id"]);
                    }
                }
                echo json_encode(array('status'=> '200', 'meta'=>$res));
                exit();
            }
        }else{
            echo json_encode(array('status'=> '301', 'msg'=>'查询失败'));
            exit();
        }
    }


    //***************************
	//  调用快递鸟api请求订阅物流信息
	//***************************
    private function getLogisticalApi($id = ''){
        $res = orderTracesSubByJson($id);
        echo $res;
    }

    //***************************
	//  调用快递鸟api接收物流信息更新
	//***************************
    public function getNewLogistical(){
        $res = json_decode($_REQUEST["RequestData"],true);
        if(array_key_exists("data",$res) && array_key_exists("EBusinessID",$res) && $res["EBusinessID"] == '1346616'){
            $data = $res["data"];

            $hander = M('logistical');
            for($i = 0; $i <= count($data)-1; $i++) {
                if($data[$i]["EBusinessID"] == '1346616' && $data[$i]["Success"] == true){
                    $last = count($data[$i]["Traces"]);
                    $con["send_id"] = $data[$i]["LogisticCode"];
                    $save["status"] = $data[$i]["State"];
                    $save["update_time"] = $data[$i]["Traces"][$last-1]["AcceptTime"];
                    $save["msg"] = $data[$i]["Traces"][$last-1]["AcceptStation"];
                    $hander->where($con)->save($save);
                }
            }

        }else{
            return false;
        }
    }

/*
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
    */
}