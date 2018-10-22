<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>exam_message_show_html</title>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/jquery1.7.1.js"></script>
	<!-- 首尾样式 -->
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/index.css">
</head>
<body>
	<?php include template("../xhc/content","header_NEW"); ?>
	<div id="show_form">
		<input id="msg" type="hidden" value="<?php echo $msg; ?>">
		<input id="url" type="hidden" value="<?php echo $url; ?>">
	</div>
	<?php include template("../xhc/content","footer_NEW"); ?>
</body>
<script type="text/javascript">
	$(document).ready(function(){
		var msg = $('#msg').val();
		var url = $('#url').val();
		var r = confirm(msg);
		if (r==true){
			window.location.replace(url);
		}
		else{
			window.location.replace(url);
		}
	});
</script>
</html>
