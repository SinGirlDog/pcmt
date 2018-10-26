$(document).ready(function(){
	$('#put_cat_form ul li').click(function(){
		var cat_mark = $(this).attr('id');
		if(cat_mark == 'rand_paper'){
			$('#sec_cat').val(cat_mark);
			$('#put_cat_form').attr('action','/index.php?m=exam&c=index&a=rand_paper_init');
		}
		else if(cat_mark == 'answer_history'){
			// $('#sec_cat').val(cat_mark);
			$('#put_cat_form').attr('action','/index.php?m=exam&c=index&a=answer_history_init');
		}
		else{
			$('#sec_cat').val(cat_mark);
		}
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

$(document).ready(function(){
	$('#third_rand_left ul li').click(function(){
		var randid = $(this).attr('id');
		$('#randid').val(randid);
		$('#put_rand_form').submit();
	});
});

$(document).ready(function(){
	$('.history_left ul li').click(function(){
		var thi_cat = $(this).attr('id');
		$('#thi_cat').val(thi_cat);
		$('#put_cat_form').attr('action','/index.php?m=exam&c=index&a=answer_history_init');
		$('#put_cat_form').submit();
	});
});

$(document).ready(function(){
	$('#third_answer_list ul li a').click(function(){
		var answer_id = $(this).parent().attr('id');
		$('#answer_id').val(answer_id);
		// console.log(answer_id);
		var action = $('#put_answerid_form').attr('action');
		$('#put_answerid_form').attr('action',action+'&answer_id='+answer_id);
		$('#put_answerid_form').submit();
	});
});