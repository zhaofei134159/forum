<?php
namespace app\index\controller;

use think\Session;
use think\Log;
use think\Loader;

use app\index\model\Common as modelCommon;
use app\index\model\Cate;
use app\index\model\Plate;
use app\index\model\Cart;

class Index extends Common
{
	public function __construct(){
		parent::__construct();
	}


    public function index(){

        $cart_model = new Cart();
        $plates = $cart_model->PlateCart();

        $cates = Cate::where(['is_del'=>0])->select();
        $cates = objToArray($cates);
        $zhuCates = 0;
        foreach($cates as $cate){
        	if($cate['parent_id']==0){
        		$zhuCates++;
        	}
        }

        $data = array(
        		'plates'=>$plates,
        		'cates'=>$cates,
        		'zhuCates'=>$zhuCates,
        	);
        return $this->view->fetch('index',$data);
    }
}
