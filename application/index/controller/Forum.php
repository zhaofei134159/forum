<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Cate;
use app\index\model\Plate;

use think\Session;
use think\Config;
use smtp;


class Forum extends Common
{	

	public function __construct(){
		parent::__construct();
		
	}

    
    public function index(){
        
        return $this->view->fetch('index');
    }

    # 我的版块列表
    public function myPlateList(){
        if(!Session::get('login_id','forum_home')){
            $this->redirect('login/index');
        }
        $uid = Session::get('login_id','forum_home');

        $where = array();
        $where['userid'] = $uid;

        $myPlate = Plate::where($where)->order('ctime','desc')->paginate(10, false);        
        $page = $myPlate->render();

        $data = array(
                'myPlate'=>$myPlate,
                'page'=>$page,
                // 'post'=>$post,
            );
        return $this->view->fetch('myPlateList',$data);
    }


    # 申请版主
    public function applyModerator(){
        if(!Session::get('login_id','forum_home')){
            $this->redirect('login/index');
        }

        $cateArr = Cate::all(['is_del'=>0]);

        $cateArr = objToArray($cateArr);
        $cateGroup = cateSplit($cateArr);

        $parentCate = array_keys($cateGroup);

        $data = array(
                'cateGroup'=>$cateGroup,
                'parentCate'=>$parentCate,
            );
        return $this->view->fetch('applyModerator',$data);
    }

    # 查询 版块名称
    public function findPlateName(){
        $post = input('post.');
        $name = $post['name'];

        $where = array('name'=>$name);
        $plate = Plate::get($where);

        if(!empty($plate)){
            return json(['flog'=>0, 'msg'=>'存在相同名称的版块','data'=>$plate]);
        }
        return json(['flog'=>1, 'msg'=>'没有存在相同名称的版块']);
    } 

    # 申请版主 保存
    public function saveApplyPlate(){
        if(!Session::get('login_id','forum_home')){
            $this->redirect('login/index');
        }
        $data = array();

        $data['userid'] = Session::get('login_id','forum_home');

        $post = input('post.');
        $headimg = request()->file('headimg');
        $backimg = request()->file('backimg');

        $saveHeadPath = 'uploads'.DS.'forum'.DS.'headimg';
        $data['headimg'] = uploadFile($headimg,$saveHeadPath);

        $saveBackPath = 'uploads'.DS.'forum'.DS.'backimg';
        $data['backimg'] = uploadFile($backimg,$saveBackPath);
        
        $data['name'] = $post['name'];
        $data['info'] = $post['info'];
        $data['cateid'] = $post['cateId'];
        $data['ctime'] = time();
        $data['utime'] = time();

        Plate::create($data);

        $this->redirect('forum/myPlateList');
    }

    # 版块 编辑
    public function myPlateEdit(){
        if(!Session::get('login_id','forum_home')){
            $this->redirect('login/index');
        }

        $cateArr = Cate::all(['is_del'=>0]);
        $cateArr = objToArray($cateArr,'id');

        $get = input('get.');
        $id = $get['id'];

        $where = array('id'=>$id);
        $plate = Plate::get($where);

        $data = array(
                'cateArr'=>$cateArr,
                'plate'=>$plate,
            );
        return $this->view->fetch('myPlateEdit',$data);
    }

    public function saveEditPlate(){
        if(!Session::get('login_id','forum_home')){
            $this->redirect('login/index');
        }

        $post = input('post.');
        $headimg = request()->file('headimg');
        $backimg = request()->file('backimg');
        var_dump($headimg);
        var_dump($backimg);
        
        $saveHeadPath = 'uploads'.DS.'forum'.DS.'headimg';
        $data['headimg'] = uploadFile($headimg,$saveHeadPath);

        $saveBackPath = 'uploads'.DS.'forum'.DS.'backimg';
        $data['backimg'] = uploadFile($backimg,$saveBackPath);
        
        $data['info'] = $post['info'];
        $data['ctime'] = time();

        $plateId = $post['plateId'];

        $accountData = Plate::where('id', $plateId)->update($data); 

        $this->redirect('forum/myPlateList');
    }


}