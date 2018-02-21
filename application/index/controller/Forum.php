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

        $cateArr = Cate::all(['is_del'=>0]);
        if($cateArr) {
            $cateArr = collection($cateArr)->toArray();
        }
        $cateGroup = cateSplit($cateArr);

        $data = array(
                'cateGroup'=>$cateGroup,
            );
        return $this->view->fetch('applyModerator',$data);
    }

}