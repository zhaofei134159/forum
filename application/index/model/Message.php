<?php 
namespace app\index\model;

class Message extends Common
{
	protected $table = 'forum_message';


	#  用户发过来的消息
	public function userMessage($uid){

        $messages = $this->where(['receive_uid'=>$uid,'is_see'=>0])->group('send_uid')->order('ctime','desc')->select();
	    $messages = objToArray($messages);
	    if(empty($messages)){
	    	return $messages;
	    }

	    foreach($messages as $key=>$val){
	       	$count = $this->where(['receive_uid'=>$val['receive_uid'],'send_uid'=>$val['send_uid'],'is_see'=>0])->order('ctime','desc')->count();
	       	$lastMessage = $this->get(['receive_uid'=>$val['receive_uid'],'send_uid'=>$val['send_uid'],'is_see'=>0])->order('ctime','desc');
       		$lastMessage = collection($lastMessage)->toArray();

	       	$messages[$lastMessage['id']] = $lastMessage;
	       	$messages[$key]['count'] = $count;
	       	$messages[$key]['msg'] = 'receive';
	    }
	    	
    	return $messages;
	}

	#  用户发出去的消息
	public function userSendMessage($uid){

        $messages = $this->where(['send_uid'=>$uid])->group('send_uid')->order('ctime','desc')->select();
	    $messages = objToArray($messages);
	    if(empty($messages)){
	    	return $messages;
	    }

	    foreach($messages as $key=>$val){
	       	$count = $this->where(['receive_uid'=>$val['receive_uid'],'send_uid'=>$val['send_uid']])->order('ctime','desc')->count();
	       	$lastMessage = $this->get(['receive_uid'=>$val['receive_uid'],'send_uid'=>$val['send_uid']])->order('ctime','desc');
			$lastMessage = collection($lastMessage)->toArray();
			
	       	$messages[$lastMessage['id']] = $lastMessage;
	       	$messages[$key]['count'] = $count;
	       	$messages[$key]['msg'] = 'send';
	    }

	    return $messages;
	}
}