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
    public function doLogin(){
      
      if(IS_POST){
        $code = $_POST['code'];
        $userInfo = $_POST['userInfo'];
        if(trim($code)=='') {
          exit;
        }
        $userinfo = M('user')->where("code='$code'")->select();
        
        if($userinfo && $userinfo != array()){
          echo json_encode(array('status'=> '200', 'meta'=> array("userinfo"=> $userinfo)));
          exit();

          //id???????????????????
        }else{

          
          $url = "https://api.weixin.qq.com/sns/jscode2session";
          $post_data["appid"] = $weixin["appid"];
          $post_data["secret"] = $weixin["secret"];
          $post_data["js_code"] = $code;
          $post_data["grant_type"] = "authorization_code";
          $post_data = json_encode($post_data);
        
          
          if($res = get_http_array("get",$url,$post_data)){
            if($res["errcode"]){
              $this->errorMsg($statusCode = 301,$errorMsg = "微信授权异常");
              exit();
            }else{
              if($res["openid"] != '' && $res["session_key"] != '' && $res["unionid"] != '' ){
                $this->errorMsg($statusCode = 302,$errorMsg = "微信授权异常");
                exit();
              }else{
                if($info = $this -> doUserAdd($res)){
                  echo json_encode(array('status'=> 200, 'meta' =>$info));
                  exit();
                }else{
                  $this->errorMsg($statusCode = 303,$errorMsg = "注册失败");
                  exit();
                }

              }
            }
          }else{
            $this->errorMsg($statusCode = 304,$errorMsg = "获取认证失败");
            exit();
          }
        }
      }
    }

    //将新授权的微信用户加入数据库user表
    private function doUserAdd($res,$info){
      if($res){
        $hander = M('user');
        $open = trim($res["openid"]);
        $data["open_id"] = $open;
        $data["session_key"] = $res["session_key"];
        $data["union_id"] = $res["unionid"]?$res["unionid"]:0;
        $data["nick_name"] = $res["nickname"];
        $data["avatarUrl"] = $res["session_key"];

        if($hander->where("open_id='$open'")->select()){
          return false;
        }else{
       /*   if($hander->data($data)->add()){
            return true;
          }
          else{
            return false;
          }*/
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