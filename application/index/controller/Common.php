<?php
namespace app\index\controller;

use think\View;
use think\Request;
use think\Session;
use think\Controller;
use think\Log;

use app\index\model\Account;
use app\index\model\Power_group;
use app\index\model\Power;
use app\index\model\Lforders;
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

        if(Session::get('login_id','lf_b2b')){
        	$this->assign('adminId',Session::get('login_id','lf_b2b'));
        	$this->assign('adminEmail',Session::get('email','lf_b2b'));
            // 记录每个人都干什么了 暂时不加日志
            // Log::log();
        }

        
        //判断id归属
        $actionName =  request()-> action();
        //判断的 action
        if(in_array($actionName,array('order_detail','get_travelitinerary','pay_order','pay_finish')))
        {
            if(in_array($actionName,array('pay_order','pay_finish')))
            {
                $id = request()->only(['id'])['id'];
                $lforderOne = Lforders::where(['id'=>base64_decode($id)])->field('platform')->find();
            } 
            else
            {
                $id = request()->only(['id'])['id'];
                $lforderOne = Lforders::where(['id'=>$id])->field('platform')->find();
            }    

             
            $loginId = Session::get('login_id');
            $vestOrderPlatNameOne = modelCommon::get_query("SELECT v.`name` FROM lf_b2b_account a LEFT JOIN `lf_b2b_vest` v ON a.vest_id=v.id WHERE a.id='".$loginId."'");
             
            if($vestOrderPlatNameOne[0]['name']!=$lforderOne['platform']||!$lforderOne)
            {
                $this->error('请求订单错误!', 'lfhotel/index');
            }
             

        }

        

        $this->init($request->controller(),$request->action());
        // echo Power_group::getLastSql();
	}

    //获取当前登录人的权限
    public function init($controller,$action){
        // $time = time();
        if(Session::get('login_id','lf_b2b')){

            $account = Account::get(['id' => Session::get('login_id','lf_b2b')]);
            if(empty($account)){
                // 找不到用户报错页面
                $this->error('找不到用户,联系管理员！', 'login/login');
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
                $this->error('权限已经不启用了,联系管理员！', 'login/login');
            }else if(!isset($user_power[$controller])){
                $this->error('没有权限', '/');
            }else if(!in_array($action,$user_power[$controller])){
                $this->error('没有权限', '/');
            }

            
            $this->assign('user_power',$user_power);

        }

    }

}
