<?php
namespace app\admin\controller;

use think\Session;
use think\Log;
use app\admin\model\Common as modelCommon;
use app\admin\model\Admin;
use think\Loader;
use think\Config;

class Index extends Common
{
	public function __construct(){
		parent::__construct();
		
        if(!Session::get('login_id','forum_admin')){
        	$this->redirect('login/login');
        }
	}


    public function index(){
        $admin_id = Session::get('login_id','forum_admin');
        $admin = Admin::get(['id' => $admin_id,'is_del'=>'0']);

        $data = array(
                'admin'=>$admin,
            );
        return $this->view->fetch('index',$data);
    }
}
