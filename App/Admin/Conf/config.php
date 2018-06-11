<?php
return array(
	//'配置项'=>'配置值'
	//更换模板变量规则，修改配置项
	'TMPL_PARSE_STRING'=>array(           //添加自己的模板变量规则
		'__DATA__'=>__ROOT__.'/Data'
	),

	'TEST_SUBMIT_FORM'=>array(
		'1' => array('user_name','phone','province','city','area','address','objects','order_time','info'),
		'2' => array('user_name','phone','province','city','area','address','objects','order_time','info'),
		'3' => array('user_name','phone','province','city','area','address','objects','order_time','info'),
		'4' => array('user_name','phone','province','city','area','address','object_num','info'),
		'5' => array('user_name','phone','province','city','area','address','room_number','order_time','info'),
		'6' => array('user_name','phone','province','city','area','address','object_num','info'),
		'7' => array('user_name','phone','province','city','area','address','object_num','info'),
	)
);