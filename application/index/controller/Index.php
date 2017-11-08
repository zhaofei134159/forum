<?php
namespace app\index\controller;

use think\Session;
use think\Log;
use app\index\model\Common as modelCommon;
use think\Loader;

class Index extends Common
{
	public function __construct(){
		parent::__construct();
	}


    public function index(){
        $data = array();
        return $this->view->fetch('index',$data);
    }
}
