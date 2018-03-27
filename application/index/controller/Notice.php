<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Cate;
use app\index\model\Plate;
use app\index\model\Notice as modelNotice;
use app\index\model\NoticeReply;
use app\index\model\Fabulous;

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

    
    public function index(){
    	$where = array();
    	$where['is_del'] = 0;
    	$where['noticeId'] = 0;
		$notices = modelNotice::where($where)->order('ctime','desc')->paginate(20, false);        
        $page = $notices->render();

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $noticeFabulous = NoticeReply::get_query('select article_id,count(1) as count from forum_fabulous where type=1 group by article_id');
        $noticeFabulous = objToArray($noticeFabulous,'article_id');
        $noticeReply = NoticeReply::get_query('select notice_id,count(1) as count from forum_notice_reply where is_del=0 group by notice_id');
        $noticeReply = objToArray($noticeReply,'notice_id');

        $data = array(
        		'notices'=>$notices,
        		'page'=>$page,
        		'users'=>$users,
                'noticeReply'=>$noticeReply,
                'noticeFabulous'=>$noticeFabulous,
        	);
        return $this->view->fetch('index',$data);
    }

    public function NoticeDetail(){
        $get = input('get.');
        $noticeId = $get['noticeId'];


        $notice = modelNotice::get(['id'=>$noticeId]);

        modelNotice::where('id',$noticeId)->update(['see'=>intval($notice['see'])+1]);

        $notice_reply = NoticeReply::where(['notice_id'=>$noticeId,'is_del'=>0])->order('ctime','desc')->select();

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $fabulous = Fabulous::get(['userid'=>$this->uid,'article_id'=>$noticeId,'type'=>1]);

        $data = array(
                'notice'=>$notice,
                'notice_reply'=>$notice_reply,
                'users'=>$users,
                'fabulous'=>$fabulous,
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

    public function FabulousNotice(){
        $post = input('post.');
        $noticeId = $post['noticeId'];
        if(!$this->uid){
            return json(['flog'=>0,'msg'=>'请先登录账号！']);
        }

        $fabulous = Fabulous::get(['userid'=>$this->uid,'article_id'=>$noticeId,'type'=>1]);
        if(!empty($fabulous)){
            return json(['flog'=>0,'msg'=>'已经赞过了']);
        }

        $insert = array();
        $insert['article_id'] = $noticeId;
        $insert['userid'] = $this->uid;
        $insert['type'] = 1;
        $insert['ctime'] = time();

        $Fabulous = Fabulous::create($insert);

        return json(['flog'=>1,'msg'=>'点赞成功！']);
    }
}