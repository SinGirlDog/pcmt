$(document).ready(function(){
    //响应文件添加成功事件
    var type_arr = ["jpeg","png","gif","jpg","tiff"];
    var feedback = $("#feedback");
    $("#inputfile").change(function(){
    	if (feedback.children('img').length>0) {
    		alert("最多只能选择1张图片");
    		return false;
    	}
        //创建FormData对象
        var data = new FormData();
        //为FormData对象添加数据
        $.each($('#inputfile')[0].files, function(i, file) {
        	data.append('upload_file'+i, file);
        });
        // $(".loading").show();    //显示加载图片
        //发送数据

        $.ajax({
        	url:'/index.php?m=mba_zwxg&c=upload_file&a=upload_file', /*去过那个php文件*/
        	type:'POST',  /*提交方式*/
        	data:data,
        	cache: false,
        	contentType: false,        /*不可缺*/
        	processData: false,         /*不可缺*/
        	success:function(data){
        		 // console.log(data);
        		 var sp_data = data.split('!@#$');
        		 var msg = sp_data[0];
        		 var file_path = sp_data[1];

        		 if(msg == 'upload_well'){
        		 	$("#feedback").val(file_path);
        		 	alert(1);
        		 }
        		 else if(msg == 'undefined_file_type'){
        		 	alert('文件格式错误！');
        		 }
        		 else if(msg == 'more_than_one_M'){
        		 	alert('文件大小已超过1M！');
        		 }
        		 else{
        		 	// alert('非预期错误！');
        		 	if(msg == 'upload_well'){
        		 		$("#feedback").val(file_path);
        		 	}
        		 	else if(msg == 'undefined_file_type'){
        		 		alert('文件格式错误！');
        		 	}
        		 	else if(msg == 'more_than_one_M'){
        		 		alert('文件大小已超过1M！');
        		 	}
        		 	if(file_path){
        		 		var first_name = file_path.split('.')[0];
        		 		var last_name = file_path.split('.')[1];
        		 		var index = $.inArray(last_name,type_arr);
        		 		if(index >= 0){
        		 			$("#feedback").val(file_path);
        		 		}
        		 		else
        		 		{
        		 			if(first_name == 'errorundefined_file_type'){
        		 				alert('文件格式错误！');
        		 			}
        		 			else if(first_name == 'errormore_than_one_M'){
        		 				alert('文件大小已超过1M！');
        		 			}
        		 		}
        		 	}
        		 }
        		},
        		error:function(){
        			alert('上传出错!');
        		}
        	});
    });
    
});
