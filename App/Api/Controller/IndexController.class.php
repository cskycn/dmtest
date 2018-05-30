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

        $machine = 0;
        $cost = 0;
        $family = 0;

        $res = M('index')->find();
        if(is_array($res) && count($res)!=0){
            $machine = $res['machine'];
            $cost = $res['cost'];
            $family = $res['family'];
        }

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