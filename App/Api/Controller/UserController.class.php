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
        $con['user_id'] = trim($_POST['user_id']);
        $hander = M('logistical');

        $res = $hander->where($con)->select();

        echo json_encode(array('status'=> 200, 'meta'=>$res));
        exit();
    }

    //***************************
    //  该用户所有的检测进度
    //***************************
    public function myTestStatus(){
        $con['user_id'] = trim($_POST['user_id']);

        $res = M()->table('dm_test c,dm_activity d')
				  ->where('c.id = d.test_id' )
				  ->field('d.id,d.status,c.name')
				  ->order('d.id desc')
				  ->select(); 

        echo json_encode(array('status'=> 200, 'meta'=>$res));
        exit();
    }

}