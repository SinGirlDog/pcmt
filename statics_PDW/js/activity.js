$(function(){
	var id = "";
	$("#loginBox .closeBtn").click(function(){
		$("#loginBox").removeClass("animated bounceInRight").show().addClass("animated bounceOutLeft")
		setTimeout(function(){
			$("#shadow").hide();
		},500)
	});
	$(".join").click(function(){
		// id = $(this).attr("activityId");
		// if(id){
			$("#shadow").show();
			$("#activeJoinBox").removeClass("animated bounceOutLeft").show().addClass("animated bounceInRight")
		// }
	})
	$("#activeJoinBox .closeBtn").click(function(){
		$("#activeJoinBox").removeClass("animated bounceInRight").show().addClass("animated bounceOutLeft")
		setTimeout(function(){
			$("#shadow").hide();
		},500)
	});
	$("#activeJoinReturnBox .closeBtn").click(function(){
		$("#activeJoinReturnBox").removeClass("animated bounceInRight").show().addClass("animated bounceOutLeft")
		setTimeout(function(){
			$("#shadow").hide();
		},500)
	});
	var len = $(".actBanner").length;
	if(len==1){
		var swiper = new Swiper('.actBanner .swiper-container', {
			pagination: '.swiper-pagination',
			paginationClickable: true,
			spaceBetween: 30,
		});
	}


	var str;
	var url = "/activity/list";
	$("#activeList .type a").click(function(){
		var att = $(this).attr("param");
		var type = $(".types .on").attr("param") ;
		var time = $(".time .on").attr("param") ;
		var city = $(".city .on").attr("param") ; 
		var index = $(".type").index($(this).parents(".type")) ;
		console.log(index)
		if(index==0){
			str = "?type="+att+"&time="+time+"&area="+city; 
		}else if(index==1){
			str = "?type="+type+"&time="+att+"&area="+city; 
		}else{
			str = "?type="+type+"&time="+time+"&area="+att; 
		}
		window.location.href = url+str;  
	});

	$(".refresh").click(function(){
		var number = parseInt(Math.random()*1000,10)+1;
		$("#codeSrc").attr("src",'/api/captcha/activity_sign_up/'+number);
		$("#activeJoinBox").removeClass("animated bounceOutLeft").show().addClass("animated bounceInRight")
	});

	var goIng = false;
	var timer = null;
	var telReg = /^1[3|4|5|6|7|8][0-9]{9}$/;
	$("#getCode").click(function() {
		var tel = $("#tel").val();

		if(!tel) {
			$("#activeJoinBox .error").eq(0).text("璇疯緭鍏ユ墜鏈哄彿").slideDown();
		} else {
			if(!telReg.test(tel)) {
               // $("#codeError").text("鎵嬫満鍙疯緭鍏ユ牸寮忔湁璇�").slideDown();
               $("#activeJoinBox .error").eq(0).text("鎵嬫満鍙疯緭鍏ユ牸寮忔湁璇�").slideDown();
               return false;
           }
       }


       if(!goIng) {
       	var _this = this;
       	if ($(this).hasClass('canclick')) {
       		var verify =  $("#verify").val();
       		if(verify){
       			$.ajax({
       				type: "post",
       				url: "/api/sysvalidate",
       				data: {verify:verify},
       				dataType: "json",
       				success: function(data){
       					if(typeof(data.data) != 'undefined' && data.data=='success'){
       						$('.slidemask').show();
       						$('.slideimg').pointsVerify({
									defaultNum : 4,	//榛樿鐨勬枃瀛楁暟閲�
									checkNum : 2,	//鏍″鐨勬枃瀛楁暟閲�
									vSpace : 5,	//闂撮殧
									imgName : ['../../resource/img/list.png', '../../resource/img/school11.png'],
									success : function() {
										var mobile   =  $("#tel").val();
										if(mobile){
											$.ajax({
												type: "post",
												url: "/api/sendsms",
												data: {mobile:mobile,verify:verify},
												dataType: "json",
												success: function(data){
													if(typeof(data.data) != 'undefined' && data.data=='success'){
														$('.slidemask').hide();
														$('.slideimg').html('');
														$(_this).addClass('code').removeClass('canclick');
														var iNum = 30;
														$(_this).html(iNum+'s鍚庨噸鏂拌幏鍙�');
														clearInterval(timer);
														timer = setInterval(function(){
															iNum--;
															$(_this).html(iNum+'s鍚庨噸鏂拌幏鍙�');
															if (iNum == 0 ) {
																clearInterval(timer);
																$(_this).addClass('canclick').removeClass('code');
																$(_this).html('閲嶆柊鑾峰彇楠岃瘉鐮�');
															}
														},1000); 
													}
												}
											}); 
										}
									}
								});		
       					}
       				}
       			}); 		
       		}
       	}
       }
   });

	$("#signUp").click(function(){
		var name = $("#name").val();
		var phone = $("#tel").val();
		var verify = $("#verify").val();
		var wxnum = $("#wxnum").val();
		var english = $("#english").val();
		var zonghe = $("#zonghe").val();
		var total = $("#total").val();
        //var id = $("#activitiy_id").val();
        var telReg =/^1[3|4|5|6|7|8][0-9]{9}$/; 
        if(!phone){
        	$("#activeJoinBox .error").eq(0).text("请输入您的手机号").slideDown();
        	return false;
        }else{
        	if(!telReg.test(phone)){
        		$("#activeJoinBox .error").eq(0).text("请输入正确的手机号").slideDown();
        		return false;
        	}
        }
        if(!name){
        	$("#activeJoinBox .error").eq(1).text("请输入您的姓名").slideDown();
        	return false;
        }
        
        if(!wxnum){
        	$("#activeJoinBox .error").eq(2).text("请输入您的微信号").slideDown();
        	return false;
        }
        
        if(!verify){
        	$("#activeJoinBox .error:last").text("请填写校验码").slideDown();
        	return false;
        }
        var extend = "";
        //$('input:radio:checked').val();
        $('input:radio:checked').each(function(){
        	var val = $(this).val();
        	extend += ","+val;
        });
        $(".extend").each(function(){
        	var val = $(this).val();
        	extend += ","+val;
        });
        
        // var colleges = $("#colleges").val();
        
        $.ajax({
        	type: "post",
        	url: "/index.php?m=other&c=activity&a=tijiao",
        	data: {name:name, phone:phone,id:id,wxnum:wxnum,extend:extend,english:english,zonghe:zonghe,total:total},
        	dataType: "json",
        	success: function(result){
        		// console.log(result);
        		if(result.data=='no_name_no_phone'){
        			$("#activeJoinBox .error").eq(0).text("请填写完整").slideDown();
        		}else if(result.data=='already' || result.data=='success'){
                    //楠岃瘉鐮侀敊璇�
                    // $("#activeJoinBox .error:last").text("已经报名").slideDown();
                    
                    //鎶ュ悕鎴愬姛
                    $("#activeJoinBox").removeClass("animated bounceInRight").show().addClass("animated bounceOutLeft")
                    $("#activeJoinReturnBox .success").show();
                    $("#activeJoinReturnBox").removeClass("animated bounceOutLeft").show().addClass("animated bounceInRight")
                    setTimeout(function(){
                    	$("#activeJoinReturnBox").removeClass("animated bounceInRight").show().addClass("animated bounceOutLeft")
                    	setTimeout(function(){
                    		$("#shadow").hide();
                    		$("#activeJoinReturnBox .success").hide();
                    		// window.location.href="/user/home"
                    	},500)
                    },2500)
                }else{
                	$("#activeJoinBox").removeClass("animated bounceInRight").show().addClass("animated bounceOutLeft")
                	$("#activeJoinReturnBox .defaut").show();
                	$("#activeJoinReturnBox").removeClass("animated bounceOutLeft").show().addClass("animated bounceInRight")
                	setTimeout(function(){
                		$("#activeJoinReturnBox").removeClass("animated bounceInRight").show().addClass("animated bounceOutLeft")
                		setTimeout(function(){
                			$("#shadow").hide();
                			$("#activeJoinReturnBox .defaut").hide();

                		},500)
                	},2500);
                }

            },
            error:function(what){
            	console.log(what.responseText);
            }
        });
    });
	var userId    = $("#userId").val();
	var articleId = $("#activitiy_id").val();
	if(userId){
		$.ajax({
			type: "post",
			url: "/api/checkFavorite",
			data: {articleId:articleId,type:'activitiy'},
			dataType: "json",
			success: function(data){
				if(typeof(data.data!=='undefined') && data.data=='favorited'){
					$("#favoriteBox").text('宸叉敹钘�');
					$("#favoriteBox").addClass('on');
				}
			}
		});

		$.ajax({
			type: "post",
			url: "/api/checkActivitiySingup",
			data: {articleId:articleId},
			dataType: "json",
			success: function(data){
				if(typeof(data.data!=='undefined') && data.data=='singuped'){
					$("#signUpButton").text('宸叉姤鍚�');
					$("#signUpButton").removeClass('join');
					$("#signUpButton").addClass('end');
					$("#signUpButton").attr('activityId',"");
				}
			}
		});
	};

	$("#favoriteBox").click(function(){ 

		if(!userId){ 
			$("#shadow").show();
			$("#loginBox").removeClass("animated bounceOutLeft").show().addClass("animated bounceInRight");
			return false;
		}
		if( !$("#favoriteBox").hasClass("favoriteBox") ){ 
			$(this).parent().addClass("on");
			var _thisSpan=$(this).siblings("span");
			_thisSpan.fadeIn();
			var timer = setTimeout(function(){
				_thisSpan.fadeOut();
			},1000);
			$.ajax({
				type: "post",
				url: "/api/favorite",
				data: {articleId:articleId,type:'activitiy'},
				dataType: "json",
				success: function(data){
					if(typeof(data.data!=='undefined') && data.data=='success'){
						$("#favoriteBox").html('宸叉敹钘�');
						$("#favoriteBox").addClass('favoriteBox');
					}    
				}
			});
		}

        //}
    });

	$(function(){
		$("#activeJoinBox input").focus(function(){
			$(this).siblings(".error").slideUp()
		})
		var actClick = 0;
		$("#activeList .slideBtn").click(function(){
			$(this).toggleClass("on")
			$(this).parent(".city").toggleClass("on")
		})
		$("#activeList .moreBtn").click(function(){
			if(actClick==0){
				$("#activeList .items").height("auto")
				actClick=1;
			}else{
				$("#activeList .moreBtn").hide();
				$("#activeList .actPage").show();
			}

		})

	});
});
