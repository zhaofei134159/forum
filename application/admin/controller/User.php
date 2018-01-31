<?php
namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\User as Home_user;
use think\Session;
use think\Config;
use smtp;


class User extends Common
{	
	public function __construct(){
		parent::__construct();
	   
        if(!Session::get('login_id','forum_admin')){
        	$this->redirect('login/login');
        }
	}
    
    public function index(){
        $post = input('post.');

        $email = $post['email'];
        $nikename = $post['nikename'];
        $sex = ($post['sex'])?$post['sex']:0;
        $is_del = ($post['is_del'])?$post['is_del']:'-1';
        $is_activate = ($post['is_activate'])?$post['is_activate']:'-1';

    	$where = array();
        if(!empty($email)){
            $where['email'] = $email; 
        }
        if(!empty($nikename)){
            $where['nikename'] = $nikename; 
        }
        if(!empty($sex)){
            $where['sex'] = $sex;
        }
        if($is_del!='-1'){ 
            $where['is_del'] = $is_del; 
        }
        if($is_activate!='-1'){ 
            $where['is_activate'] = $is_activate; 
        }

        $users = Home_user::where($where)->order('ctime','desc')->paginate(10, false);
        $page = $users->render();

        $data = array(
                'users'=>$users,
                'page'=>$page,
                'post'=>$post,
            );
        return $this->view->fetch('index',$data);
    }
}