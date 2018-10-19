function show(name,mobile){
		
		// var mobile = $("#mobile").val();
		// var name = name;
		var preg_mobile = /^1[3|4|5|7|8][0-9]\d{8}$/;
		var res_preg = preg_mobile.test(mobile);
		if(!res_preg)
		{
			alert('手机号格式错误');
			$("#mobile").focus();
		}
		else{
			$("#pay_phone").val(mobile);
			$("#pay_name").val(name);
			var param = name+"."+mobile;
			$("#quest_img").attr('src',quest_url+param);
			// $("#quest_img").attr('src',quest_url+name+'&mobile'+mobile);

		    $(".lbOverlay").css({"height":window.screen.availHeight});
		    $(".lbOverlay").show();
		 
		    var st=$(document).scrollTop(); //页面滑动高度
		    var objH=$(".hidden_pro_au").height();//浮动对象的高度
		    var ch=$(window).height();//屏幕的高度  
		    var objT=Number(st)+(Number(ch)-Number(objH))/2;   //思路  浮动高度+（（屏幕高度-对象高度））/2
		    $(".hidden_pro_au").css("top",objT);
		     
		    var sl=$(document).scrollLeft(); //页面滑动左移宽度
		    var objW=$(".hidden_pro_au").width();//浮动对象的宽度
		    var cw=$(window).width();//屏幕的宽度  
		    var objL=Number(sl)+(Number(cw)-Number(objW))/2; //思路  左移浮动宽度+（（屏幕宽度-对象宽度））/2
		    $(".hidden_pro_au").css("left",objL);
		    $(".hidden_pro_au").slideDown("20000");//这里显示方式多种效果
		}
		
	}
	function closeDiv(){
	    $(".lbOverlay").hide();
	    $(".hidden_pro_au").hide();
	}


function show1(){
	    $(".cxjg").css({"height":window.screen.availHeight});
	    $(".cxjg").show();
	 
	    var st=$(document).scrollTop(); //页面滑动高度
	    var objH=$(".cxjg_1").height();//浮动对象的高度
	    var ch=$(window).height();//屏幕的高度  
	    var objT=Number(st)+(Number(ch)-Number(objH))/2;   //思路  浮动高度+（（屏幕高度-对象高度））/2
	    $(".cxjg_1").css("top",objT);
	     
	    var sl=$(document).scrollLeft(); //页面滑动左移宽度
	    var objW=$(".cxjg_1").width();//浮动对象的宽度
	    var cw=$(window).width();//屏幕的宽度  
	    var objL=Number(sl)+(Number(cw)-Number(objW))/2; //思路  左移浮动宽度+（（屏幕宽度-对象宽度））/2
	    $(".cxjg_1").css("left",objL);
	    $(".cxjg_1").slideDown("20000");//这里显示方式多种效果
	}
	function closeDiv1(){
	    $(".cxjg").hide();
	    $(".cxjg_1").hide();
	}