$(document).ready(function() {
	$('.zxmk_ul li div').hover(function() {
		var filter = '<div class="filter_div">详细内容</div>';
		$(this).append(filter);
	}, function() {
		$('.filter_div').remove();
	});
});