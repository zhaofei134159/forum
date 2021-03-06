<?php
namespace app\index\controller;

use think\View;
use think\Request;
use think\Session;
use think\Controller;
use think\Log;

use app\index\model\Common as modelCommon;
use app\index\model\Setting;
use app\index\model\User;
use app\index\model\Cate;
use app\index\model\Message;
use app\index\model\Security;

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
        $webSetting = Setting::get();
        if(!empty($webSetting)){
            if($webSetting['switch']==2){
                $this->error('当前网站正在维护中。。。。。');
            }
            $this->assign('webSetting',$webSetting);
        }

        $this->init($request->controller(),$request->action());
        // echo Power_group::getLastSql();
	}

    //获取当前登录人的权限
    public function init($controller,$action){
        // $time = time();
        $messages = array();
        $security = array();
        if(Session::get('login_id','forum_home')){

            $account = User::get(['id' => Session::get('login_id','forum_home')]);
            
            $this->assign('user',$account);

            $message_model = new Message();
            $messages = $message_model->userMessage(Session::get('login_id','forum_home'));

            # 密保
            $securityData = Security::all(['userid'=>Session::get('login_id','forum_home')]);
            if(!empty($securityData)){
                $security = objToArray($securityData);
            }
        }
        $this->assign('messages',$messages);
        $this->assign('security',$security);


        
        # 分类展示
        $topHeaderCates =  Cate::where(['is_del'=>0,'parent_id'=>0])->limit(3)->select();
        $topHeaderCates = objToArray($topHeaderCates);
        foreach($topHeaderCates as $cateId=>$cate){
            $sonCate = Cate::where(['is_del'=>0,'parent_id'=>$cateId])->select();
            $sonCate = objToArray($sonCate);
            $topHeaderCates[$cateId]['son'] = $sonCate;
        }
        $this->assign('topHeaderCates',$topHeaderCates);
    }

}
