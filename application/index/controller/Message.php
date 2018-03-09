<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Cate;
use app\index\model\Plate;
use app\index\model\Cart;
use app\index\model\Message as model_message;
use app\index\model\messageMember;

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
		$this->is_login();
		// 登录人
		$uid = $this->send_uid;
		$get = input('get.');

		$mid = $get['mid'];

		if(!empty($get['uid'])){
			$memberWhere = array();
			$memberWhere['member'] = array('like','#'.$this->send_uid.'#');
			$memberWhere['member'] = array('like','#'.$get['uid'].'#');
			$memberArr = messageMember::where($memberWhere)->find();
			if(empty($memberArr)){
				$member = array();
				$member['member'] = $this->send_uid.','.$get['uid'];
				$member['creator'] = $this->send_uid;
				$member['ctime'] = time();
				$messageEr = messageMember::create($member);
			}
		}


		$message_model = new model_message();
		
        $send_messages = $message_model->userMessage($uid);
        $send_messages = objToArray($send_messages);

        $receive_messages = $message_model->userSendMessage($uid);
        $receive_messages = objToArray($receive_messages);

        $messages_list = array_merge($send_messages,$receive_messages);
        array_multisort(array_column($messages_list,'ctime'),SORT_DESC,$messages_list);

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);


        $model_follow = new Follow();
        $follows = $model_follow->userCount();

        $model_cart = new model_cart();
        $userCarts = $model_cart->userCount();

        $data = array(
        		'mid'=>$mid,
        		'messages_list'=>$messages_list,
        		'users'=>$users,
        	);
        return $this->view->fetch('index',$data);
	}

	# 回复
	public function ReplyMessage(){
		$post = input('post.');
		$content = $post['content'];
		var_dump($content);
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

		$member = array();
		$member['member'] = '#'.$this->send_uid.'#'.$uid.'#';
		$member['creator'] = $this->send_uid;
		$member['ctime'] = time();
		$messageEr = messageMember::create($member);

		$insert = array();
		$insert['member_id'] = $messageEr->id;
		$insert['send_uid'] = $this->send_uid;
		$insert['receive_uid'] = $uid;
		$insert['message'] = $message;
		$insert['ctime'] = time();
		$insert['is_see'] = 0;
		$messageId = model_message::create($insert);

		return json(['flog'=>1,'msg'=>'success']);
	}
}