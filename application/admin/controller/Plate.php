<?php
namespace app\admin\controller;

use app\admin\model\Admin as Admin_user;
use app\admin\model\User as Home_user;
use app\admin\model\Cate as Admin_cate;
use app\admin\model\Plate;
use think\Session;
use think\Config;

/**
* 论坛 版块 展示
**/
class Plate extends Common
{   
    public function __construct(){
        parent::__construct();
       
        if(!Session::get('login_id','forum_admin')){
            $this->redirect('login/login');
        }
    }
    
    public function index(){
        $post = input('post.');

        $name = $post['name'];
        $is_del = ($post['is_del'])?$post['is_del']:'-1';

        $where = array();
        if(!empty($name)){
            $where['name'] = $name; 
        }
        if($is_del!='-1'){ 
            $where['is_del'] = $is_del; 
        }

        $plate = Plate::where($where)->order('ctime','desc')->paginate(10, false);
        $page = $plate->render();

        $data = array(
                'plate'=>$plate,
                'page'=>$page,
                'post'=>$post,
            );
        return $this->view->fetch('index',$data);
    }
}