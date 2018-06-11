<?php
//各目录公用funcion,如要改要考虑各目录

//是否手机访问
function isMobile(){  
	$useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';  
	$useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';  	  
	function CheckSubstrs($substrs,$text){  
		foreach($substrs as $substr)  
			if(false!==strpos($text,$substr)){  
				return true;  
			}  
			return false;  
	}
	$mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
	$mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');  
		  
	$found_mobile=CheckSubstrs($mobile_os_list,$useragent_commentsblock) ||  
			  CheckSubstrs($mobile_token_list,$useragent);  
		  
	if ($found_mobile){  
		return true;  
	}else{  
		return false;  
	}  
}

function get_org_url($php,$m='',$c='',$a='',$list=array()){
	if(!$m || !$a || !$a){
		$url = SELF_ROOT.$php;
	}else{
		$url = SELF_ROOT.$php.'?m='.$m.'&c='.$c.'&a='.$a;
	}
	if($list){
		$list_str = '';
		foreach($list as $k=>$v){
			$list_str = $list_str.'&';
			$list_str = $list_str.$k.'='.$v;	
		}
		$url = $url.$list_str;
	}
	return $url;
}

function get_region_name($region_id){
	$region_name = M('all_region')->getFieldByregion_id($region_id,'region_name');
	return $region_name;	
}

//是否微信访问
function is_weixin()
{ 
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }  
        return false;
}

//取IP地址
function getIP() { 
	if (getenv('HTTP_CLIENT_IP')) { 
		$ip = getenv('HTTP_CLIENT_IP'); 
	} 
	elseif (getenv('HTTP_X_FORWARDED_FOR')) { 
		$ip = getenv('HTTP_X_FORWARDED_FOR'); 
	} 
	elseif (getenv('HTTP_X_FORWARDED')) { 
		$ip = getenv('HTTP_X_FORWARDED'); 
	} 
	elseif (getenv('HTTP_FORWARDED_FOR')) { 
		$ip = getenv('HTTP_FORWARDED_FOR'); 
	} 
	elseif (getenv('HTTP_FORWARDED')) { 
		$ip = getenv('HTTP_FORWARDED'); 
	} 
	else { 
		$ip = $_SERVER['REMOTE_ADDR']; 
	}

	if(strstr($ip,',')){
		$ip = substr($ip,0,strrpos($ip,','));  
	}
	return $ip; 
} 

//二维数组排序
function array_sort($arr,$keys,$type='asc'){ 
	$keysvalue = $new_array = array();
	foreach ($arr as $k=>$v){
		$keysvalue[$k] = $v[$keys];
	}
	if($type == 'asc'){
		asort($keysvalue);
	}else{
		arsort($keysvalue);
	}
	reset($keysvalue);
	foreach ($keysvalue as $k=>$v){
		$new_array[$k] = $arr[$k];
	}
	return $new_array; 
}

//检查提交的检测表单是否合法
function checkSubmitForm($submit){
	if(!checkUserTest($submit) || !checkUserName($submit) || !checkLocation($submit) || !checkRoomNumber($submit) || !checkPhone($submit) || !checkOrderTime($submit)){
		return false;
	}
	return true;
}

//检查提交的检测结果是否合法
function checkSubmitResult($submit){
	if(!checkUserTest($submit) || !checkRoomResult($submit) || !checkDecorateTime($submit) || !checkImgUpload($submit)){
		return false;
	}
	return true;
}

function checkUserTest($submit){
	// testid 和 userid 合法
	if(!array_key_exists("user_id",$submit) || !array_key_exists("test_id",$submit)){
		return false;
	}else{
		if(!is_int($submit["user_id"]) || !is_int($submit["test_id"])){
			return false;
		}
		return true;
	}
}

