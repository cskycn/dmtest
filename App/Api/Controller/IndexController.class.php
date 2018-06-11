<?php


namespace Api\Controller;
use Think\Controller;
class IndexController extends PublicController {
	//***************************
	//  首页数据接口
	//***************************
    public function index(){
        $machine = 0;
        $cost = 0;
        $family = 0;
        if(S('index_machine') && S('index_cost') && S('index_family')){
            $machine = S('index_machine');
            $cost = S('index_cost');
            $family = S('index_family');
        }else{
            $res = M('index')->find();
            if(is_array($res) && count($res)!=0){
                $machine = $res['machine'];
                $cost = $res['cost'];
                $family = $res['family'];
                S('index_machine', $machine, 3600);
                S('index_cost', $cost, 3600);
                S('index_family', $family, 3600);
            }
        }

        $testList = $this->getTestList();

        if($testList && count($testList) > 0){
            $statusCode = 200;
            echo json_encode(array('status'=> $statusCode, 'meta' => array('machine'=>$machine,'cost'=>$cost,'family'=>$family,'testList'=>$testList)));
        }else{
            $this->errorMsg(301,'获取数据失败');
        }
    	exit();
    }

    //***************************
    //  获取测试List
    //***************************
    private function getTestList(){
        $list = array();
        if(S('index_tests')){
            $list = S('index_tests');
        }else{
            $con['status'] = 1 ;
            $list = M ('test')->where ($con)->select();
            if($list && is_array($list)){
                S('index_tests', $list, 7200);
            }
        }
        return $list;
    }

}