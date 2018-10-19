<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>exam_file_show_html</title>
	<script type="text/javascript" src="{APP_PATH}statics_EXAM/js/jquery1.7.1.js"></script>
	<script type="text/javascript" src="{APP_PATH}statics_EXAM/js/answer_validate.js"></script>
</head>
<body>
	<?php
        $xml = new xml();
        $xml->dir = $file_data['thumb'];
        $xml->init();
		// echo '<pre/>';
		// var_export($file_data['thumb']);
		// var_export($xml);
	?>
</body>
</html>