function checkUserName($submit) {
	// 用户名长度合法
	if(array_key_exists("user_name",$submit)){
		if(!strlen(trim($submit["user_name"])) >= 8 || !strlen(trim($submit["user_name"])) <= 20){
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
}

function checkLocation($submit) {
	//省市区与地址不能为空
	if(array_key_exists("province",$submit) || array_key_exists("city",$submit) || array_key_exists("area",$submit) || array_key_exists("address",$submit) ){
		if(trim($submit["province"])=='' || trim($submit["city"])=='' || trim($submit["area"])==''|| trim($submit["address"])=='') {
			return false;
		}
		else{
			return true;
		}
	}else{
		return true;
	}
}

function checkRoomNumber($submit) {
	//房间数不能小于1的整数
	if(array_key_exists("room_number",$submit)) {
		if(!preg_match("/^[1-9][0-9]*$/",$submit["room_number"])){
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
}

function checkPhone($submit) {
	// 手机号合法
	if(array_key_exists("phone",$submit)) {
		if(!preg_match("/^1[34578]{1}\d{9}$/",$submit["phone"])){  
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
}

function checkOrderTime($submit){
	//时间戳合法
	if(array_key_exists("order_time",$submit)) {
		if(!is_int($submit["order_time"])){  
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
}

function checkRoomResult($submit){
	//检查提交的房间以及对应的检测结果是否合规
	if(array_key_exists("submit_rooms",$submit)) {
		if(is_array($submit["submit_rooms"]) && count($submit["submit_rooms"] >= 1)){  
			return true;
		}else{
			return false;
		}
	}else{
		return true;
	}
} 

function checkDecorateTime($submit) {
	//检查装修时间是否合规
	if(array_key_exists("order_time",$submit)) {
		if(!is_int($submit["order_time"])){  
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
} 

function checkImgUpload($submit){
	//检查上传图片的数量是否合规
	if(array_key_exists("upload_img_url",$submit)) {
		if(is_array($submit["upload_img_url"]) && count($submit["upload_img_url"] >= 1)){  
			return true;
		}else{
			return false;
		}
	}else{
		return true;
	}
}

function getUserInfo($ids){
	//根据用户id返回id、用户名、头像
	if(is_array($ids)){
		$con["friend_ids"] = array('in', $ids);
	}
	else{
		$con["friend_ids"] = $ids;
	}

	$res = M("user")->where($con)->select();

	if($res) {
		return $res;
	}else{
		return array();
	}
	
}

//***************************
//  检查该用户的这个检测进度   
//  status 0.错误 1.未开始 2.有好友助力 3.完成助力 4.完成提交检测需求 5.可查看报告 
//***************************
function checkActivityStatus($testid, $userid){

	//接口默认返回码
	$statusCode = 500;
    $errorMsg = '提交的信息有误';

    if(!$uid || !$testid){
        return false;
        exit();
    }
    $con = array();
    $con['user_id'] = $uid;
    $con['test_id'] = $testid;
    $num = M ('activity')->where ($con)->field("status")->select();
    if($num != 0){
        return $num[0]["status"];
    }else{
        return 0;
    }
    exit();
}


//php 去除html标签 js 和 css样式 - 最爱用的一个PHP清楚html格式函数
function clearhtml($content) {  
	$content = preg_replace("/<a[^>]*>/i", "", $content);  
	$content = preg_replace("/<\/a>/i", "", $content);   
	$content = preg_replace("/<div[^>]*>/i", "", $content);  
	$content = preg_replace("/<\/div>/i", "", $content);      
	$content = preg_replace("/<!--[^>]*-->/i", "", $content);//注释内容
   // $content = preg_replace("/style=.+?['|\"]/i",'',$content);//去除样式  
	$content = preg_replace("/class=.+?['|\"]/i",'',$content);//去除样式  
	$content = preg_replace("/id=.+?['|\"]/i",'',$content);//去除样式     
	$content = preg_replace("/lang=.+?['|\"]/i",'',$content);//去除样式      
	$content = preg_replace("/width=.+?['|\"]/i",'',$content);//去除样式  
	
	$content = preg_replace("/width:.+?['|\;]/i",'',$content);//去除样式
	$content = preg_replace("/height:.+?['|\;]/i",'',$content);//去除样式
	 
	$content = preg_replace("/height=.+?['|\"]/i",'',$content);//去除样式   
	$content = preg_replace("/border=.+?['|\"]/i",'',$content);//去除样式   
	$content = preg_replace("/face=.+?['|\"]/i",'',$content);//去除样式   
	$content = preg_replace("/face=.+?['|\"]/",'',$content);//去除样式只允许小写正则匹配没有带 i 参数
	return $content;
 }
 
 function get_http_array($url,$post_data){
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   //没有这个会自动输出，不用print_r();也会在后面多个1
		 curl_setopt($ch, CURLOPT_POST, 1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		 $output = curl_exec($ch);
		 curl_close($ch);
		 $out = json_decode($output);
		 $data = object_array($out);
		 return $data;
 }
 
 function object_array($array)
 {
	if(is_object($array))
	{
	 $array = (array)$array;
	}
	if(is_array($array))
	{
	 foreach($array as $key=>$value)
	 {
	  $array[$key] = object_array($value);
	 }
	}
	return $array;
 }
 
 function get_config(){
	 $config_org = S('config_org');	
	 if(!$config_org){
		 $lyb_config = M('lyb_config');
		 $config_org = $lyb_config->field('code,value')->select();
		 S('config_org',$config_org);
	 }
	 
	 $config_list = array();
	 foreach($config_org as $k => $v){
		 $config_list[$v['code']] = $v['value'];
	 }
	 return $config_list;
 }


/**
*
* 快递鸟订阅推送2.0接口
*
* @技术QQ群: 340378554
* @see: http://kdniao.com/api-follow
* @copyright: 深圳市快金数据技术服务有限公司
* 
* ID和Key请到官网申请：http://kdniao.com/reg
*/

//电商ID
defined('EBusinessID') or define('EBusinessID', '1346616');
//电商加密私钥，快递鸟提供，注意保管，不要泄漏
defined('AppKey') or define('AppKey', '5b5c5b69-cc9b-48c9-8eb0-00cd11ded3ea');
//测试请求url
defined('ReqURL') or define('ReqURL', 'http://140.143.11.136/index.php/api/logistical/getNewLogistical');//'http://testapi.kdniao.cc:8081/api/dist');
//正式请求url
//defined('ReqURL') or define('ReqURL', 'http://api.kdniao.cc/api/dist');

//调用获取物流轨迹
//-------------------------------------------------------------
/*
$logisticResult = orderTracesSubByJson();
echo $logisticResult;
*/
//-------------------------------------------------------------
 
/**
 * Json方式  物流信息订阅
 */
function orderTracesSubByJson($send_id){
	$requestData="{'ShipperCode':'SF',".
			   "'LogisticCode':'".$send_id."',".
			   "'Remark':''}";
	
	$datas = array(
        'EBusinessID' => EBusinessID,
        'RequestType' => '1008',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '2',
    );
    $datas['DataSign'] = encrypt($requestData, AppKey);
	$result=sendPost(ReqURL, $datas);	
	
	//根据公司业务处理返回的信息......
	return $result;
}

/**
 * 电商Sign签名生成
 * @param data 内容   
 * @param appkey Appkey
 * @return DataSign签名
 */
function encrypt($data, $appkey) {
    return urlencode(base64_encode(md5($data.$appkey)));
}


/**
 *  post提交数据 
 * @param  string $url 请求Url
 * @param  array $datas 提交的数据 
 * @return url响应返回的html
 */
function sendPost($url, $datas) {
    $temps = array();	
    foreach ($datas as $key => $value) {
        $temps[] = sprintf('%s=%s', $key, $value);		
    }	
    $post_data = implode('&', $temps);
    $url_info = parse_url($url);
	if(empty($url_info['port']))
	{
		$url_info['port']=80;	
	}
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader.= "Host:" . $url_info['host'] . "\r\n";
    $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
    $httpheader.= "Connection:close\r\n\r\n";
    $httpheader.= $post_data;
    $fd = fsockopen($url_info['host'], $url_info['port']);
    fwrite($fd, $httpheader);
    $gets = "";
	$headerFlag = true;
	while (!feof($fd)) {
		if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
			break;
		}
	}
    while (!feof($fd)) {
		$gets.= fread($fd, 128);
    }
    fclose($fd);  
    
    return $gets;
}

