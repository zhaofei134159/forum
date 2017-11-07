<?php

//网站名称
$conf['website']['web_name'] = 'fangforum';
$conf['website']['web_top_name'] = '<span>fang<span>forum</span></span>';

//邮箱回调
$conf['website']['email_callback'] = 'forum.myfeiyou.com';



//包含 qq  key
$conf['login']['qq_appkey'] = '101390259';
$conf['login']['qq_appsecret'] = 'c166d5355d23ccd0f30e55529ae411a0';
$conf['login']['qq_login'] = 'http://forum.myfeiyou.com/index/login/qq_web';



//smtp的配置
$conf['smtp']['smtpserver'] = 'smtp.163.com';//SMTP服务器
$conf['smtp']['smtpserverport'] = 25;//SMTP服务器端口
$conf['smtp']['smtpusermail'] = "blogfamily@163.com";//SMTP服务器的用户邮箱
$conf['smtp']['smtpuser'] = "blogfamily@163.com";//SMTP服务器的用户帐号
$conf['smtp']['smtppass'] = "zf134159";//SMTP服务器的用户密码
$conf['smtp']['mailtype'] = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件


// 控制器 方法
$conf['controller']['Login'] = '登录 or 注册';
$conf['action']['order'] = '12312';

return $conf;