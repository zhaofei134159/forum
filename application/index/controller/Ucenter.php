<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Userinfo;
use app\index\model\Province;
use app\index\model\City;
use app\index\model\Area;
use app\index\model\Plate;
use app\index\model\Cart;


use think\Session;
use think\Config;


// 个人中心
class Ucenter extends Common
{	
    public $uid = 0;
    public $userid = 0;

    public $infos = array('sex','birthday','bloodtype','homeplace','domicile','maritalStatus','label','education','occupation','phone','edu_back','work_info');

	public function __construct(){
		parent::__construct();

        if(!Session::get('login_id','forum_home')){
            $this->redirect('login/index');
        }

        $this->uid = Session::get('login_id','forum_home');
        $this->userid =  Session::get('login_id','forum_home');

	}

    
    // 个人资料
    public function index(){
    	$uid = $this->uid;
    	$get = input('get.');
    	if($get['uid']){
    		$uid = $get['uid'];
    	}
        $plateId = $get['plateId'];

    	$user = User::get(['id'=>$uid]);

        $userinfo = Userinfo::get(['uid'=>$uid]);
        $infoRate = 0;
        if(!empty($userinfo)){
            foreach($this->infos as $info){
                if(!empty($userinfo[$info])&&$userinfo[$info]!='[]'){
                    $infoRate += 8.3;
                }
            }
        }
        $infoRate = round($infoRate);
        
        $cart_model = new Cart();
        $cartResults = $cart_model->userSendCartPlate($uid);
        
        $first_pid = $plateId;
        if(!isset($first_pid)&&!empty($cartResults['Plates'])){
            $first_pid = array_keys($cartResults['Plates'])[0]; 
        }

        
        $users = User::all(['is_del'=>0]);
        $users = objToArray($users);

        $result = $this->userPlateCart($first_pid,$uid);
        
    	$data = array(
    			'user'=>$user,
                'infoRate'=>$infoRate,
                'Plates'=>$cartResults['Plates'],
                'first_pid'=>$first_pid,
                'users'=>$users,
                'result'=>$result,
    		);
        return $this->view->fetch('index',$data);
    }

    public function userPlateCart($plateId,$uid){
        $plateId = $post['plateId'];
        $uid = $post['uid'];

        $cart_model = new model_cart();
        $result = $cart_model->userPalteCart($plateId,$uid);

        $data = array(
                'carts'=>$result['carts'],
                'page'=>$result['page'],
            );
        return $data;
    }

    // 个人设置
    public function setting(){
    	return $this->view->fetch('setting');
    }

    public function editUser(){
        $uid = $this->uid;
        $user = User::get(['id'=>$uid]);
        $userinfo = Userinfo::get(['uid'=>$uid]);


        $data = array(
                'user'=>$user,
                'userinfo'=>$userinfo
            );
        return $this->view->fetch('editUser',$data);
    }

    public function saveUserHeadimg(){
        $uid = $this->uid;
        $user = User::get(['id'=>$uid]);
        if(empty($user)||$user['is_del']==1){
            $this->redirect('ucenter/editUser');
        }

        $post = input('post.');

        $update = array();
        $update['nikename'] = $post['nikename'];

        $file = request()->file('headimg');
        if($file){
            $info = $file->move(ROOT_PATH.'public'.DS.'uploads'.DS.'headimg');
            if($info){
               $update['headimg'] = 'uploads'.DS.'headimg'.DS.$info->getSaveName();
            }else{
                Log::log($uid.' 前台上传用户 头像错误 ：'.$file->getError());
                $update['headimg'] = '';
            }
        }

        User::where('id', $uid)->update($update); 

        $this->redirect('ucenter/editUser');
    }

    # 基本资料
    public function basicUserInfo(){
        $uid = $this->uid;

        $user = User::get(['id'=>$uid]);
        $userinfo = Userinfo::get(['uid'=>$uid]);

        $birthday = explode('-',$userinfo['birthday']);
        
        $province = Province::all();
        $data = array(
                'user'=>$user,
                'userinfo'=>$userinfo,
                'province'=>$province,
                'data_year'=>$birthday[0],
                'data_month'=>$birthday[1],
                'homeplace'=>json_decode($userinfo['homeplace'],true),
                'domicile'=>json_decode($userinfo['domicile'],true),
            );
        return $this->view->fetch('basicUserInfo',$data);
    }

    public function proCitys(){
        $post = input('post.'); 
        $proCode = $post['proCode'];

        if(empty($proCode)){
            return json(['flog'=>0, 'msg'=>'没有选择省份']);
        }

        $Citys = City::where('provincecode',$proCode)->select();

        return json(['flog'=>1, 'msg'=>'成功','data'=>$Citys]);
    }

