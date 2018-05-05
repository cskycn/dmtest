<?php
namespace Admin\Controller;
use Think\Controller;
class TestController extends PublicController{

	public function __construct(){
		//引入父类的构造函数
		parent::__construct();
		//登录判断
		
	}
	
	//*************************
	// 显示检测管理页面
	//*************************
	public function index(){

		$imagePATH = __PUBLIC__ . "/home/images/test/";

		$count=M('test')->count();
		$empty = 0;
		if(count($count) == 0){
			$empty = 1;
		}
		
		$rows=ceil($count/rows);
		$page=(int)$_REQUEST['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$page_index=$this->page_index($count,$rows,$page);
		$testList = M('test')->order('id desc')->limit($limit,rows)->select();

		for ($i=0; $i <= count($testList) - 1; $i++) {
			$testList[$i]["cover_img_url"] = $imagePATH . "cover/" . $testList[$i]["cover_img_url"];
			$testList[$i]["detail_img_url"] = $imagePATH . "detail/" . $testList[$i]["detail_img_url"];
		
			if($testList[$i]["status"] == '1') {
				$testList[$i]["status"] = '活动中';
			}else{
				$testList[$i]["status"] = '已停止';
			}

			/*
			switch($testList[$i]["status"])
			{
				case 0:
					$testList[$i]["status"] =='错误';
					break;
				case 1:
					$testList[$i]["status"] =='未开始';
					break;
				case 2:
					$testList[$i]["status"] =='有好友助力';
					break;
				case 3:
					$testList[$i]["status"] =='完成助力';
					break;
				case 4:
					$testList[$i]["status"] =='完成提交检测需求';
					break;
				case 5:
					$testList[$i]["status"] =='已发出快递';
					break;
				case 6:
					$testList[$i]["status"] =='已寄回快递';
					break;
				case 7:
					$testList[$i]["status"] =='检测中';
					break;
				case 8:
					$testList[$i]["status"] =='完成';
					break;
				default:
					$testList[$i]["status"] =='错误';
			}
			*/
		}

		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('empty',$empty);	
		$this->assign('tests',$testList);	
		$this->display();
	}


	//*************************
	// 操作删除检测
	//*************************
	public function del(){

		$itemID = trim($_REQUEST["item"]);
		
		$con["id"] = $itemID;

		/*
		if(this->doDel($itemID)){
			echo true;
		}*/
		echo false;
		exit();		
	}

	protected function doDel($itemID){
		if(is_int($itemID)){
			$hander =  M('test');
			if($hander->where($$itemID)->delete()){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	//*************************
	// 编辑检测页面显示
	//*************************
	public function edit(){
		$con["id"] = intval($_REQUEST["test_id"]);
		$empty = 1;
		$res = M('test')->where($con)->find();
		if($res && $res != []){
			$empty = 0;	
		}
		$this->assign('empty',$empty);
		$this->assign('res',$res);
		$this->display();
	}


	//*************************
	// 编辑检测页面操作
	//*************************
	public function doEdit(){

		$con["id"] = intval($_POST["tid"]);
		$data["name"] = trim(clearhtml($_POST["name"]));
		$data["video_url"] = trim(clearhtml($_POST["video_url"]));

		//统一的图片上传信息
		$exts = array('gif','jpg','jepg','png');
		$cover_path ='test/cover/';
		$detail_path ='test/cover/';
		
		if($_FILES['cover_img_url']){		
			if(!$hander = $this->upload_images($_FILES['cover_img_url'],$exts,$cover_path)){ 
				echo false;
				return false;
			}
			$data["cover_img_url"] = $hander['save_name'];//['cover_img_url']['name'];
		}

		if($_FILES['detail_img_url']){
			if(!$hander = $this->upload_images($_FILES['detail_img_url'],$exts,$detail_path)){
				echo false;
				return false;
			}
			$data["detail_img_url"] = $hander['save_name'];
		}
		
		$hander = M('test');

		//
	/*	if(!$hander->where($con)->save($data)){
			return false;
		}
		*/
		echo true;
		return true;
	}


	//*************************
	// 新增检测页面显示
	//*************************
	public function add(){
		$this->display();
	}


	//*************************
	// 编辑检测页面操作
	//*************************
	public function doAdd(){

		$data["name"] = trim($this->clearhtml($_POST["name"]));
		$data["video_url"] = trim($this->clearhtml($_POST["video_url"]));

		//统一的图片上传信息
		$exts = array('gif','jpg','jepg','png');
		$cover_path ='test/cover/';
		$detail_path ='test/cover/';
		
		if($_FILES['cover_img_url']){		
			if(!$hander = $this->upload_images($_FILES['cover_img_url'],$exts,$cover_path)){ 
				echo false;
				return false;
			}
			$data["cover_img_url"] = $hander['save_name'];//['cover_img_url']['name'];
		}else{
			echo false;
			return false;
		}

		if($_FILES['detail_img_url']){
			if(!$hander = $this->upload_images($_FILES['detail_img_url'],$exts,$detail_path)){
				echo false;
				return false;
			}
			$data["detail_img_url"] = $hander['save_name'];
		}else{
			echo false;
			return false;
		}
		
		
		$hander = M('test');

		

	/*	if(!$hander->add($data)){
			return false;
		}

		*/
		echo true;
		return true;
	}

	
	
}