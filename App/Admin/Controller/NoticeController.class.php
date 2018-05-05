<?php
namespace Admin\Controller;
use Think\Controller;
class NoticeController extends PublicController{

	public function __construct(){
		//引入父类的构造函数
		parent::__construct();

	}

	
	//*************************
	// 推送消息页面显示
	//*************************
	public function index(){
		$hander = M('notice');
		$count = $hander->count();
		$empty = 0;
		if(count($count) == 0){
			$empty = 1;
		}
		
		$rows=ceil($count/rows);
		$page=(int)$_REQUEST['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$page_index=$this->page_index($count,$rows,$page);

		$list = $hander ->order('id desc')->limit($limit,rows) ->select();

		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('empty',$empty);	
		$this->assign('tests',$list);	
		$this->display();
	} 


	//*************************
	// 操作删除推送
	//*************************
	public function del(){

		$itemID = trim($_REQUEST["item"]);
		
		$con["id"] = $itemID;

		/*
		if(this->doDel($itemID)){
			echo true;
		}*/
		echo true;
		exit();		
	}

	protected function doDel($itemID){
		if(is_int($itemID)){
			$hander =  M('notice');
			$data["status"] = 0;
			if($hander->where($$itemID)->save($data)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//*************************
	// 新增推送页面显示
	//*************************
	public function add(){
		$this->display();
	}

	public function doAdd(){

		$data["title"] = trim($this->clearhtml($_POST["title"]));
		$data["content"] = trim($this->clearhtml($_POST["content"]));
		$data["ctime"] = time();
		$data["status"] = 1; 

		$hander = M('notice');

		
		if($hander->add($data)){
			echo true;
			return true;
		}else{
			echo false;
			return false;
		}
	}

	


} 