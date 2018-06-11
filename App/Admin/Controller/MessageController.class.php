<?php
namespace Admin\Controller;
use Think\Controller;
class MessageController extends PublicController{

	public function __construct(){
		//引入父类的构造函数
		parent::__construct();

	}

	
	//*************************
	// 客服消息首页
	//*************************
	public function index(){
		$count=M('customer')->count();
		$empty = 0;
		if($count == 0){
			$empty = 1;
		}
		
		$rows=ceil($count/rows);
		$page=(int)$_REQUEST['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$page_index=$this->page_index($count,$rows,$page);
	//	$messageList=M('customer')->order('id desc')->limit($limit,rows)->select();


		$messageList = M()->table('dm_customer a, dm_user b')
						   ->where('b.open_id = a.user_id')
						   ->field('a.id,a.user_id,a.content,a.create_time,a.reply,a.is_read,b.nick_name')
						   ->order('a.id desc')
						   ->limit($limit,rows)
						   ->select(); 
		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('empty',$empty);	
		$this->assign('tests',$messageList);	
		$this->display();
	} 



	//*************************
	// 显示回复页面
	//*************************
	public function reply(){

		$con["id"] = I('request.id');

		$res = M()->table('dm_customer a, dm_user b')
				  ->where('b.id = a.user_id' )
				  ->field('a.id,a.content,a.create_time,a.reply,b.nick_name')
				  ->order('a.id desc')
				  ->find(); 

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
	// 操作回复留言
	//*************************
	public function doReply(){

		$con["id"] = I('request.id');
		$data["reply"] = I('request.reply');
		$data["reply_time"] = time();

		if($data["reply"] == ''){
			echo false;
			exit;
		}

		$hander = M('customer');
		if($hander->where($con)->save($data)){
			echo true;
			exit;
		}else{
			echo false;
			exit;
		}
	}
} 