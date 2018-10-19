<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>exam_info_welcome_html</title>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/jquery1.7.1.js"></script>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/welcome.js"></script>
	<!-- 首尾样式 -->
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/index.css">
	<link rel="stylesheet" type="text/css" href="<?php echo APP_PATH;?>statics_EXAM/css/welcome.css">

</head>
<body>
	<?php include template("../xhc/content","header_NEW"); ?>
	<div id="welcome">
		<form method="post" name="put_cat_form" id="put_cat_form" action="index.php?m=exam&c=index&a=choose_sec_cat">
			<?php include template("../xhc/content","position_exam"); ?>
			<ul>
				<?php
				foreach($list_sec as $item)
				{
					?>
					<li id="<?php echo $item['catid'] ?>"><a><?php echo $item['catname'] ?></a></li>
					<?php
				}
				?>
				<!-- <li>
					<a>自动组卷</a>
				</li> -->
			</ul>
			<input type="hidden" name="sec_cat" id="sec_cat" value="">
		</form>
	</div>

	<?php include template("../xhc/content","footer_NEW"); ?>

</body>
</html>
