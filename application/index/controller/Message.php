<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Cate;
use app\index\model\Plate;
use app\index\model\Cart;
use app\index\model\Message as model_message;

use think\Session;
use think\Config;
use smtp;

# 帖子
class Message extends Common
{	
	public $send_uid;

	public function __construct(){
		parent::__construct();	
		$this->send_uid = Session::get('login_id','forum_home');
	}

    public function is_login(){
        if(!Session::get('login_id','forum_home')){
            $this->redirect('login/index');
        }
    } 


	public function index(){


        $data = array(
        	);
        return $this->view->fetch('index',$data);

	}

	# finduser
	public function findUser(){
		$post = input('post.');
		$uid = $post['uid'];

		if(!$send_uid){
			return json(['flog'=>0,'msg'=>'请先登录']);
		}

		$user = User::get(['id'=>$uid]);
		if(empty($user)){
			return json(['flog'=>0,'msg'=>'用户不存在']);
		}
		return json(['flog'=>1,'msg'=>'success','data'=>$user]);
	}
}