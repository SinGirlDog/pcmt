$(document).ready(function(){
	$('.select_ajax').change(function(){
		var param_id = $(this).val();
		var num = $(this).attr('id').split('_')[1];
		var num_next = Number(num)+1;
		var	url = '/index.php?m=exam&c=index&a=ajax_select_qanda&siteid=1&param_id='+param_id;

		$.ajax({
			url:url,
			datatype:'html',
			success:function(ResultHtml,textStatus){
				if(textStatus == 'success'){
					$('#select_'+num_next).html(ResultHtml);
				}
			}
		});
	});
});