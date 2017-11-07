// 弹框
function alertFun(lable,body,footer,footer_html){
	var modelLable=$('#custom-width-modalLabel');
	var modelBody=$('#custom-model-body');
	var modelFooter=$('#modelFooter');
	var modelSave=$('#modelSave');

	var alertBtn = $('#alert-btn');

	modelLable.html('注册错误');
	modelBody.html('注册邮箱不能为空!');
	if(!footer){
		modelFooter.css('display','none');
	}

	alertBtn.click();

}

