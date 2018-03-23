<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Cate;
use app\index\model\Plate;
use app\index\model\Notice as modelNotice;

use think\Session;
use think\Config;
use smtp;


class Notice extends Common
{	

	public function __construct(){
		parent::__construct();
		
	}

    
    public function list(){
    	$where = array();
        $notices = modelNotice::where($where)->select();

        $data = array(
        		'notices'=>$notices
        	);
        return $this->view->fetch('list',$data);
    }
}