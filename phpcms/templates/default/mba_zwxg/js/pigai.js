$(function(){	
	$('.ssg h6').click(function(){
		$(this).addClass('hot').siblings().removeClass('hot');
		$('.jg_con>div:eq('+$(this).index()+')').show().siblings().hide();	
	})
})