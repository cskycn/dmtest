<?php
namespace Admin\Controller;
use Think\Controller;
class LogisticalController extends PublicController{

	public function __construct(){
		//引入父类的构造函数
		parent::__construct();
		
	}
	
	//*************************
	// 物流管理首页
	//*************************
	public function index(){
		/*$hander = M('activity');
		$con['status'] = array('in','4,5,6');
		$count= $hander->where($con)->count();*/
		$hander = M('logistical');
		$count= $hander->count();

		$empty = 0;
		if($count == 0){
			$empty = 1;
		}
		
		$rows=ceil($count/rows);
		$page=(int)$_REQUEST['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$page_index=$this->page_index($count,$rows,$page);

		
 

		$list = M()	->table('dm_logistical a, dm_user b, dm_test d')
						->where('a.test_id = d.id  && a.user_id = b.id' )
						->field('a.id,d.name,b.nick_name,a.update_time,a.send_id')
						->order('a.id desc')
						->limit($limit,rows)
						->select(); 
		//print_r($list);
		
		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('empty',$empty);	
		$this->assign('tests',$list);
		$this->assign('total',$count);
		$this->display();	
		
	}



	//*************************
	// 显示编辑运单号页面
	//*************************
	public function edit(){

		$con["id"] = $this->clearhtml(trim($_REQUEST["id"]));

		$res = M()->table('dm_logistical a, dm_user b, dm_test c,dm_activity d')
				  ->where('b.id = a.user_id && c.id = a.test_id && a.activity_id = d.id' )
				  ->field('a.id,a.send_id,a.update_time,b.nick_name,c.name,d.user_name,d.phone,d.room_number')
				  ->order('a.id desc')
				  ->select(); 

		if(!$res){
			$this->display('index');
		}

		/*
		if(this->doDel($itemID)){
			echo true;
		}*/
		$this->assign('res',$res);
		$this->display();
	}

	//*************************
	// 操作运单号
	//*************************
	public function doEdit(){

		$con["id"] = trim($_REQUEST["id"]);
		$data["send_id"] = $this->clearhtml(trim($_REQUEST["send_id"]));

		if($data["send_id"] == '0'){
			echo false;
			exit;
		}

		$hander = M('customer');
		/*对send_id做unique
		$check["send_id"] = $data["send_id"];
		if($res1 = $hander->where($check)->select()){
			
		}*/

		if($hander->where($con)->save($data)){
			orderTracesSubByJson($data["send_id"]);
			echo true;
			exit;
		}else{
			echo false;
			exit;
		}
	}
	
	
}