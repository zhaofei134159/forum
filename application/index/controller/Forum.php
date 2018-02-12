<?php
namespace app\index\controller;

use app\index\model\User;

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

    # 申请版主
    public function applyModerator(){

    }

}