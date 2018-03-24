<?php
namespace app\admin\controller;

use think\Session;
use think\Log;
use app\admin\model\Admin as Admin_user;
use app\admin\model\User as Home_user;
use app\admin\model\Common as modelCommon;
use app\admin\model\Admin;
use app\admin\model\Notice as modelNotice;
use app\index\model\NoticeReply;
use app\index\model\Fabulous;

use think\Loader;
use think\Config;

class Notice extends Common
{
	public function __construct(){
		parent::__construct();
		
        if(!Session::get('login_id','forum_admin')){
        	$this->redirect('login/login');
        }
	}


    public function index(){
        $get = input('get.');

        $where = array();
        if(!empty($get['title'])){
            $where['title'] = array('like','%'.$get['title'].'%'); 
        }
        if(isset($get['is_del'])&&$get['is_del']!='-1'){ 
            $where['is_del'] = $get['is_del']; 
        }
        $where['noticeId'] = 0;
        $notices = modelNotice::where($where)->order('ctime','desc')->paginate(10, false);
        $page = $notices->render();

        $noticeFabulous = NoticeReply::get_query('select article_id,count(1) as count from forum_fabulous where type=1 group by article_id');
        $noticeFabulous = objToArray($noticeFabulous,'article_id');
        $noticeReply = NoticeReply::get_query('select notice_id,count(1) as count from forum_notice_reply where is_del=0 group by notice_id');
        $noticeReply = objToArray($noticeReply,'notice_id');

        $users = Home_user::all(['is_del'=>0]);
        $users = objToArray($users);

        $data = array(
                'notices'=>$notices,
                'page'=>$page,
                'noticeFabulous'=>$noticeFabulous,
                'noticeReply'=>$noticeReply,
                'users'=>$users,
                'get'=>$get,
            );

        return $this->view->fetch('index',$data);
    }

    public function isDelNotice(){

        $post = input('post.');
        $id = $post['id'];

        $notice = modelNotice::get(['id'=>$id]);
        $update = array();
        if(empty($notice['is_del'])){
            $update['is_del'] = 1;
        }else if($notice['is_del']==1){
           $update['is_del'] = 0;
        }
        modelNotice::where('id', $id)->update($update);

        return json(['flog'=>1, 'msg'=>$update['is_del']]);
    }

    # 公告编辑
    public function NoticeEdit(){
        $noticeId = input('get.noticeId');

        $notice = array();
        if(!empty($noticeId)){
            $notice = modelNotice::get(['id'=>$noticeId]);
        }

        $data = array(
                'notice'=>$notice,
                'noticeId'=>$noticeId,
            );

        return $this->view->fetch('NoticeEdit',$data);

    }

    # 保存公告
    public function saveNotice(){
        $post = input('post.');
        $noticeId = $post['noticeId'];
        if(empty($noticeId)){

            $data = array();
            $data['title'] = $post['title'];
            $data['content'] = $post['content'];
            $data['userid'] = 1;
            $data['is_del'] = 0;
            $data['ctime'] = time();
            $data['utime'] = time();

            $headimg = request()->file('headimg');
            if($headimg){
                $saveHeadPath = 'uploads'.DS.'notice'.DS.'headimg';
                $data['headimg'] = uploadFile($headimg,$saveHeadPath);
            }
           
            $place = modelNotice::insert($data); 
        }else{

            $headimg = request()->file('headimg');
           

            if($headimg){
                $saveHeadPath = 'uploads'.DS.'notice'.DS.'headimg';
                $data['headimg'] = uploadFile($headimg,$saveHeadPath);
            }
          
            $data['title'] = $post['title'];
            $data['content'] = $post['content'];
            $data['utime'] = time();

            $place = modelNotice::where('id', $noticeId)->update($data); 
        }

        $this->redirect('Notice/index');
    }

    public function upload(){
        $file = request()->file('editormd-image-file');
        if($file){
            $saveCartPath = 'uploads'.DS.'notice'.DS.'head';
            $link = uploadFile($file,$saveCartPath);
        }
        
        return json(['success'=>1,'message'=>'成功1','url'=>'/'.$link]);
    }

    # 公告回复
    public function reply(){
        
    }
}
