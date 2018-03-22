<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Cate as model_cate;
use app\index\model\Plate;
use app\index\model\Cart as model_cart;
use app\index\model\Follow;

use think\Session;
use think\Config;
use smtp;

# 帖子
class Cate extends Common
{	

	public function __construct(){
		parent::__construct();	
	}

	public function index(){
		$CateId = input('get.CateId');
        
        # 获取所有分类
		$parentCates = model_cate::where(['is_del'=>0,'parent_id'=>0])->select();



		$data = array(
				'parentCates'=>$parentCates,
			);
        return $this->view->fetch('index',$data);

    }
}