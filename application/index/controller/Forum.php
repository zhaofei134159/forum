<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Cate;

use think\Session;
use think\Config;
use smtp;


class Forum extends Common
{	

	public function __construct(){
		parent::__construct();
		
	}

    
    public function index(){
        
        return $this->view->fetch('index');
    }

    # 申请版主
    public function applyModerator(){
        if(!Session::get('login_id','forum_home')){
            $this->redirect('login/index');
        }

        $cate = Cate::all(['is_del'=>0]);

        return $this->view->fetch('applyModerator');
    }

}