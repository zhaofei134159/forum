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
        $parentCates = objToArray($parentCates);

        $sonCate = array();
        if(!empty($CateId)){
			$sonCate = model_cate::where(['is_del'=>0,'parent_id'=>$CateId])->select();
        	$sonCate = objToArray($sonCate);
		}

		$data = array(
				'CateId'=>$CateId,
				'parentCates'=>$parentCates,
				'sonCate'=>$sonCate,
			);
        return $this->view->fetch('index',$data);

    }

    # 某个分类下的版块
    public function CatePlate(){
    	$CateId = input('get.CateId');
    	$parentId = input('get.parentId');
    	if(empty($CateId)||empty($parentId)){
        	$this->redirect('Cate/index');
    	}

		$parentCate = model_cate::get(['is_del'=>0,'id'=>$parentId]);
		$sonCates = model_cate::where(['is_del'=>0,'parent_id'=>$parentId])->select();
		
		# 版块
		$plateList = Plate::where(['cateid'=>$CateId,'is_check'=>'1','is_del'=>0])->select();

        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

		$data = array(
				'CateId'=>$CateId,
				'parentId'=>$parentId,
				'parentCate'=>$parentCate,
				'sonCates'=>$sonCates,
				'plateList'=>$plateList,
				'users'=>$users,
			);
        return $this->view->fetch('CatePlate',$data);

    }
}