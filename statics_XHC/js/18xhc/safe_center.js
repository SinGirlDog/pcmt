$(document).ready(function(){
	$(".scm_hd span,.log_reg_hd span").hover(function(){
		$(this).css('cursor','pointer');
	},function(){
	});
});

//登录注册-账号保护-密码管理-切换
$(document).ready(function(){
	$(".scm_hd span").click(function(){
		var position_idx = $(this).index();
		var position_real = position_idx*88 + 'px';
		if(!$(this).hasClass('scm_hd_on')){
			$(this).addClass('scm_hd_on').siblings().removeClass('scm_hd_on');
			$(".red_line").css('left',position_real);
			$(".red_line_0"+position_idx).removeClass('scm_hide').siblings().addClass('scm_hide');
			if(position_idx != 2){
				$('.pwd_manage').children('div').addClass('scm_hide');
				$('.pwd_m_step_0').removeClass('scm_hide');
			}
		}
	});
});

//登录-注册-切换
$(document).ready(function(){
	$(".log_reg_hd span").click(function(){
		var lg_rg_idx = $(this).index();
		if(!$(this).hasClass('scm_hd_on')){
			$(this).addClass('scm_hd_on').siblings().removeClass('scm_hd_on');
			$(".login_reg_0"+lg_rg_idx).removeClass('scm_hide').siblings().addClass('scm_hide');
		}
	});
});

//密码管理方式选择
$(document).ready(function(){
	$(".pwd_m_step_0 a").click(function(){
		var rec_idx = $(this).index();
		$(this).parent().addClass('scm_hide');
		if(rec_idx == 1){
			$('.pwd_m_recovery').removeClass('scm_hide');
		}
		else if(rec_idx == 0){
			$(".pwd_m_edit").removeClass('scm_hide');
		}
	});
});

//密码找回方式选择
$(document).ready(function(){
	$(".pwd_m_recovery a").click(function(){
		var rec_way_idx = $(this).index();
		if(rec_way_idx == 2){
			$(this).parent().addClass('scm_hide');
			$('.pwd_m_rec_byphone').removeClass('scm_hide');
		}
		else if(rec_way_idx == 1){
			$(this).parent().addClass('scm_hide');
			$(".pwd_m_rec_bymail").removeClass('scm_hide');
		}
		else{
			;
		}
	});
});

