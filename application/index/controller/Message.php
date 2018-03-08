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
		// 登录人
		$uid = $this->$send_uid;
		$mid = input('get.mid');
		
        $send_messages = Message::where('send_uid',$uid)->group('send_uid')->order('ctime','desc')->select();
        $send_messages = objToArray($send_messages);

        $receive_messages = Message::where('receive_uid',$uid)->group('receive_uid')->order('ctime','desc')->select();
        $receive_messages = objToArray($receive_messages);

        $messages = array_merge($send_messages,$receive_messages);
        array_multisort(array_column($messages,'ctime'),SORT_DESC,$messages);
        
        var_dump($messages);

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $data = array(
        		'mid'=>$mid,
        		'messages'=>$messages,
        		'users'=>$users,
        	);
        return $this->view->fetch('index',$data);
	}

	# finduser
	public function findUser(){
		$post = input('post.');
		$uid = $post['uid'];

		if(!$this->send_uid){
			return json(['flog'=>0,'msg'=>'请先登录']);
		}

		$user = User::get(['id'=>$uid]);
		if(empty($user)){
			return json(['flog'=>0,'msg'=>'用户不存在']);
		}
		$data = array(
				'user'=>$user,
        	);
        $html = $this->view->fetch('findUser',$data);
		return json(['flog'=>1,'msg'=>'success','data'=>$html]);
	}

	# 快捷发送信息
	public function quickSend(){
		$post = input('post.');
		$uid = $post['uid'];
		$message = $post['message'];

		if(!$this->send_uid){
			return json(['flog'=>0,'msg'=>'请先登录']);
		}

		$user = User::get(['id'=>$uid]);
		if(empty($user)){
			return json(['flog'=>0,'msg'=>'用户不存在']);
		}

		$insert = array();
		$insert['send_uid'] = $this->send_uid;
		$insert['receive_uid'] = $uid;
		$insert['message'] = $message;
		$insert['ctime'] = time();
		$insert['is_see'] = 0;
		$messageId = model_message::create($insert);

		return json(['flog'=>1,'msg'=>'success']);
	}
}