<?php

// 控制器 方法
// $conf['controller']['Login'] = '登录 or 注册';
// $conf['controller']['Ucenter'] = '个人资料';
// $conf['action']['setting'] = '设置';

$conf['home_url'] = 
array(
	'Login'=>array(
		'controller'=>'登录',
		'action'=>array(
			'do_bind'=>'绑定邮箱',
		),
	),
	'Ucenter'=>array(
		'controller'=>'个人资料',
		'action'=>array(
			'edituser'=>'个人信息修改',
			'basicuserinfo'=>'基本资料',
			'detailuserinfo'=>'详细资料',
			'edubackinfo'=>'教育背景',
			'editwordinfo'=>'工作信息',
		),
	),
	'Forum'=>array(
		'controller'=>'论坛版块',
		'action'=>array(
			'applymoderator'=>'申请版主',
		),
	),
);


return $conf;