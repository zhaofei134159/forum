<?php
namespace app\index\controller;

use think\View;
use think\Request;
use think\Session;
use think\Controller;
use think\Log;

use app\index\model\Common as modelCommon;


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

            $account = Account::get(['id' => Session::get('login_id','forum_home')]);
            if(empty($account)){
                // 找不到用户报错页面
                $this->error('找不到用户,联系管理员！', 'login/index');
            }

            $this->assign('vest_user',$account);
            
            // Log::log('这是common.php 控制器 时间为：'.(time()-$time));

            //全部权限 直接返回就好
            if($account['is_master']==1&&$account['power_group_id']==0){
                return ;
            }

            $where = array(
                'lf_b2b_power_group.state'=>1,
                'lf_b2b_power_group.id'=>$account['power_group_id'],
            );
            $field = 'lf_b2b_power.id,lf_b2b_power.name,lf_b2b_power.controller,lf_b2b_power.function';
            $join = array();
            $join[] = array('lf_b2b_power_relation_group','lf_b2b_power_group.id = lf_b2b_power_relation_group.power_group_id','left');
            $join[] = array('lf_b2b_power','lf_b2b_power_relation_group.power_id = lf_b2b_power.id','left');
            $powers = Power_group::join($join)->field($field)->where($where)->select();


            $user_power = array();
            foreach($powers as $power){
                $power_cont = ucfirst($power['controller']);
                $user_power[$power_cont][] = $power['function'];
            }
          
            // 对不起， 没有权限
            if($controller=='Index' && $action=='index'){

            }else if(empty($powers)){
                $this->error('权限已经不启用了,联系管理员！', 'login/index');
            }else if(!isset($user_power[$controller])){
                $this->error('没有权限', '/');
            }else if(!in_array($action,$user_power[$controller])){
                $this->error('没有权限', '/');
            }

            
            $this->assign('user_power',$user_power);

        }

    }

}
