<?php


namespace Api\Controller;
use Think\Controller;
class UserController extends PublicController {
	//***************************
	//  首页数据接口
	//***************************
    public function index(){

        exit();
    }

    //***************************
    //  该用户的所有物流进度
    //***************************
    public function myLogistical(){
        /*
        $con['user_id'] trim($_REQUEST['user_id']));
        $hander = M('logistical');

        $res = $hander->where($con)->select();
        */

        $userID = I('request.user_id');
        $res = M()->table('dm_test a,dm_logistical b')
				  ->where('a.id = b.test_id AND b.user_id = \''.$userID.'\'' )
				  ->field('a.id,a.name,a.cover_img_url,b.activity_id')//status,a.name,b.send_id,b.update_time,b.status')
				  ->order('a.id')
                  ->select(); 
        if($res){
            echo json_encode(array('status'=> 200, 'meta'=>$res));
            exit();
        }
        $this->errorMsg($statusCode = 301,$errorMsg = "没有物流信息");
    }

    //***************************
    //  该用户所有的检测进度
    //***************************
    public function myTestStatus(){
        /*
        $con['user_id'] = array('EQ',trim($_REQUEST['user_id']));
        $hander = M('activity');
        $res = $hander->where($con)->select();
        ofhul5KXzYSrV_vwsVWVqV-0ibaM
        */
        $userID = I('request.user_id');
        $res = M()->table('dm_test a,dm_activity b')
				  ->where('a.id = b.test_id AND b.user_id = \''.$userID.'\'' )
				  ->field('a.id,b.status,a.name,a.cover_img_url,b.order_type')
				  ->order('a.id')
                  ->select(); 
                  
                  //echo M()->getLastSql();exit();

        if($res){
            echo json_encode(array('status'=> 200, 'meta'=>$res));
            exit();
        }
        $this->errorMsg($statusCode = 301,$errorMsg = "没有检测");
    }

    //***************************
    //  获取单个物流状态
    //***************************
    public function getOneLogistical(){
        $userID = I('request.user_id');
        $con["activity_id"] = trim($_REQUEST['activity_id']);

        $hander = M('logistical');
        $res = $hander->where($con)->find();
                  //echo M()->getLastSql();exit();

        if($res){
            echo json_encode(array('status'=> 200, 'meta'=>$res));
            exit();
        }
        $this->errorMsg($statusCode = 301,$errorMsg = "没有检测");
    }

    //***************************
    //  该用户的所有检测报告
    //***************************
    public function myResult(){
        $userID= I('request.user_id');

        $res = M()->table('dm_test a,dm_result b')
				  ->where('a.id = b.test_id AND b.user_id = \''.$userID.'\'' )
				  ->field('a.name,b.id')//status,a.name,b.send_id,b.update_time,b.status')
				  ->order('b.id')
                  ->select(); 



        //$hander = M('result');
        //$res = $hander->where($con)->find();
        if($res){
            echo json_encode(array('status'=> 200, 'meta'=>$res));
            exit();
        }
        $this->errorMsg($statusCode = 301,$errorMsg = "没有报告结果");
    }
}