    public function cityAreas(){
        $post = input('post.'); 
        $cityCode = $post['cityCode'];

        if(empty($cityCode)){
            return json(['flog'=>0, 'msg'=>'没有选择城市']);
        }

        $Areas = Area::where('citycode',$cityCode)->select();

        return json(['flog'=>1, 'msg'=>'成功','data'=>$Areas]);
    }

    public function saveUserBasic(){

        $uid = $this->uid;
        $user = User::get(['id'=>$uid]);
        if(empty($user)||$user['is_del']==1){
            $this->redirect('ucenter/editUser');
        }

        $post = input('post.');
      
        $sex = '';
        $birthday = '';
        $bloodtype = '';
        $homeplace = array();
        $domicile = array();

        if(!empty($post['sex'])){
            $sex = $post['sex'];
        }
        if(!empty($post['year'])&&!empty($post['month'])){
            $birthday = $post['year'].'-'.$post['month'];
        }
        if(!empty($post['bloodtype'])){
            $bloodtype = $post['bloodtype'];
        }
        if(!empty($post['province'])&&!empty($post['city'])&&!empty($post['area'])){
            $homeplace['province'] = $post['province'];
            $homeplace['city'] = $post['city'];
            $homeplace['area'] = $post['area'];
        }
        if(!empty($post['live_province'])&&!empty($post['live_city'])&&!empty($post['live_area'])){
            $domicile['province'] = $post['live_province'];
            $domicile['city'] = $post['live_city'];
            $domicile['area'] = $post['live_area'];
        }

        $data = array();
        $data['sex'] = $sex;
        $data['birthday'] = $birthday;
        $data['bloodtype'] = $bloodtype;
        $data['homeplace'] = json_encode($homeplace);
        $data['domicile'] = json_encode($domicile);
        $data['utime'] = time();

        $userinfo = Userinfo::get(['uid'=>$uid]);
        if(empty($userinfo)){
            $data['ctime'] = time();
            $data['uid'] = $uid;
            Userinfo::create($data);
        }else{
            Userinfo::where('uid', $uid)->update($data); 
        }


        $this->redirect('ucenter/basicUserInfo');
    }


    # 详细资料
    public function detailUserInfo(){

        $uid = $this->uid;

        $userinfo = Userinfo::get(['uid'=>$uid]);
        $userinfo['labelArr'] = json_decode($userinfo['label'],true);
        
        $data = array(
                'userinfo'=>$userinfo,
            );
        return $this->view->fetch('detailUserInfo',$data);   
    }

    public function saveDetailUser(){

        $uid = $this->uid;
        $user = User::get(['id'=>$uid]);
        if(empty($user)||$user['is_del']==1){
            $this->redirect('ucenter/detailUserInfo');
        }

        $post = input('post.');

        $maritalStatus = '';
        $label = array();
        $education = '';
        $occupation = '';
        $phone = '';
       
        if(!empty($post['maritalStatus'])){
            $maritalStatus = $post['maritalStatus'];
        }
        if(!empty($post['label'])){
            $label = json_encode($post['label']);
        }
        if(!empty($post['education'])){
            $education = $post['education'];
        }
        if(!empty($post['education'])){
            $occupation = $post['occupation'];
        }
        if(!empty($post['education'])){
            $phone = $post['phone'];
        }

        $data = array();
        $data['maritalStatus'] = $maritalStatus;
        $data['label'] = $label;
        $data['education'] = $education;
        $data['occupation'] = $occupation;
        $data['phone'] = $phone;
        $data['utime'] = time();

        $userinfo = Userinfo::get(['uid'=>$uid]);
        if(empty($userinfo)){
            $data['uid'] = $uid;
            $data['ctime'] = time();
            Userinfo::create($data);
        }else{
            Userinfo::where('uid', $uid)->update($data); 
        }


        $this->redirect('ucenter/detailUserInfo');
    }


    # 教育背景
    public function eduBackInfo(){
        
        $uid = $this->uid;
        
        $userinfo = Userinfo::get(['uid'=>$uid]);
        $eduBack = json_decode($userinfo['edu_back'],true);
        if(!empty($eduBack)){
            foreach($eduBack as $key=>$data){
                $eduBack[$key]['educationStr'] = read_conf('edu_lavel')[$data['education']];
            }
        }
        $data = array(
                'userinfo'=>$userinfo,
                'eduBack'=>$eduBack,
            );
        return $this->view->fetch('eduBackInfo',$data); 
    }

