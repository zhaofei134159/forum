<?php 
namespace app\index\model;

class Message extends Common
{
	protected $table = 'forum_message';


	public function userMessage($uid){

        $messages = $this->where(['receive_uid'=>$uid,'is_see'=>0])->group('send_uid')->order('ctime','desc')->select();
	    $messages = objToArray($messages);

	    foreach($messages as $key=>$val){
	       	$count = $this->where(['receive_uid'=>$val['receive_uid'],'send_uid'=>$val['send_uid'],'is_see'=>0])->order('ctime','desc')->count();
	       	
	       	$messages[$key]['count'] = $count;
	    }

	    return $messages;
	}
}