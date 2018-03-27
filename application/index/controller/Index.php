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
use app\index\model\Message;
use app\index\model\Notice;



class Index extends Common
{
    public $uid;

	public function __construct(){
		parent::__construct();
        $this->uid = Session::get('login_id','forum_home');
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
        $plateHome = Plate::where(['is_del'=>0,'is_check'=>1,'is_home'=>1])->limit(5)->select(); 

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $friend = Friend::where(['is_del'=>0,'is_check'=>1])->order('ctime','desc')->limit(8)->select();

        # 公告
        $notices = Notice::where(['is_del'=>0,'noticeId'=>0])->order('see','desc')->limit(4)->select();

        $data = array(
        		'plates'=>$plates,
        		'cates'=>$cates,
        		'zhuCates'=>$zhuCates,
        		'plateHome'=>$plateHome,
        		'users'=>$users,
                'friend'=>$friend,
                'notices'=>$notices,
        	);
        return $this->view->fetch('index',$data);
    }

    # 友情链接列表
    public function FriendList(){
        $uid = input('get.uid');

        $sel = array();

        $where = array();
        $where['is_del'] = 0;
        if(isset($uid)&&!empty($uid)){
            $where['adminid'] = $uid;
            $sel =  [
                        'query'=>['uid'=>$uid],
                    ];
        }else{
            $where['is_check'] = 1;
        }
        $friends = Friend::where($where)->order('ctime','desc')->paginate(16, false, $sel);        
        $page = $friends->render();

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $data = array(
                'friends'=>$friends,
                'page'=>$page,
                'users'=>$users,
            );

        return $this->view->fetch('FriendList',$data);
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


    public function search(){
        $get = input('get.');
        $searchText = $get['searchText'];
        $get['datetime'] = !empty($get['datetime'])?$get['datetime']:'';
        $username = $get['userNikename'];

        $where = array();
        $where['is_del'] = 0;
        $where['cartId'] = 0;
        if(!empty($get['datetime'])){
            $dateS = explode(' - ',$get['datetime']);
            $timestart = strtotime($dateS[0]);
            $timeend = strtotime($dateS[1])+86400-1;
            $where['ctime'] = ['>=',$timestart];
            $where['ctime'] = ['<=',$timeend];
        }
        echo 123123;
        var_dump($get['datetime']);
        if(!empty($username)){
            $user = User::where('nikename','like','%'.$username.'%')->find();
            $where['userid'] = $user['id'];
        }
        if(!empty($searchText)){
            $where['title|content'] = ['like','%'.$searchText.'%'];
        }
        # 帖子
        $carts = Cart::where($where)->paginate(20, false);
        // echo Cart::getLastSql();
        foreach($carts as $key=>$cart){
              $reply = Cart::where(['cartId'=>$cart['id'],'is_del'=>0])->count();
              $carts[$key]['reply'] = $reply;
        } 

        $page = $carts->render();

        # 用户名
        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $data = array(
                'carts'=>$carts,
                'page'=>$page,
                'users'=>$users,
                'get'=>$get,
            );
        return $this->view->fetch('search',$data);

    }
}
