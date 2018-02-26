<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Cate;
use app\index\model\Plate;

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

        return $this->view->fetch('index');
	}
}