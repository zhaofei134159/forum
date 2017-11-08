<?php
namespace app\index\controller;

use app\index\model\User;


use think\Session;
use think\Config;


// 个人中心
class Ucenter extends Common
{	
	public function __construct(){
		parent::__construct();
	}

    
    // 个人资料
    public function index(){
    	$uid = Session::get('login_id','forum_home');
    	$userid =  Session::get('login_id','forum_home');
    	
    	$get = input('get.');
    	if($get['uid']){
    		$uid = $get['uid'];
    	}
    	$user = User::get(['id'=>$uid]);

    	$data = array(
    			'user'=>$user
    		);
        return $this->view->fetch('index',$data);
    }

    // 个人设置
    public function setting(){
    	return $this->view->fetch('setting');
    }


}