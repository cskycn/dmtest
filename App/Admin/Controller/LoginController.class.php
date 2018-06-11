<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends PublicController{
	public function index(){
		$this->display();
	}
	//********************
	//登录和退出的操作
	//********************
	public function doLogin(){
		
		if(IS_POST){
			$username=I('post.username');
			$admininfo=M('admin')->where("name='$username'")->find();
			if($admininfo && $admininfo != array()){
				if(MD5(MD5(I('post.pwd'))) == $admininfo['password']){
					$admin=array(
					   "id"         =>$admininfo["id"],
					   "name"       =>$admininfo["name"]
 					);
 					unset($_SESSION['admininfo']);
					$_SESSION['admininfo']=$admin;
					echo "<script>location.href='".U('Index/index')."'</script>";				
				}else{
					$this->error('账号密码错误');
				}
			}else{
				$this->error('账号不存在或已注销');
			}
		}
	}
	public function logout(){
		unset($_SESSION['admininfo']);
		echo "<script>alert('注销成功');location.href='".U('Login/index')."'</script>";
		exit;
	}	
}