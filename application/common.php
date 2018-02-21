<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//curl
function httpRequest($url, $post_data='', $header=0, $timeout=15)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, $header);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	//post
	if($post_data)
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	}
	$output = curl_exec($ch);
	curl_close($ch);

	return $output;
}


//读取配置
function read_conf($name = '',$config_path = 'config.php')
{
	if(empty($name)){
		return array();
	}

	$a = think\Config::load($config_path);
	return think\Config::get($name);
}

# 地址读配置
function url_read_conf($name,$config_path = 'urlConfig.php',$actionField='index')
{
	if(empty($name)){
		return array();
	}

	$urlConfig = think\Config::load(APP_PATH.'/index/'.$config_path,'',$actionField);
	return $urlConfig[$name];
}


//过滤参数
function clearhtml($str)
{
	return addslashes(htmlspecialchars(trim($str)));
}

function sprint_r($arr)
{
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

//文本处理之中文汉字字符串转换为数组
function ch2arr($str)
{
    $length = mb_strlen($str, 'utf-8');
    $array = array();
    for ($i=0; $i<$length; $i++)  
        $array[] = mb_substr($str, $i, 1, 'utf-8');    
    return $array;
}


function str_em($str,$title)
{
    $length = mb_strlen($str, 'utf-8');
    $array = array();
    for ($i=0; $i<$length; $i++)  
        $array[] = mb_substr($str, $i, 1, 'utf-8');   
    $new = array();     
    foreach($array as $v)
	{	
		$new[$v] = '<b>'.$v.'</b>';
		
	}
	$title = strtr($title, $new);
	return $title;
}