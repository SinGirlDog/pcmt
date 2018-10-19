$(document).ready(function(){
	$(".shaixuan ul li a").click(function(){
		$(this).addClass('shaixuan_on').siblings().removeClass('shaixuan_on');
	});
});

$(document).ready(function(){
	$(".gouwuche,.goumai").hover(function(){
		$(this).css('cursor','pointer');
		$(this).css('background','#1174b0');
		$(this).css('color','#fff');
	},function(){
		$(this).css('background','#fff');
		$(this).css('color','#000');
	});
});