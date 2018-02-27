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

		$where = array();
		$where['is_del'] = 0;
		$where['plateId'] = $plateId;

		$carts = model_cart::where($where)->order('ctime','desc')->paginate(20, false);        
        $page = $carts->render();

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $data = array(
        		'carts'=>$carts,
        		'page'=>$page,
                'users'=>$users,
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

	public function saveCart(){
        $userid = Session::get('login_id','forum_home');
		if(!$userid){
            $this->redirect('login/index');
        }
        $post = input('post.');

        $plateId = $post['plateId'];
        $name = $post['name'];
        $content = $post['content'];

        $data = array();
        $data['plateId'] = $plateId;
        $data['title'] = $name;
        $data['content'] = $content;
        $data['userid'] = $userid;
        $data['cartId'] = 0;
        $data['see'] = 0;
        $data['ctime'] = time();
        $data['utime'] = time();

        $cart = model_cart::create($data);

        $this->redirect('cart/seeCart',['cartId'=>$cart['id'],'plateId'=>$plateId]);
	}


    public function seeCart(){
        $get = input('input.');
        $cartId = input('param.cartId');
        $plateId = input('param.plateId');

        $cart = model_cart::get(['id'=>$cartId]);

        model_cart::where('id',$cartId)->update(['see'=>intval($cart['see'])+1]);
        
        $data = array(
                'plateId'=>$plateId
                'cart'=>$cart,
            );
        return $this->view->fetch('seeCart',$data);
    }


}