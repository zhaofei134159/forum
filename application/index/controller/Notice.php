<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Cate;
use app\index\model\Plate;
use app\index\model\Notice as modelNotice;

use think\Session;
use think\Config;
use smtp;


class Notice extends Common
{	

	public function __construct(){
		parent::__construct();
		
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

        $data = array(
                'notice'=>$notice,
            );
        return $this->view->fetch('NoticeDetail',$data);
    }
}