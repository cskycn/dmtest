<?php

namespace Api\Controller;

use Think\Controller;

class SmsController extends Controller {
    public function index(){
        exit();
    }


    //***************************
    //  发起短信请求
    //***************************
    public function sendSMStoUser(){
        
        $appid = '1400088944';
        $appkey = 'd5c05b78417b83f662a369ee5890cad0';
        $country_prefix = '86';
        $templId = '128306';  //短信模板的ID


        $phone = I('request.phone');
      //  $userID = I('request.user_id');
        

        //S('sms_'.$phone,null);
        if(!$phone || !$this->checkSmsCode($phone)) { //短信验证码限定时间内
            $statusCode = 301;
            $errorMsg = '发送短信太频繁或错误';
            $this->errorMsg($statusCode,$errorMsg);
            exit();
        }

        $code = $this->createSMSCode();

        try {
            Vendor('Sms/SmsSenderUtil');
            Vendor('Sms/SmsSingleSender');

            $sender = new \Qcloud\Sms\SmsSingleSender($appid, $appkey);
            $params = [$code];
            $result = $sender->sendWithParam($country_prefix, $phone, $templId,$params, "");
            $result = json_decode($result,true);
            
            $data["mobile"] = $phone;
            $data["code"] = $code;
            $data["time"] = time();

            if($result["result"] == 0){
                $this->setSmsCache($data);
                echo json_encode(array('status'=> 200, 'meta' =>array('msg'=>'短信发送成功')));
                exit();
            }else{
                $statusCode = 302;
                $errorMsg = '短信发送失败';
                $this->errorMsg($statusCode,$errorMsg);
                exit();
            }
        } 
        catch(\Exception $e) { 
            return false;
        }
    }


    //***************************
    //  生成短信验证码
    //***************************
    public function createSMSCode($length = 4){
        $min = pow(10 , ($length - 1));
        $max = pow(10, $length) - 1;
        return rand($min, $max);
    }

    //***************************
    //  检测手机短信验证码是否发送过
    //***************************
    protected function checkSmsCode($mobile){
        if (!$mobile) {
            return false;
        }

        //没有缓存记录，之前没发过
        if (!S('sms_' . $mobile) ){ 
            return true;
        }

        //缓存记录超过60s
        $tmp = S('sms_' . $mobile);
        $gap = time() - (int)$tmp["time"];
        if($gap >= 60){
            return true;
        }   
        return false;

    }


    //***************************
    //  设置手机短息验证码缓存
    //***************************
    protected function setSmsCache($data_cache){

        $lifetime = 1800;
        //Cache::set
        S('sms_' . $data_cache['mobile'], $data_cache, $lifetime);
    }

/*
    public function test(){
        $data["mobile"] = 'aa';
        $data["b"] = 'bb';
        $data["c"] = 'cc';
        $this->setSmsCache($data);

        $ee = S('sms_'.$data["mobile"]);
        print_r($ee["b"]);

    
        S('sms_'.$data["a"],NULL);
echo "123";
        exit();

    }
*/

}