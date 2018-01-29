<?php
namespace app\admin\controller;

use think\View;
use think\Request;
use think\Session;
use think\Controller;
use think\Log;

use app\admin\model\Common as modelCommon;
use app\admin\model\User;


class Common extends Controller
{
	public $view;

	public function __construct(){
    
		//实例化view
		$this->view = new View();

        $request = \think\Request::instance();
        $this->assign('controller',$request->controller());
        $this->assign('action',$request->action());


        if(Session::get('login_id','forum_home')){
        	$this->assign('userid',Session::get('login_id','forum_home'));
        	$this->assign('username',Session::get('name','forum_home'));
            // 记录每个人都干什么了 暂时不加日志
            // Log::log();
        }

        $this->init($request->controller(),$request->action());
        // echo Power_group::getLastSql();
	}

    //获取当前登录人的权限
    public function init($controller,$action){
        // $time = time();
        if(Session::get('login_id','forum_home')){

            $account = User::get(['id' => Session::get('login_id','forum_home')]);
            
            $this->assign('user',$account);
            
        }

    }

}
