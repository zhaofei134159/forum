<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Userinfo;
use app\index\model\Province;
use app\index\model\City;
use app\index\model\Area;


use think\Session;
use think\Config;


// 个人中心
class Ucenter extends Common
{	
    public $uid = 0;
    public $userid = 0;

	public function __construct(){
		parent::__construct();

        if(!Session::get('login_id','forum_home')){
            $this->redirect('login/index');
        }

        $this->uid = Session::get('login_id','forum_home');
        $this->userid =  Session::get('login_id','forum_home');

	}

    
    // 个人资料
    public function index(){
    	$uid = $this->uid;
    	
    	$get = input('get.');
    	if($get['uid']){
    		$uid = $get['uid'];
    	}
    	$user = User::get(['id'=>$uid]);
        $userinfo = Userinfo::get(['uid'=>$uid]);

    	$data = array(
    			'user'=>$user
    		);
        return $this->view->fetch('index',$data);
    }

    // 个人设置
    public function setting(){
    	return $this->view->fetch('setting');
    }

    public function editUser(){
        $uid = $this->uid;
        $user = User::get(['id'=>$uid]);
        $userinfo = Userinfo::get(['uid'=>$uid]);


        $data = array(
                'user'=>$user,
                'userinfo'=>$userinfo
            );
        return $this->view->fetch('editUser',$data);
    }

    public function saveUserHeadimg(){
        $uid = $this->uid;
        $user = User::get(['id'=>$uid]);
        if(empty($user)||$user['is_del']==1){
            $this->redirect('ucenter/editUser');
        }

        $post = input('post.');

        $update = array();
        $update['nikename'] = $post['nikename'];

        $file = request()->file('headimg');
        if($file){
            $info = $file->move(ROOT_PATH.'public'.DS.'uploads'.DS.'headimg');
            if($info){
               $update['headimg'] = 'uploads'.DS.'headimg'.DS.$info->getSaveName();
            }else{
                Log::log($uid.' 前台上传用户 头像错误 ：'.$file->getError());
                $update['headimg'] = '';
            }
        }

        User::where('id', $uid)->update($update); 

        $this->redirect('ucenter/editUser');
    }

    public function basicUserInfo(){
        $uid = $this->uid;

        $user = User::get(['id'=>$uid]);
        $userinfo = Userinfo::get(['uid'=>$uid]);

        $birthday = explode('-',$userinfo['birthday']);
        
        $province = Province::all();
        $data = array(
                'user'=>$user,
                'userinfo'=>$userinfo,
                'province'=>$province,
                'year'=>date('Y'),
                'data_year'=>$birthday[0],
                'data_month'=>$birthday[1],
                'homeplace'=>json_decode($userinfo['homeplace'],true),
                'domicile'=>json_decode($userinfo['domicile'],true),
            );
        return $this->view->fetch('basicUserInfo',$data);
    }

    public function proCitys(){
        $post = input('post.'); 
        $proCode = $post['proCode'];

        if(empty($proCode)){
            return json(['flog'=>0, 'msg'=>'没有选择省份']);
        }

        $Citys = City::where('provincecode',$proCode)->select();

        return json(['flog'=>1, 'msg'=>'成功','data'=>$Citys]);
    }

    public function cityAreas(){
        $post = input('post.'); 
        $cityCode = $post['cityCode'];

        if(empty($cityCode)){
            return json(['flog'=>0, 'msg'=>'没有选择城市']);
        }

        $Areas = Area::where('citycode',$cityCode)->select();

        return json(['flog'=>1, 'msg'=>'成功','data'=>$Areas]);
    }

    public function saveUserBasic(){

        $uid = $this->uid;
        $user = User::get(['id'=>$uid]);
        if(empty($user)||$user['is_del']==1){
            $this->redirect('ucenter/editUser');
        }

        $post = input('post.');

        $sex = $post['sex'];
        $birthday = $post['year'].'-'.$post['month'];
        $bloodtype = $post['bloodtype'];

        $homeplace['province'] = $post['province'];
        $homeplace['city'] = $post['city'];
        $homeplace['area'] = $post['area'];

        $domicile['province'] = $post['live_province'];
        $domicile['city'] = $post['live_city'];
        $domicile['area'] = $post['live_area'];


        $data = array();
        $data['sex'] = $sex;
        $data['birthday'] = $birthday;
        $data['bloodtype'] = $bloodtype;
        $data['homeplace'] = json_encode($homeplace);
        $data['domicile'] = json_encode($domicile);

        $userinfo = Userinfo::get(['uid'=>$uid]);
        if(empty($userinfo)){
            $data['uid'] = $uid;
            Userinfo::create($data);
        }else{
            Userinfo::where('uid', $uid)->update($data); 
        }


        $this->redirect('ucenter/basicUserInfo');
    }

    public function detailUserInfo(){

        
        $data = array();
        return $this->view->fetch('detailUserInfo',$data);   
    }

}