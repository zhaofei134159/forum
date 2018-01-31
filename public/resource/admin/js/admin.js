// 弹框
function alertFun(body){
	var ButtonId = $('#ButtonId');
	var alertId = $('#alertId');

	alertId.html(body);
	ButtonId.css('display','');
}

// 加载
function loading(str){
	console.log(str);
	if(str=='show'){
		console.log(1);
		console.log($('#loadImg'));
		$('#loadImg').show();
	}else if(str=='hide'){
		console.log(2);
		$('#loadImg').hide();
	}
}

// 返回
function back(){
	loading('show');
	window.history.back();
}