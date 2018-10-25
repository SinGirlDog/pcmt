$(document).ready(function(){
	$('#put_answer_ajax_button').click(function(){

		var only_num = 0;
		$('.choice_only').each(function(){
			var input_object = $(this).children('dl').children('label').children('input');
			var check_flag = 0;
			input_object.each(function(){
				if($(this).attr("checked")){
					check_flag = 1;
				}
			});
			if(check_flag != 1){
				only_num = 0;
				var num = input_object.attr('id').split('_')[1];
				alert("请选择：第"+num+"题");
				input_object.focus();
				return false ;
			}
			else{
				only_num = 1;
			}
		});

		if(only_num == 1){
			var more_num = 0;
			$('.choice_more').each(function(){
				var input_object = $(this).children('dl').children('label').children('input');
				var check_flag = 0;
				input_object.each(function(){
					if($(this).attr("checked")){
						check_flag = 1;
					}
				});
				if(check_flag != 1){
					more_num = 0;
					var num = input_object.attr('id').split('_')[1];
					alert("请选择：第"+num+"题");
					input_object.focus();
					return false ;
				}
				else{
					more_num = 1;
				}
			});
		}
		else{
			return false;
		}
		if(more_num == 0){
			return false;
		}
		$('#put_answer_form').submit();

	});
});

$(document).ready(function(){

	function checkMobile() {
		var pda_user_agent_list = new Array("2.0 MMP", "240320", "AvantGo", "BlackBerry", "Blazer",
			"Cellphone", "Danger", "DoCoMo", "Elaine/3.0", "EudoraWeb", "hiptop", "IEMobile", "KYOCERA/WX310K", "LG/U990",
			"MIDP-2.0", "MMEF20", "MOT-V", "NetFront", "Newt", "Nintendo Wii", "Nitro", "Nokia",
			"Opera Mini", "Opera Mobi",
			"Palm", "Playstation Portable", "portalmmm", "Proxinet", "ProxiNet",
			"SHARP-TQ-GX10", "Small", "SonyEricsson", "Symbian OS", "SymbianOS", "TS21i-10", "UP.Browser", "UP.Link",
			"Windows CE", "WinWAP", "Androi", "iPhone", "iPod", "iPad", "Windows Phone", "HTC");
		var pda_app_name_list = new Array("Microsoft Pocket Internet Explorer");

		var user_agent = navigator.userAgent.toString();
		for (var i = 0; i < pda_user_agent_list.length; i++) {
			if (user_agent.indexOf(pda_user_agent_list[i]) >= 0) {
				return true;
			}
		}
		var appName = navigator.appName.toString();
		for (var i = 0; i < pda_app_name_list.length; i++) {
			if (user_agent.indexOf(pda_app_name_list[i]) >= 0) {
				return true;
			}
		}

	    //针对iPhone微信扫地址栏生成的二维码访问之处理
	    var ua = navigator.userAgent.toLowerCase();
	    if(ua.match(/MicroMessenger/i) == "micromessenger") {
	    	return true;
	    }
	    if(ua.match(/android/i) == "android"){  
	    	return true;
	    }  

	    return false;
	}
	if (checkMobile()){
		gomobile();
	}
	else {
	}
	function gomobile(){
		$("label").css('font-size','24px');
	}

});