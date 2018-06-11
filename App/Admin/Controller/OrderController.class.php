<?php
namespace Admin\Controller;
use Think\Controller;
class OrderController extends PublicController{

	public function __construct(){
		//引入父类的构造函数
		parent::__construct();
	}

	
	//*************************
	// 用户预约首页
	//*************************
	public function index(){

		$search = I('request.search');
		if($search == ''){
			$count=M('activity')->count();
			$empty = 0;
			if($count == 0){
				$empty = 1;
			}else{
				$rows=ceil($count/rows);
				$page=(int)I('request.page');;
				$page<0?$page=0:'';
				$limit=$page*rows;
				$page_index=$this->page_index($count,$rows,$page);

				$activityList = M()->table('dm_activity a, dm_user b, dm_test c')
								   ->where('b.open_id = a.user_id && c.id = a.test_id')
								   ->field('a.id,a.test_id,a.user_id,a.user_name,a.phone,a.province,a.city,a.area,a.address,a.room_number,a.order_time,a.info,a.status,b.nick_name,c.name')
								   ->order('a.id desc')
								   ->limit($limit,rows)
								   ->select(); 

				$counter = count($activityList);

				for($i = 0; $i < $counter; $i ++){
					$activityList[$i]["address"] = $activityList[$i]["province"] . $activityList[$i]["city"] 
											   . $activityList[$i]["area"] . $activityList[$i]["address"];
				}
			}
			
		}else{
			$activityList = M()->table('dm_activity a, dm_user b, dm_test c')
							   ->where('b.open_id = a.user_id && c.id = a.test_id && b.nick_name LIKE \'%'.$search.'%\'')
							   ->field('a.id,a.test_id,a.user_id,a.user_name,a.phone,a.province,a.city,a.area,a.address,a.room_number,a.order_time,a.info,a.status,b.nick_name,c.name')
							   ->order('a.id desc')
							   ->select(); 

			$counter = count($activityList);

			for($i = 0; $i < $counter; $i ++){
				$activityList[$i]["address"] = $activityList[$i]["province"] . $activityList[$i]["city"] 
										   . $activityList[$i]["area"] . $activityList[$i]["address"];
			}
			if($counter == 0) $empty = 1;
		}

		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('empty',$empty);	
		$this->assign('tests',$activityList);
		$this->assign('search',$search);	
		$this->display();
	}



	//*************************
	// 编辑预约页面显示
	//*************************
	public function edit(){
		$con["id"] = intval(I('request.activity_id'));
		$empty = 1;
		$res = M('activity')->where($con)->find();
		if($res && $res != []){
			$empty = 0;	
		}
		$this->assign('empty',$empty);
		$this->assign('res',$res);
		$this->display();
	}


	//*************************
	// 编辑预约页面操作
	//*************************
	public function doEdit(){

		$con["id"] = intval(I('request.aid'));

		$data["user_name"] = I('request.user_name');
		$data["phone"] = I('request.phone');
		$data["room_number"] = I('request.room_nunber');
		//........更多字段需要可以编辑if need

		$hander = M('activity');
		//
	/*	if(!$hander->where($con)->save($data)){
			return false;
		}
		*/
		echo true;
		return true; 
	}

	
	
}