$(document).ready(function(){
	$('#put_cat_form ul li').click(function(){
		var cat_mark = $(this).attr('id');
		$('#sec_cat').val(cat_mark);
		$('#thi_cat').val('');
		$('#put_cat_form').submit();
	});
});


$(document).ready(function(){
	$('#third_left ul li').click(function(){
		var thi_cat = $(this).attr('id');
		$('#thi_cat').val(thi_cat);
		$('#put_cat_form').submit();
	});
});

$(document).ready(function(){
	$('#third_file_list ul li a').click(function(){
		var fileid = $(this).parent().attr('id');
		$('#fileid').val(fileid);
		$('#put_fileid_form').submit();
	});
});
