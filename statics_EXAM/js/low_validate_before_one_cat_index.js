$(document).ready(function(){
	$('#get_info_ajax_button').click(function(){

		var name = $('#name').val();
		var mobile = $('#mobile').val();
		var select_1 = $('#select_1').val();
		var select_2 = $('#select_2').val();
		
		if(!select_1){
			alert('请做出选择');
			$("#select_1").focus();
			return false;
		}
		if(!select_2){
			alert('请做出选择');
			$("#select_2").focus();
			return false;
		}
		if(!name){
			alert('姓名不可为空');
			$("#name").focus();
			return false;
		}
		if(!mobile){
			alert('手机号码不可为空');
			$("#mobile").focus();
			return false;
		}
		else
		{
			var preg_mobile = /^1[3|4|5|7|8][0-9]\d{8}$/;
			var res_preg = preg_mobile.test(mobile);
			if(!res_preg)
			{
				alert('手机号格式错误');
				$("#mobile").focus();
				return false;
			}
		}

		$('#get_info_form').submit();

	});
});