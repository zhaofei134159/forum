<?php
namespace app\admin\controller;

use app\admin\model\Admin as Admin_user;
use app\admin\model\User as Home_user;
use app\admin\model\Cate as Admin_cate;
use think\Session;
use think\Config;


class Cate extends Common
{	
	public function __construct(){
		parent::__construct();
	   
        if(!Session::get('login_id','forum_admin')){
        	$this->redirect('login/login');
        }
	}
    
    public function index(){

        $where = array();
        $where['is_del'] = 0;
        $cate = Admin_cate::all($where);
        $cateArr = array();
        $i = 0;
        $j = 0;
        foreach($cate as $val){
            if($val['parent_id']==0){
                $cateArr[$i]['text'] = $val['name'];
                $cateArr[$i]['nodeId'] = $val['id'];
                $cateArr[$i]['parent_id'] = $val['parent_id'];
                $cateArr[$i]['nodes'] = array();
            }
            foreach($cate as $c){
                if($c['parent_id']==$val['id']){
                    $cateArr[$i]['nodes'][$j]['text'] = $c['name'];
                    $cateArr[$i]['nodes'][$j]['nodeId'] = $c['id'];
                    $cateArr[$i]['nodes'][$j]['parent_id'] = $c['parent_id'];
                    $j++;
                }
            }
            $i++;
        }

        // var_dump($cateArr);die;

        $data = array(
                'cateStr'=>json_encode($cateArr),
            );
        return $this->view->fetch('index',$data);
    }

    public function cateAdd(){
        $post = input('post.');
        $parentId = $post['parentId'];
        $parentName = $post['parentName'];
        $cateName = $post['cateName'];
        $cateId = $post['cateId'];

        $cate = Admin_cate::get(['id'=>$cateId]);
        if(!empty($cateId)&&!empty($cate)){
            $id = $cate['id'];
            $updateArr = array(
                    'parent_id'=>$parentId,                    
                    'name'=>$cateName,
                    'utime'=>time(),                  
                );
            Admin_cate::where('id', $id)->update($updateArr); 

            return json(['flog'=>1, 'msg'=>'分类修改成功']);
        }else{
            $cateArr = array();
            $cateArr['name'] = $cateName;
            $cateArr['cateName'] = $parentId;
            $cateArr['ctime'] = time();
            $cateArr['utime'] = time();
            $cateId = Admin_cate::create($cateArr);

            return json(['flog'=>1, 'msg'=>'分类创建成功']);
        }
    }

    // 查找分类名称是否存在
    public function cateFind(){
        $post = input('post.');
        $selectCate = $post['selectCate'];
        $parentArr = array();


        $cate = Admin_cate::get(['name'=>$selectCate,'is_del'=>0]);
        if(empty($cate)){
            return json(['flog'=>0, 'msg'=>'分类不存在']);
        }
        if($cate['parent_id']==0){
            $data = array('cate'=>$cate,'parentArr'=>$parentArr);
            return json(['flog'=>1, 'msg'=>'父分类为 顶级分类','data'=>$data]);
        }

        $parentArr = Admin_cate::get(['id'=>$cate['parent_id'],'is_del'=>0]);

        $data = array('cate'=>$cate,'parentArr'=>$parentArr);
        return json(['flog'=>2, 'msg'=>'父分类','data'=>$data]);
   
    }

    public function findParentId(){
        $post = input('post.');
        $selectCate = $post['selectCate'];
        if(empty($selectCate)){
            return json(['flog'=>0, 'msg'=>'分类不存在','data'=>array()]);
        }

        $cate = Admin_cate::get(['name'=>$selectCate,'is_del'=>0]);
        if(empty($cate)){
            return json(['flog'=>0, 'msg'=>'分类不存在','data'=>array()]);
        }else{
            if($cate['parent_id']!=0){
                return json(['flog'=>0, 'msg'=>'该分类无法创建子类','data'=>array()]);
            }else{
                return json(['flog'=>1, 'msg'=>'分类已存在','data'=>$cate]);
            }
        }
    }
}