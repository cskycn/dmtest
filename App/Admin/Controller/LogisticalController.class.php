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
		}else{
			$rows=ceil($count/rows);
			$page=(int)$_REQUEST['page'];
			$page<0?$page=0:'';
			$limit=$page*rows;
			$page_index=$this->page_index($count,$rows,$page);

			$list = M()	->table('dm_logistical a, dm_user b, dm_test d')
						->where('a.test_id = d.id  && a.user_id = b.open_id' )
						->field('a.id,d.name,b.nick_name,a.update_time,a.send_id')
						->order('a.id desc')
						->limit($limit,rows)
						->select(); 
		}
		
		
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

		$con["id"] = I('request.id');

		$res = M()->table('dm_logistical a, dm_test c,dm_activity d')
				  ->where('c.id = a.test_id && a.activity_id = d.id' )
				  ->field('a.id,a.send_id,c.name,d.user_name,d.phone,d.room_number')
				  ->order('a.id desc')
				  ->find(); 
		/*
		if(this->doDel($itemID)){
			echo true;
		}*/

		$this->assign('id',$res["id"]);
		$this->assign('name',$res["name"]);
		$this->assign('user_name',$res["user_name"]);
		$this->assign('phone',$res["phone"]);
		$this->assign('send_id',$res["send_id"]);
		$this->display();
	}

	//*************************
	// 操作运单号
	//*************************
	public function doEdit(){

		$con["id"] = I('request.id');
		$data["send_id"] = I('request.send_id');

		if($data["send_id"] == '0'){
			echo false;
			exit;
		}

		$hander = M('logistical');
		//不做运单号的unique
		$res = $hander->where($con)->save($data);
		if($res){
			//orderTracesSubByJson($data["send_id"]);
			echo true;
			exit;
		}else{
			echo false;
			exit;
		}
	}
	
	
}