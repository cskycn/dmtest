<?php
namespace Admin\Controller;
use Think\Controller;
class FriendController extends PublicController{

	public function __construct(){
		//引入父类的构造函数
		parent::__construct();

	}
	
	//*************************
	// 好友助力首页
	//*************************
	public function index(){
		$hander = M('activity');
		$count= $hander->count();

		//发起助力的
		$active= $hander->where('status = 2')->count();

		//完成助力的
		$completed= $hander->where('status = 3')->count();
		$empty = 0;
		if(count($count) == 0){
			$empty = 1;
		}
		
		$rows=ceil($count/rows);
		$page=(int)$_REQUEST['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$page_index=$this->page_index($count,$rows,$page);


		//查询所有的助力记录
		$helpList = M()	->table('dm_friendhelp a, dm_user b,dm_activity c, dm_test d')
						->where('b.id = a.user_id && c.id = a.activity_id && d.id = c.test_id' )
						->field('a.id,a.activity_id,a.count,a.is_complete,a.creat_time,a.finish_time,b.nick_name,d.name')
						->order('a.id desc')
						->limit($limit,rows)
						->select(); 

		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('empty',$empty);	
		$this->assign('tests',$helpList);
		$this->assign('total',$count);
		$this->assign('active',$active);
		$this->assign('completed',$completed);	
		$this->display();
	}

	
	
}