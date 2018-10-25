<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>sec_cat_paper_list</title>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/jquery1.7.1.js"></script>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/welcome.js"></script>
	<!-- <script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/low_validate.js"></script> -->
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
			<?php include template("../xhc/content","sec_cat_block"); ?>
			<input type="hidden" name="thi_cat" id="thi_cat" value="<?php echo $thi_cat; ?>">
		</form>
	</div>
	<div id="third_commen_list">
		<div id="third_rand_left">
			<form method="post" name="put_rand_form" id="put_rand_form" action="index.php?m=exam&c=index&a=rand_one_exam_paper">
				<input type="hidden" name="randid" id="randid" value="">
			</form>
			<ul>
				<?php
				foreach($rand_paper_cat as $rditem)
				{
					?> 
					<li id="<?php echo $rditem['id'] ?>">
						<a>
							<?php echo $rditem['title'] ?>
						</a>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
	</div>
	<div style="clear:both;"></div>

	<?php include template("../xhc/content","footer_NEW"); ?>

</body>
</html>
