<?php


namespace Api\Controller;
use Think\Controller;
class IndexController extends PublicController {
	//***************************
	//  首页数据接口
	//***************************
    public function index(){

        //接口默认返回码
        $statusCode = 500;
        $errorMsg = '啊呀出错了';

        //======================
        //获取当前机器数量
        //======================
        $machine = 1024;
    
        //======================
        //获取活动成本
        //======================
        $cost = 2222;

        //======================
        //获取已检测家庭数
        //======================
        $family = 123;

        $testList = $this->getTestList();

        if($testList && count($testList) > 0){
            $statusCode = 200;
            echo json_encode(array('status'=> $statusCode, 'meta' => array('machine'=>$machine,'cost'=>$cost,'family'=>$family,'testList'=>$testList)));
        }else{
            $this->errorMsg($statusCode,$errorMsg);
        }
    	exit();
    }

    //***************************
    //  获取测试List
    //***************************
    private function getTestList(){
        $con = array ();
	    $con[ 'status' ] = 1 ;
        $list = M ('test')->where ($con)->select ();
        return $list;
    }

}