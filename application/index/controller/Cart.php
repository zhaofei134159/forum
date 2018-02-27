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
        $type = input('get.type');
        if(empty($type)){
            $type = 'all';
        }

        $plate = Plate::get(['id'=>$plateId]);

        # 置顶的一些帖子 
        $topWhere = array();
        $topWhere['is_del'] = 0;
        $topWhere['plateId'] = $plateId;
        $topWhere['is_top'] = 1;
        $topCarts = model_cart::where($topWhere)->select();        

        # 不置顶的一些 帖子
		$where = array();
		$where['is_del'] = 0;
		$where['plateId'] = $plateId;
        $where['is_top'] = 0;
        if($type=='new'){
            $where['ctime'] = array('>=',time()-3600);
        }else if($type=='hot'){
            $where['is_hot'] = 1;
        }else if($type=='elite'){
            $where['is_elite'] = 1;
        }
		$carts = model_cart::where($where)->order('ctime','desc')->paginate(20, false,[
                'query'=>['plateId'=>$plateId],
            ]);        
        $page = $carts->render();

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $data = array(
                'topCarts'=>$topCarts,
        		'carts'=>$carts,
        		'page'=>$page,
                'users'=>$users,
                'plate'=>$plate,
        		'plateId'=>$plateId,
                'type'=>$type,
        	);
        return $this->view->fetch('index',$data);
	}

    # 修改状态
    public function removeCart(){
        $post = input('post.');
        $cartId = $post['cartId'];
        $str = $post['str'];

        $cart = model_cart::get(['id'=>$cartId,'is_del'=>0]);
        if(empty($cart)){
            return json(['flog'=>0, 'msg'=>'帖子出错了!']);
        }    

        $update = array();
        $update['is_'.$str] = 0;

        model_cart::where('id',$cartId)->update($update);
        
        return json(['flog'=>1, 'msg'=>'success!']);
    }

    public function addCart(){
        $post = input('post.');
        $cartId = $post['cartId'];
        $str = $post['str'];

        $cart = model_cart::get(['id'=>$cartId,'is_del'=>0]);
        if(empty($cart)){
            return json(['flog'=>0, 'msg'=>'帖子出错了!']);
        }    

        $update = array();
        $update['is_'.$str] = 1;

        model_cart::where('id',$cartId)->update($update);

        return json(['flog'=>1, 'msg'=>'success!']);
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

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        model_cart::where('id',$cartId)->update(['see'=>intval($cart['see'])+1]);
        
        $data = array(
                'plateId'=>$plateId,
                'cart'=>$cart,
                'users'=>$users,
            );
        return $this->view->fetch('seeCart',$data);
    }


}