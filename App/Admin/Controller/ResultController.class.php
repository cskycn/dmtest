<?php
namespace Admin\Controller;
use Think\Controller;
class ResultController extends PublicController{


	public function __construct(){
		//引入父类的构造函数
		parent::__construct();
	}

	
	//*************************
	// 
	//*************************
	public function index(){
		
		$count=M('result')->count();
		$empty = 0;
		if(count($count) == 0){
			$empty = 1;
		}
		
		$rows=ceil($count/rows);
		$page=(int)$_REQUEST['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$page_index=$this->page_index($count,$rows,$page);
		$resultList=M('result')->order('id desc')->limit($limit,rows)->select();
		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('empty',$empty);	
		$this->assign('tests',$result);	
		$this->display();	
	}

	
	
}