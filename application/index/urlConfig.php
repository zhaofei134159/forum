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
	'Cate'=>array(
		'controller'=>'论坛分类',
		'action'=>array(
			'index'=>'分类首页',
			'CatePlate'=>'分类版块',
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
			'index'=>'版块首页',
			'applymoderator'=>'申请版主',
			'myplatelist'=>'我的版块',
			'myPlateEdit'=>'版块编辑',
		),
	),
	'Cart'=>array(
		'controller'=>'论坛帖子',
		'action'=>array(
			'index'=>'帖子首页',
			'sendcart'=>'发表帖子',
			'seecart'=>'查看帖子',
		),
	),
	'Notice'=>array(
		'controller'=>'系统公告',
		'action'=>array(
			'noticelist'=>'公告列表',
			'noticedetail'=>'公告详情',
		),
	),
	'Message'=>array(
		'controller'=>'私信列表',
		'action'=>array(
			'index'=>'',
		),
	),
);


return $conf;