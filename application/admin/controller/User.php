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
    	$where = array();
        
        $users = Home_user::all($where);
        $data = array(
                'users'=>$users,
            );
        return $this->view->fetch('index',$data);
    }
}