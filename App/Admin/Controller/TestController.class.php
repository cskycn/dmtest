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
		if($count == 0){
			$empty = 1;
		}else{
			$rows=ceil($count/rows);
			$page=(int)I('request.page');
			$page<0?$page=0:'';
			$limit=$page*rows;
			$page_index=$this->page_index($count,$rows,$page);
			$testList = M('test')->order('id desc')->limit($limit,rows)->select();	

			for ($i=0; $i <= count($testList) - 1; $i++) {	
				$testList[$i]["cover_img_url"] = $imagePATH . "cover/" . $testList[$i]["cover_img_url"];
				$testList[$i]["detail_img_url"] = $imagePATH . "detail/" . $testList[$i]["detail_img_url"];
			}
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
	public function doStop(){

		$itemID = I('request.itemID');
		$status = I('request.status');

		$con["id"] = $itemID;

		$data["status"] = ($status=='启用')? 1:0;

		if(M('test')->where($con)->save($data)){
			echo true;
			exit();
		}
		/*
		if(this->doDel($itemID)){
			echo true;
		}*/
		echo false;//$itemID . '   '.$status;
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
		$con["id"] = intval(I('request.test_id'));
		$empty = 1;
		$res = M('test')->where($con)->find();
		if($res && $res != array()){
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

		$con["id"] = intval(I('request.tid'));

		$data["name"] = I('request.name');
		$data["video_url"] = I('request.video_url');
		$data["group_count"] = I('request.group_count');
		$data["person_count"] = I('request.person_count');

		//统一的图片上传信息
		$exts = array('jpg','jepg','png');
		$cover_path ='test/cover';
		$detail_path ='test/detail';
		
		$testHander = M('test');

		$tmp = $testHander->where(array($con))->find();	
		$hasCover = $tmp['cover_img_url'];
		$hasDetail = $tmp['detail_img_url'];



		if($_FILES['cover_img_url']){	
			
			if(!$hander = $this->upload_images($_FILES['cover_img_url'],$exts,$cover_path)){ 
				echo false;
				return false;
			}
			$data["cover_img_url"] = $hander['savename'];//['cover_img_url']['name'];
		}

		if($_FILES['detail_img_url']){
			if(!$hander = $this->upload_images($_FILES['detail_img_url'],$exts,$detail_path)){
				echo false;
				return false;
			}
			$data["detail_img_url"] = $hander['savename'];
		}

		if($data["person_count"] < 0 && $data["group_count"] < 0){
			echo false;
			return false;
		}

		$data["person_count"] = ((int)$data["person_count"] < 0)? 0 : (int)$data["person_count"];
		$data["group_count"] = ((int)$data["group_count"] < 0)? 0 : (int)$data["group_count"];


		if($before = $testHander->where($con)->find()){
			$groupEdited = ($before["group_count"] == $data["group_count"])?false:true;
			$personEdited = ($before["person_count"] == $data["person_count"])?false:true;
		}

		

		if(!$testHander->where($con)->save($data)){
			//print_r($testHander->getLastSql());
			echo false;
			return false;
		}
		

		if($hasCover){
			$unCoverPath = './Public/home/images/'. $cover_path . $hasCover;
			unlink($unCoverPath);
		}
		if($hasDetail){
			$unDetailPath = './Public/home/images/'. $detail_path . $hasDetail;
			unlink($unDetailPath);
		}

		if(!$this->updateFriendhelpStatus($con["id"],$groupEdited,$personEdited)){
			echo false;//$con["id"] . '  1 '. $groupEdited . ' 1 ' .$personEdited;
			return false;
		}
		
		echo true;
		return true;
	}


	//*************************
	// 更新检测下所有的activity以及好友助力的状态
	// param $group 标识需要更新类型是团队预约的所有状态
	// param $person 标识需要更新类型是团队预约的所有状态
	//*************************
	protected function updateFriendhelpStatus($tid,$group,$person){
		$hander = M();

		//只将还没完成的助力改成完成
		$data["status"] = 3;
		if($group){
			$sql = "UPDATE dm_activity a, dm_test b, dm_friendhelp c
					SET a.`status`=3 
					WHERE b.id = a.test_id AND a.id = c.activity_id AND a.order_type = 1 AND (c.status = 1 OR c.status = 2) AND b.group_count <= c.count";
			if(!$res = $hander->query($sql)){
				return false;
			}
		}

		if($person){
			$sql = "UPDATE dm_activity a, dm_test b, dm_friendhelp c
					SET a.`status`=3 
					WHERE b.id = a.test_id AND a.id = c.activity_id AND a.order_type = 0 AND (c.status = 1 OR c.status = 2) AND b.person_count <= c.count";
			if(!$res = $hander->query($sql)){
				return false;
			}
		}
		return true;
	}


/*
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
		$data["friend_count"] = intval($_POST["friend_count"]);

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

		if($data["friend_count"] <= 0){
			echo false;
			return false;
		}
		
		
		$hander = M('test');

		if(!$hander->add($data)){
			return false;
		}

		echo true;
		return true;
	}
*/
	
}