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
		
		$search = I('request.search');

		$hander = M('activity');
		//参与助力的
		$count = $hander->count();
		//有发起助力的
		$active= $hander->where('status = 2')->count();
		//完成助力的
		$completed= $hander->where('status = 3')->count();
		$empty = 0;
		
		
		if($search == ''){

			if((int)$active + (int)$completed == 0){
				$empty = 1;
			}else{
				$rows=ceil((int)$active + (int)$completed/rows);
				$page=(int)$_REQUEST['page'];
				$page<0?$page=0:'';
				$limit=$page*rows;
				$page_index=$this->page_index($count,$rows,$page);


			//查询所有的助力记录
				$helpList = M()	->table('dm_friendhelp a, dm_user b,dm_activity c, dm_test d')
								->where('b.open_id = a.user_id && c.id = a.activity_id && d.id = c.test_id' )
								->field('a.id,a.activity_id,a.count,a.is_complete,b.nick_name,d.name')
								->order('a.id desc')
								->limit($limit,rows)
								->select(); 
			}
			
		}else{
			$helpList = M()	->table('dm_friendhelp a, dm_user b,dm_activity c, dm_test d')
							->where('b.open_id = a.user_id && c.id = a.activity_id && d.id = c.test_id && b.nick_name LIKE \'%'.$search.'%\'' )
							->field('a.id,a.activity_id,a.count,a.is_complete,b.nick_name,d.name')
							->order('a.id desc')
							->select(); 
			if(!$helpList){
				$empty = 1;
			}
		}
		

		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('empty',$empty);	
		$this->assign('tests',$helpList);
		$this->assign('total',$count);
		$this->assign('active',$active);
		$this->assign('completed',$completed);	
		$this->assign('search',$search);	
		$this->display();
	}

	
	
}