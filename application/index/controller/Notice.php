<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Cate;
use app\index\model\Plate;
use app\index\model\Notice as modelNotice;
use app\index\model\NoticeReply;

use think\Session;
use think\Config;
use smtp;


class Notice extends Common
{	
    public $uid;

	public function __construct(){
		parent::__construct();
        $this->uid = Session::get('login_id','forum_home');
	}

    
    public function NoticeList(){
    	$where = array();
    	$where['is_del'] = 0;
    	$where['noticeId'] = 0;
		$notices = modelNotice::where($where)->order('ctime','desc')->paginate(20, false);        
        $page = $notices->render();

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $data = array(
        		'notices'=>$notices,
        		'page'=>$page,
        		'users'=>$users,
        	);
        return $this->view->fetch('NoticeList',$data);
    }

    public function NoticeDetail(){
        $get = input('get.');
        $noticeId = $get['noticeId'];

        $notice = modelNotice::get(['id'=>$noticeId]);

        $notice_reply = NoticeReply::where(['notice_id'=>$noticeId,'is_del'=>0])->order('ctime','asc')->select();

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $data = array(
                'notice'=>$notice,
                'notice_reply'=>$notice_reply,
                'users'=>$users,
            );
        return $this->view->fetch('NoticeDetail',$data);
    }

    public function ReplyNotice(){
        $post = input('post.');

        $insert = array();
        $insert['notice_id'] = $post['noticeId'];
        $insert['userid'] = $this->uid;
        $insert['content'] = $post['content'];
        $insert['ctime'] = time();

        $notice = NoticeReply::create($insert);

        header('location:/index/Notice/NoticeDetail.html?noticeId='.$post['noticeId'].';');
    }
}