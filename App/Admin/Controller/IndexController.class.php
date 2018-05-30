<?php
namespace Admin\Controller;
use Think\Controller;

class IndexController extends PublicController{

	public function __construct(){
		//引入父类的构造函数
		parent::__construct();
		
	}
	//***********************************
	// 后台首页
	//**********************************
	public function index(){
		$machine = 0;
        $cost = 0;
		$family = 0;
			
		$res = M('index')->find();
        if(is_array($res) && count($res)!=0){
            $machine = $res['machine'];
            $cost = $res['cost'];
            $family = $res['family'];
        }
		$this->assign('machine',$machine);	
		$this->assign('cost',$cost);	
		$this->assign('family',$family);	
	    $this->display();
	}	

	//***********************************
	// 编辑首页数据
	//**********************************
	public function doEdit(){
		if($_POST['machine'] && is_numeric(trim($_POST['machine']))){
			$data['machine'] = trim($_POST['machine']);
		}else if($_POST['cost'] && is_numeric(trim($_POST['cost']))){
			$data['cost'] = trim($_POST['cost']);
		}else if($_POST['family'] && is_numeric(trim($_POST['family']))){
			$data['family'] = trim($_POST['family']);
		}

		if(count($data) == 1){
			$hander = M('index');
			$con['id'] = 1;
			if($hander->where($con)->save($data)){
				echo true;
				exit;
			}
		}
		echo false;
		exit;
	}
	
}