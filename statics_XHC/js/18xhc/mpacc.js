// zhibo_tab
$(function(){	
	$('.zb').mouseover(function(){
		$(this).addClass('zb_1').siblings().removeClass('zb_1');
		$('.panes>div:eq('+$(this).index()+')').show().siblings().hide();	
	})
})

// ms_shuxue
$(function(){
	$li1 = $("#demo1 a");
	$window1 = $("#demo1");
	$left1 = $("#indemo .ms_l");
	$right1 = $("#indemo .ms_r");	
	$window1.css("width", $li1.length*202);
	var lc1 = 0;
	var rc1 = $li1.length-5;
	$left1.click(function(){
		if (lc1 < 1) {
			return;
		}
		lc1--;
		rc1++;
		$window1.animate({left:'+=202px'}, 1000);
	});
	$right1.click(function(){
		if (rc1 < 1){
			return;
		}
		lc1++;
		rc1--;
		$window1.animate({left:'-=202px'}, 1000);
	});
})
// ms_yingyu
$(function(){
	$li2 = $("#demo1_m a");
	$window2 = $("#demo1_m");
	$left2 = $("#indemo_m .ms_l_m");
	$right2 = $("#indemo_m .ms_r_m");	
	$window2.css("width", $li2.length*202);
	var lc2 = 0;
	var rc2 = $li2.length-5;
	$left2.click(function(){
		if (lc2 < 1) {
			return;
		}
		lc2--;
		rc2++;
		$window2.animate({left:'+=202px'}, 1000);
	});
	$right2.click(function(){
		if (rc2 < 1){
			return;
		}
		lc2++;
		rc2--;
		$window2.animate({left:'-=202px'}, 1000);
	});
})
// ms_luoji
$(function(){
	$li3 = $("#demo1_n a");
	$window3 = $("#demo1_n");
	$left3 = $("#indemo_n .ms_l_n");
	$right3 = $("#indemo_n .ms_r_n");	
	$window3.css("width", $li3.length*202);
	var lc3 = 0;
	var rc3 = $li3.length-5;
	$left3.click(function(){
		if (lc3 < 1) {
			return;
		}
		lc3--;
		rc3++;
		$window3.animate({left:'+=202px'}, 1000);
	});
	$right3.click(function(){
		if (rc3 < 1){
			return;
		}
		lc3++;
		rc3--;
		$window3.animate({left:'-=202px'}, 1000);
	});
})
// ms_qiehuan
$(function(){	
	$('.ms_nav ul li').mouseover(function(){
		$(this).addClass('ms_li').siblings().removeClass('ms_li');
		$('.mingshi>div:eq('+$(this).index()+')').show().siblings().hide();	
	})
})

// jingcai_tab
$(function(){	
	$('.m9_nav a').mouseover(function(){
		$(this).addClass('sj').siblings().removeClass('sj');
		$('.shunjian>div:eq('+$(this).index()+')').show().siblings().hide();	
	})
})