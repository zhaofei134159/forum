<?php
namespace app\index\controller;

use think\Session;
use think\Log;
use think\Loader;

use app\index\model\Common as modelCommon;
use app\index\model\Cate;
use app\index\model\Plate;
use app\index\model\Cart;

class Index extends Common
{
	public function __construct(){
		parent::__construct();
	}


    public function index(){


        $data = array(
        	);
        return $this->view->fetch('index',$data);
    }
}
