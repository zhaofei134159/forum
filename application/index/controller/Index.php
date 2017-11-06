<?php
namespace app\index\controller;

use think\Session;
use think\Log;
use app\index\model\Common as modelCommon;
use think\Loader;

class Index extends Common
{
	public function __construct(){
		parent::__construct();
		
        // if(!Session::get('login_id','forum_home')){
        // 	$this->redirect('login/index');
        // }
	}


    public function index()
    {
        // var_dump($userid);die;
        $data = array();
        return $this->view->fetch('index',$data);
    }
}