    public function saveEduBack(){

        $uid = $this->uid;
        $user = User::get(['id'=>$uid]);
        if(empty($user)||$user['is_del']==1){
            return json(['flog'=>0, 'msg'=>'用户出错!']);
        }

        $post = input('post.');
        
        $education = $post['education'];
        $schoolName = $post['schoolName'];
        $year = $post['year'];

        $eduBack = array();
        $userinfo = Userinfo::get(['uid'=>$uid]);
        if(empty($userinfo['edu_back'])){
            $eduBack[0]['education'] = $education;
            $eduBack[0]['schoolName'] = $schoolName;
            $eduBack[0]['year'] = $year;
        }else{
            $edu_back = json_decode($userinfo['edu_back'],true);
            $count = count($edu_back);

            $eduBack[$count]['education'] = $education;
            $eduBack[$count]['schoolName'] = $schoolName;
            $eduBack[$count]['year'] = $year;
        }

        $data = array();
        $data['edu_back'] = json_encode($eduBack);
        $data['utime'] = time();

        $userinfo = Userinfo::get(['uid'=>$uid]);
        if(empty($userinfo)){
            $data['uid'] = $uid;
            $data['ctime'] = time();
            Userinfo::create($data);
        }else{
            Userinfo::where('uid', $uid)->update($data); 
        }

        $userinfo = Userinfo::get(['uid'=>$uid]);
        $eduBack = json_decode($userinfo['edu_back'],true);
        foreach($eduBack as $key=>$data){
            $eduBack[$key]['educationStr'] = read_conf('edu_lavel')[$data['education']];
        }
        
        return json(['flog'=>1, 'msg'=>'','data'=>$eduBack]);

    }

    public function delEduBack(){

        $uid = $this->uid;
        $user = User::get(['id'=>$uid]);
        if(empty($user)||$user['is_del']==1){
            return json(['flog'=>0, 'msg'=>'用户出错!']);
        }

        $post = input('post.');
        $key = $post['key'];


        $userinfo = Userinfo::get(['uid'=>$uid]);

        # 重组教育背景数组
        $eduBack = json_decode($userinfo['edu_back'],true);
        unset($eduBack[$key]);
        $eduBack = array_values($eduBack);

        $data = array(
                    'edu_back'=>json_encode($eduBack),
                    'utime'=>time(),
                );
        Userinfo::where('uid', $uid)->update($data); 

        return json(['flog'=>1, 'msg'=>'删除成功']);
    }


    # 工作信息
    public function editWordInfo(){
        $uid = $this->uid;
        
        $userinfo = Userinfo::get(['uid'=>$uid]);
        $workInfo = json_decode($userinfo['work_info'],true);
       
        $data = array(
                'userinfo'=>$userinfo,
                'workInfo'=>$workInfo,
            );
        return $this->view->fetch('editWordInfo',$data); 
    }

    public function saveWordInfo(){
        
        $uid = $this->uid;
        $user = User::get(['id'=>$uid]);
        if(empty($user)||$user['is_del']==1){
            return json(['flog'=>0, 'msg'=>'用户出错!']);
        }

        $post = input('post.');
        
        $wordJob = $post['wordJob'];
        $year_start = $post['year_start'];
        $month_start = $post['month_start'];
        $year_end = $post['year_end'];
        $month_end = $post['month_end'];
            
        $timeSpan = $year_start.'-'.$month_start.'~'.$year_end.'-'.$month_end;
        if($year_end=='至今'){
            $timeSpan = $year_start.'-'.$month_start.'~'.$year_end;
        }


        $wordInfo = array();
        $userinfo = Userinfo::get(['uid'=>$uid]);
        if(empty($userinfo['work_info'])){
            $wordInfo[0]['wordJob'] = $wordJob;
            $wordInfo[0]['timeSpan'] = $timeSpan;
        }else{
            $work_info = json_decode($userinfo['work_info'],true);
            $count = count($work_info);

            $wordInfo[$count]['wordJob'] = $wordJob;
            $wordInfo[$count]['timeSpan'] = $timeSpan;
        }

        $data = array();
        $data['work_info'] = json_encode($wordInfo);
        $data['utime'] = time();

        $userinfo = Userinfo::get(['uid'=>$uid]);
        if(empty($userinfo)){
            $data['uid'] = $uid;
            $data['ctime'] = time();
            Userinfo::create($data);
        }else{
            Userinfo::where('uid', $uid)->update($data); 
        }
        
        return json(['flog'=>1, 'msg'=>'','data'=>$wordInfo]);
    }

    public function delWorkInfo(){

        $uid = $this->uid;
        $user = User::get(['id'=>$uid]);
        if(empty($user)||$user['is_del']==1){
            return json(['flog'=>0, 'msg'=>'用户出错!']);
        }

        $post = input('post.');
        $key = $post['key'];


        $userinfo = Userinfo::get(['uid'=>$uid]);

        # 重组工作信息数组
        $workInfo = json_decode($userinfo['work_info'],true);
        unset($workInfo[$key]);
        $workInfo = array_values($workInfo);

        $data = array(
                    'work_info'=>json_encode($workInfo),
                    'utime'=>time(),
                );
        Userinfo::where('uid', $uid)->update($data); 

        return json(['flog'=>1, 'msg'=>'删除成功']);
    }

}