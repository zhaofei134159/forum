<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Cate;
use app\index\model\Plate;
use app\index\model\Cart as model_cart;

use think\Session;
use think\Config;
use smtp;

# 帖子
class Cart extends Common
{	

	public function __construct(){
		parent::__construct();	
	}

	public function index(){
		$plateId = input('get.plateId');

		$where = array()
		$where['is_del'] = 0;
		$where['plateId'] = $plateId

		$carts = model_cart::where($where)->order('ctime','desc')->paginate(20, false);        
        $page = $carts->render();

        $data = array(
        		'carts'=>$carts,
        		'page'=>$page,
        		'plateId'=>$plateId,
        	);
        return $this->view->fetch('index',$data);
	}

	# 发帖
	public function sendCart(){
        if(!Session::get('login_id','forum_home')){
            $this->redirect('login/index');
        }
        $plateId = input('get.plateId');

        $data = array(
        		'plateId'=>$plateId,
        	);
        return $this->view->fetch('sendCart',$data);
	}
}