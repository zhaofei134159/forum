<?php
namespace app\admin\controller;

use app\admin\model\Admin as Admin_user;
use app\admin\model\User as Home_user;
use app\admin\model\Cate as Admin_cate;
use app\admin\model\Plate as model_plate;
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
            $where['name'] = array('like','%'.$name.'%'); 
        }
        if($is_del!='-1'){ 
            $where['is_del'] = $is_del; 
        }

        $plates = model_plate::where($where)->order('ctime','desc')->paginate(10, false);
        $page = $plates->render();

        $cates = Admin_cate::all(['is_del'=>0]);
        $cates = objToArray($cates);

        $users = Home_user::all(['is_del'=>0]);
        $users = objToArray($users);

        $data = array(
                'plates'=>$plates,
                'page'=>$page,
                'post'=>$post,
                'cates'=>$cates,
                'users'=>$users,
            );
        return $this->view->fetch('index',$data);
    }


    public function isDelPlate(){
        $post = input('post.');
        $id = $post['id'];

        $user = model_plate::get(['id'=>$id]);
        $update = array();
        if(empty($user['is_del'])){
            $update['is_del'] = 1;
        }else if($user['is_del']==1){
           $update['is_del'] = 0;
        }
        model_plate::where('id', $id)->update($update);

        return json(['flog'=>1, 'msg'=>$update['is_del']]);
    }

    public function isCheckPlate(){
        $post = input('post.');
        $id = $post['id'];

        $user = model_plate::get(['id'=>$id]);
        $update = array();
        if(empty($user['is_check'])||$user['is_check']==2){
            $update['is_check'] = 1;
        }else if($user['is_check']==1){
           $update['is_check'] = 2;
        }
        model_plate::where('id', $id)->update($update);

        return json(['flog'=>1, 'msg'=>$update['is_check']]);
    }

    public function editPlate(){
        $get = input('get.');

        $PlateInfo = array();
        if(!empty($get['plateId'])){
            $plateId = $get['plateId'];
            $PlateInfo =  model_plate::get(['id'=>$plateId]);
        }


        # 用户
        $userArr = Home_user::all(['is_del'=>0]);
        $userArr = objToArray($userArr);

        # 分类
        $cate = Admin_cate::all(['is_del'=>0]);

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
        $cateStr = json_encode($cateArr);

        $data = array(
                'PlateInfo'=>$PlateInfo,
                'cateStr'=>$cateStr,
                'userArr'=>$userArr
            );
        return $this->view->fetch('editPlate',$data);
    }

    public function savePlate(){
        $post = input('post.');    
        $plateId = $post['plateId'];

        if(empty($plateId)){

        }else{

            $headimg = request()->file('headimg');
            $backimg = request()->file('backimg');

            if($headimg){
                $saveHeadPath = 'uploads'.DS.'forum'.DS.'headimg';
                $data['headimg'] = uploadFile($headimg,$saveHeadPath);
            }
            if($backimg){
                $saveBackPath = 'uploads'.DS.'forum'.DS.'backimg';
                $data['backimg'] = uploadFile($backimg,$saveBackPath);
            }
            
            $data['info'] = $post['info'];
            $data['utime'] = time();

            $accountData = model_plate::where('id', $plateId)->update($data); 

        }

        $this->redirect('plate/index');
    }
}