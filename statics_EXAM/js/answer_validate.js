$(document).ready(function(){
	$('#put_answer_ajax_button').click(function(){

		// console.log($('input:radio:checked'));
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
		
		// var url = 'index.php?m=exam&c=index&a=put_answer';
		// var data = $('#put_answer_form').serializeArray();
		// $.ajax({
		// 	type:'POST',
		// 	url:url,
		// 	data:data,
		// 	datatype:'json',
		// 	success:function(Result){
		// 		$.each($.parseJSON(Result), function(idx, obj) {
		// 			if(idx == 'msg'){
		// 				if(obj == 'answer_saved'){
		// 					alert('答案已提交！');
		// 					$(window).attr('location','/index.php?m=exam&c=index');
		// 				}
		// 				else{
		// 					alert(obj);
		// 				}
		// 			}
		// 			else{
		// 				alert("非预期状态！");
		// 			}
		// 		});
		// 	}
		// });

	});
});