<?php
namespace app\index\controller;

use think\Session;
use think\Log;
use app\index\model\Common as modelCommon;
use think\Loader;

class Login extends Common
{
	public function __construct(){
		parent::__construct();
		
	}


    public function index()
    {
        return 'login';
    }
}
