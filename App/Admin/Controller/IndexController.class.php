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
	   $this->assign('test','1');	
	   $this->display();
	}	
	
}