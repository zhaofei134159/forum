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



# 用户性格
	$conf['user_character']['1'] = '温柔';
	$conf['user_character']['2'] = '粗犷';
	$conf['user_character']['3'] = '活泼';
	$conf['user_character']['4'] = '老成';
	$conf['user_character']['5'] = '内向';
	$conf['user_character']['6'] = '开朗';
	$conf['user_character']['7'] = '豪爽';
	$conf['user_character']['8'] = '沉默';
	$conf['user_character']['9'] = '急躁';
	$conf['user_character']['10'] = '沉稳';


# 教育程度
	$conf['edu_lavel']['no'] = '未知';
	$conf['edu_lavel']['1'] = '初中';
	$conf['edu_lavel']['2'] = '高中';
	$conf['edu_lavel']['3'] = '大学';
	$conf['edu_lavel']['4'] = '硕士';
	$conf['edu_lavel']['5'] = '小学';
	$conf['edu_lavel']['6'] = '中专/技校';
	$conf['edu_lavel']['7'] = '大专';
	$conf['edu_lavel']['8'] = '博士';
	$conf['edu_lavel']['9'] = '其他';


# 当前职业
	$conf['occupation']['no'] = '未知';
	$conf['occupation']['1'] = '广告/营销/公关';
	$conf['occupation']['2'] = '航天';
	$conf['occupation']['3'] = '农业/化工/林业产品';
	$conf['occupation']['4'] = '汽车';
	$conf['occupation']['5'] = '计算机/电子产品';
	$conf['occupation']['6'] = '建筑';
	$conf['occupation']['7'] = '教育(包括学生)';
	$conf['occupation']['8'] = '能源/采矿';
	$conf['occupation']['9'] = '金融/保险/房地产';
	$conf['occupation']['10'] = '政府/军事/公共服务';
	$conf['occupation']['11'] = '招待';
	$conf['occupation']['12'] = '传媒/出版/娱乐';
	$conf['occupation']['13'] = '医疗/保健服务';
	$conf['occupation']['14'] = '制药';
	$conf['occupation']['15'] = '零售';
	$conf['occupation']['16'] = '服务';
	$conf['occupation']['17'] = '电信/网络';
	$conf['occupation']['18'] = '旅游/交通';
	$conf['occupation']['19'] = '其他';


# 密保问题
	$conf['security']['1'] = '您目前的姓名是？';
	$conf['security']['2'] = '您配偶的生日是？';
	$conf['security']['3'] = '您的学号（或工号）是？';
	$conf['security']['4'] = '您高中班主任的名字是？';
	$conf['security']['5'] = '您父亲的姓名是？';
	$conf['security']['6'] = '您小学班主任的名字是？';
	$conf['security']['7'] = '您父亲的生日是？';
	$conf['security']['8'] = '您配偶的姓名是？';
	$conf['security']['9'] = '您初中班主任的名字是？';
	$conf['security']['10'] = '您最熟悉的童年好友名字是？';
	$conf['security']['11'] = '您最熟悉的学校宿舍舍友名字是？';
	$conf['security']['12'] = '对您影响最大的人名字是？';
	$conf['security']['13'] = '您母亲的生日是？';

return $conf;