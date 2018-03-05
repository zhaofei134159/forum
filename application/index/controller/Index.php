<?php
namespace app\index\controller;

use think\Session;
use think\Log;
use think\Loader;

use app\index\model\Common as modelCommon;
use app\index\model\Cate;
use app\index\model\Plate;
use app\index\model\Cart;
use app\index\model\User;
use app\index\model\Friend;



class Index extends Common
{
	public function __construct(){
		parent::__construct();
	}


    public function index(){

        $cart_model = new Cart();
        $plates = $cart_model->PlateCart();

        $cates = Cate::where(['is_del'=>0])->select();
        $cates = objToArray($cates);
        $zhuCates = 0;
        foreach($cates as $cate){
        	if($cate['parent_id']==0){
        		$zhuCates++;
        	}
        }

        # 版主
        $plateUser = Plate::where(['is_del'=>0,'is_check'=>1,'is_home'=>1])->group('userid')->limit(4)->select(); 
        $plateUser = objToArray($plateUser);

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $friend = Friend::where(['is_del'=>0,'is_check'=>1])->limit(15)->select();

        $data = array(
        		'plates'=>$plates,
        		'cates'=>$cates,
        		'zhuCates'=>$zhuCates,
        		'plateUser'=>$plateUser,
        		'users'=>$users,
                'friend'=>$friend,
        	);
        return $this->view->fetch('index',$data);
    }

    # 申请友情链接
    public function ApplyFriend(){

        if(!Session::get('login_id','forum_home')){
            $this->redirect('login/index');
        }
        $data = array();

        return $this->view->fetch('ApplyFriend',$data);
    }

    public function saveApplyFriend(){
        $post = input('post.');

        if(!preg_match('/(http:\/\/)|(https:\/\/)/i',$post['link'])) {
            $post['link'] = 'http://'.$post['link'];
        }

        $file = request()->file('linkimg');
        if($file){
            $info = $file->move(ROOT_PATH.'public'.DS.'uploads'.DS.'linkimg');
            if($info){
               $post['linkimg'] = 'uploads'.DS.'linkimg'.DS.$info->getSaveName();
            }else{
                $post['linkimg'] = '';
                Log::log(Session::get('login_id','forum_home').' 上传链图 '.$post['name'].' 错误 ：'.$file->getError());
            }
        }

        $post['is_check'] = 0;
        $post['adminid'] = Session::get('login_id','forum_home');
        $post['ctime'] = time();
        $post['utime'] = time();
        Friend::create($post);
        
        $this->redirect('index/index');
    }
}
