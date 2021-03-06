<?php 
namespace app\index\model;

class Message extends Common
{
	protected $table = 'forum_message';


	#  用户发过来的消息
	public function userMessage($uid){

        $messages = $this->where(['receive_uid'=>$uid,'is_see'=>0])->group('send_uid')->order('ctime','desc')->select();
        if(empty($messages)){
	    	return $messages;
	    }
	    $messages = objToArray($messages);

	    $messagesArr = array();
	    foreach($messages as $key=>$val){
	       	$count = $this->where(['receive_uid'=>$val['receive_uid'],'send_uid'=>$val['send_uid'],'is_see'=>0])->order('ctime','desc')->count();
	       	$lastMessage = $this->where('receive_uid',$val['receive_uid'])
	       						->where('send_uid',$val['send_uid'])
	       						->where('is_see',0)
	       						->order('ctime','desc')
	       						->find();

	       	$messagesArr[$lastMessage['id']] = $lastMessage;
	       	$messagesArr[$lastMessage['id']]['count'] = $count;
	       	$messagesArr[$lastMessage['id']]['msg'] = 'receive';
	    }
	    
    	return $messagesArr;
	}

	#  用户发出去的消息
	public function userSendMessage($uid){

        $messages = $this->where(['send_uid'=>$uid])->group('send_uid')->order('ctime','desc')->select();
        if(empty($messages)){
	    	return $messages;
	    }
	    $messages = objToArray($messages);
	    

	    $messagesArr = array();
	    foreach($messages as $key=>$val){
	       	$count = $this->where(['receive_uid'=>$val['receive_uid'],'send_uid'=>$val['send_uid']])->order('ctime','desc')->count();
			$lastMessage = $this->where('receive_uid',$val['receive_uid'])
	       						->where('send_uid',$val['send_uid'])
	       						->order('ctime','desc')
	       						->find();

	       	$messagesArr[$lastMessage['id']] = $lastMessage;
	       	$messagesArr[$lastMessage['id']]['count'] = $count;
	       	$messagesArr[$lastMessage['id']]['msg'] = 'send';
	    }

	    return $messagesArr;
	}
}