$(document).ready(function(){
	//页头右上角微信二维码显示
	$(".wx_gf,.wx_box").hover(function(){
    	$(".wx_box").show();
    },function(){
    	$(".wx_box").hide();
    });
	
	//页头右上角微博显示
    $(".sina_wb,.sina_box").hover(function(){
    	$(".sina_box").show();
    },function(){
    	$(".sina_box").hide();
    });

    $(".wzdt,.dropMenu").hover(function(){
    	$(".dropMenu").show();
    },function(){
    	$(".dropMenu").hide();
    });


});