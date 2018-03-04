<?php
namespace app\admin\controller;

use app\admin\model\Admin as Admin_user;
use app\admin\model\User as Home_user;
use app\admin\model\Cate as Admin_cate;
use app\admin\model\Plate as Admin_plate;
use think\Session;
use think\Config;

/**
* 帖子 展示
**/
class Cart extends Common
{	
	public function __construct(){
		parent::__construct();
	   
        if(!Session::get('login_id','forum_admin')){
        	$this->redirect('login/login');
        }
	}
    
    public function index(){
        $get = input('get.');

        $where = array();
        if(!empty($get['title'])){
            $where['title'] = array('like','%'.$get['title'].'%'); 
        }
        if($get['plateId']){
            $where['plateId'] = $get['plateId'];
        }
        if($get['is_del']!='-1'){ 
            $where['is_del'] = $get['is_del']; 
        }

        $where['cartId'] = 0;
        $carts = Cart::where($where)->order('ctime','desc')->paginate(10, false);
        $page = $carts->render();

        $plates = Admin_plate::all(['is_del'=>0]);
        $plates = objToArray($plates);

        $users = Home_user::all(['is_del'=>0]);
        $users = objToArray($users);


        $data = array(
                'carts'=>$carts,
                'page'=>$page,
                'plates'=>$plates,
                'users'=>$users,
            );
        return $this->view->fetch('index',$data);
    }
}