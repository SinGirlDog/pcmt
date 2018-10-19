
$(document).ready(function(){
	$(".m2_left_l,.m2_left_r").hover(function(){
		if($(this).hasClass('m2_left_hide')){
		  $(this).toggleClass('m2_left_hide').siblings().toggleClass('m2_left_hide');
		  $(".list_m2").toggleClass('list_m2_hide');
		}
	},function(){
	});
});

//all_hover_be_here
$(document).ready(function(){
	$("#zx_m_ul,#zl_m_ul,#st_m_ul,#mk_m_ul,#sz_m_ul").children(".menu_li").hover(function(){
		var position_idx = $(this).index();
		var box_num = position_idx+1;
		var mark=$(this).parent().attr('id').split('_')[0];
		var position_real = 0;
		
		if(mark==='sz'){
			position_real = 188 + 82*position_idx;
		}
		else{
			position_real = 188 + 93*position_idx;
			
		}
		$("#"+mark+"_mv_mk").css("left",position_real+"px");
		
		if($('.'+mark+'_box_0'+box_num).hasClass(mark+'_box_hide')){
			$('.'+mark+'_box_0'+box_num).toggleClass(mark+'_box_hide').siblings().addClass(mark+'_box_hide');
		}

	},function(){
	});
});	

$(document).ready(function(){
	$('.mkbox_r button').hover(function(){
		$(this).css("cursor","pointer");
		$(this).css("background","#A22");
		$(this).css("color","#FFF");
	},function(){
		$(this).css("background","#FFF");
		$(this).css("color","#000");
	});
});

$(document).ready(function(){
	$('.right').hover(function(){
		$(this).children('span').css("background","#aa2222");
	},function(){
		$(this).children('span').css("background","#666666");
	});
});

$(document).ready(function(){
	$('.zbgg ul li').hover(function(){
		$(this).children('div').css("background","#aa2222");
	},function(){
		$(this).children('div').css("background","#666666");
	});
});


