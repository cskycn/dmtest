<?php
namespace Admin\Controller;
use Think\Controller;
class ResultController extends PublicController{


	public function __construct(){
		//引入父类的构造函数
		parent::__construct();
	}

	
	//*************************
	// 列出可以填写报告的
	//*************************
	public function index(){
		$search = I('request.search');

		//获取已到货且用户有填写手机号的activity
		if($search == ''){
			$count = M()->table('dm_activity a, dm_test c')
							   ->where('a.status = 4 && a.test_id = c.id')
							   //->field('a.id,a.test_id,a.user_id,a.user_name,a.phone,a.order_type,a.province,a.city,a.area,a.address,a.room_number,a.objects,a.object_num,a.order_time,a.info,a.status,c.name')
							   //->order('a.id desc')
							   ->count();
			$empty = 0;
			if($count == 0){
				$empty = 1;
			}else{
				$rows=ceil($count/rows);
				$page=(int)$_REQUEST['page'];
				$page<0?$page=0:'';
				$limit=$page*rows;
				$page_index=$this->page_index($count,$rows,$page);

				$list = M()->table('dm_activity a, dm_test c')
							   ->where('a.status = 4 && a.test_id = c.id')
							   ->field('a.id,a.test_id,a.user_id,a.user_name,a.phone,a.province,a.order_type,a.city,a.area,a.address,a.room_number,a.objects,a.object_num,a.order_time,a.info,a.status,c.name')
							   ->order('a.id desc')
							   ->limit($limit,rows)
							   ->select(); 
			}
		}else{
			$list = M()->table('dm_activity a,dm_test c')
							   ->where('a.status = 4 && a.test_id = c.id && a.user_name LIKE \'%'.$search.'%\'' )
							   ->field('a.id,a.test_id,a.user_id,a.user_name,a.phone,a.order_type,a.province,a.city,a.area,a.address,a.room_number,a.objects,a.object_num,a.order_time,a.info,a.status,c.name')
							   ->order('a.id desc')
							   ->select(); 
			if(!$list){
				$empty = 1;
			}
		}
		
		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('empty',$empty);	
		$this->assign('list',$list);	
		$this->assign('search',$search);	
		$this->display();	
	}

	//*************************
	// 显示编辑报告信息
	//*************************
	public function edit(){
		$activity_id = I('request.activity_id');
		
		if($activity_id){

			$list = M()->table('dm_activity a, dm_result b ,dm_test c')
							   ->where('b.activity_id = '.$activity_id . ' && a.id = b.activity_id && c.id = b.test_id' )
							   ->field('a.id,c.name,a.user_name,b.decorate_time,b.upload_img_urls,b.test_1,b.test_2,b.test_3,b.test_4,b.test_5,b.result_1,b.result_2,b.result_3,b.result_4,b.result_5')
							   ->order('b.id desc')
							   ->find(); 
			//$hander = M('result');
			//$list = $hander->where(array('activity_id'=>$activity_id))->find();
		}

		//print_r($list);
		//exit();



		if($list){
			$list["decorate_time"] = date('Y-m-d', $list["decorate_time"]);
		}
		if(trim($list["upload_img_urls"]) != ''){
			$images_display = explode(",",$list["upload_img_urls"]);
		}
		//print_r($list);
		//exit();
		$this->assign('res',$list);	
		$this->assign('images_display',$images_display);	
		$this->display();	
	}




	//*************************
	// 编辑检测报告
	//*************************
	public function doEdit(){
		$con["activity_id"] = I('post.aid');

		/*
		获取填写的数据
		*/

		$data["item1"] = I('request.item1');
		$data["result1"] = I('request.result1');
		if($data["item1"]=='' || $data["result1"]==''){
			echo false;
			return false;
		}

		for($i=2;$i<=5;$i++){
			if(isset($_POST["item".$i])){
				$data["test_".$i] = I("request.item".$i);
				$data["result_".$i] = I("request.result".$i);
				if($data["test_".$i]=='' || $data["result_".$i]==''){
					//为空直接删除这个data
					unset($data["test_".$i]);
					unset($data["result_".$i]);
				}
			}
		}
		
		if(isset($_POST["decorate_time"])){
			$data["decorate_time"] = strtotime(I('request.decorate_time') + ' 08:00:00');
		}

		
		/*
		获取上传图片并先上传
		*/
		//统一的图片上传信息
		$exts = array('jpg','jepg','png');
		$resultPath ='result/result';
		$newImgUrls = array();

		for($i=1;$i<4;$i++){
			if($_FILES["image".$i]){
				if(!$hander = $this->upload_images($_FILES["image".$i],$exts,$resultPath)){ 
					echo false;
					return false;
				}

				if(is_array($hander)){
					array_push($newImgUrls , $hander['savename']);
				}else{
					echo false;
					return false;
				}
			}
		}

		if($newImgUrls != array()){
			$data["upload_img_urls"] = implode(",",$newImgUrls);
		}else{
			$data["upload_img_urls"] = '';
		}
		
		
		$res = M('activity')->where(array('id'=>$con["activity_id"]))->find();
		if($res){
			$data["test_id"] = $res["test_id"];
			$data["user_id"] = $res["user_id"];	
		}else{
			echo false;
			return false;
		}


		/*
		根据报告是否存在进行add或者edit
		*/
		$hander = M('result');
		$tmp = $hander->where($con)->find();

		if($tmp){
			//已存在报告，进行编辑
			$res = $hander->where($con)->save($data);
			$imgArray = explode(",",$tmp["upload_img_urls"]);
			$count = count($imgArray);
			if($res){
				for($i=0;$i<$count;$i++){
					$unPath = './Public/home/images/'. $resultPath . $imgArray[$i];
					unlink($unPath);
				}
				echo true;
				return true;
			}else{
				echo false;
				return false;
			}
		}else{
			//新增报告
			$data["activity_id"] = $activity_id;
			if($res = $hander->add($data) && $res1 = M('activity')->where(array('id'=>$activity_id))->save(array('status'=>5))){
				echo true;
				return true;
			}else{
				echo false;
				return false;
			}
		}
		
	}
}