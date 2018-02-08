<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Userinfo;


use think\Session;
use think\Config;


// 个人中心
class Ucenter extends Common
{	
    public $uid = 0;
    public $userid = 0;

	public function __construct(){
		parent::__construct();

        $this->uid = Session::get('login_id','forum_home');
        $this->userid =  Session::get('login_id','forum_home');
	}

    
    // 个人资料
    public function index(){
    	$uid = $this->uid;
    	
    	$get = input('get.');
    	if($get['uid']){
    		$uid = $get['uid'];
    	}
    	$user = User::get(['id'=>$uid]);
        $userinfo = Userinfo::get(['uid'=>$uid]);

    	$data = array(
    			'user'=>$user
    		);
        return $this->view->fetch('index',$data);
    }

    // 个人设置
    public function setting(){
    	return $this->view->fetch('setting');
    }

    public function editUser(){
        $uid = $this->uid;
        $userinfo = Userinfo::get(['uid'=>$uid]);


        $data = array(
                'userinfo'=>$userinfo
            );
        return $this->view->fetch('editUser',$data);
    }

}