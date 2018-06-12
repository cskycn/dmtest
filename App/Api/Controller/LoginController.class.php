<?php

  namespace Api\Controller;

  use Think\Controller;
  use think\Db;

  class LoginController extends PublicController{
    public function index(){

    }

    //********************
    //登录和退出的操作
    //********************
    public function checkUserExisted(){ 
      if(IS_POST){
        $userinfo = json_decode(I('request.user_obj'),true);
        $con["avatarUrl"] = $userinfo["avatarUrl"];
        $res = M('user')->where($con)->find();
        if($res && res!=''){
          echo json_encode(array('status'=> 200, 'meta' =>$res));
        }else{
          $this->errorMsg($statusCode = 302,$errorMsg = "用户尚未注册");
          exit();
        }
      }
    }
    //********************
    //登录和退出的操作
    //********************
    public function doLogin(){ 
      
      if(IS_POST){
        
        $code = I('request.code');
        $userInfo = json_decode($_REQUEST["userInfo"],true);

        if(trim($code)=='') {
          exit;
        }
        
        //$userinfo = M('user')->where("code='$code'")->select();
        /*
        if($userinfo && $userinfo != array()){
          echo json_encode(array('status'=> '200', 'meta'=> array("userinfo"=> $userinfo)));
          exit();

          //id???????????????????
        }else{

          */
          $url = "https://api.weixin.qq.com/sns/jscode2session";



          $post_data["appid"] = C('weixin')["appid"];
          $post_data["secret"] = C('weixin')["secret"];
          $post_data["js_code"] = $code;
          $post_data["grant_type"] = "authorization_code";
         // $post_data = json_encode($post_data);
        
          
          if($res = get_http_array("post",$url,$post_data)){
            if($res["errcode"]){
              $this->errorMsg($statusCode = 301,$errorMsg = $res["errmsg"]);
              exit();
            }else{              
              if($res["openid"] == '' || $res["session_key"] == '' ){
                $this->errorMsg($statusCode = 302,$errorMsg = "微信授权异常");
                exit();
              }else{
                if($info = $this -> doUserAdd($res,$userInfo)){
                  echo json_encode(array('status'=> 200, 'meta' =>array("userinfo"=> $userInfo,"sessionKey" => $res["sessionKey"], "openid"=>$res["openid"])));
                  exit();
                }else{
                  $this->errorMsg($statusCode = 303,$errorMsg = "注册失败");
                  exit();
                }

              }
            }
          /*}else{
            $this->errorMsg($statusCode = 304,$errorMsg = "获取认证失败");
            exit();
          }*/
          }
      }
    }

    //将新授权的微信用户加入数据库user表
    private function doUserAdd($res,$info){
      if($res){
        $hander = M('user');
        $open = trim($res["openid"]);
        $data["session_key"] = $res["session_key"];
        $data["union_id"] = $res["unionid"]?$res["unionid"]:0;
        $data["open_id"] = $open;

        $data["nick_name"] = $info["nickName"];
        $data["avatarUrl"] = $info["avatarUrl"];
        $data["city"] = $info["city"];
        $data["country"] = $info["country"];
        $data["gender"] = $info["gender"];
        $data["province"] = $info["province"];
        $data["language"] = $info["language"];

        if($hander->where("open_id='$open'")->find()){
          return true;
        }else{
          if($hander->add($data)){
            return true;
          }
          else{
            return false;
          }
        }

        $nextIsRead = $hander->where("open_id='$open'")->find();

        $uid = $nextIsRead["id"];

        $hander = M('isread');
        $nextData["time"] = time();
        if(!$hander->where("uid='$uid'")->find()){
          $nextData["uid"] = $uid;
          $hander->add($nextData);
        }else{
          $hander->where("uid='$uid'")->save($nextData);
        }
        
      }else{
        return false;
      }
    }

    public function logout(){
      
      exit;
    }	
  }