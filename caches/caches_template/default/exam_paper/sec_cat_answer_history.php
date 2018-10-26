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
	<div id="third_history_list">
		<div id="third_left" class="history_left">
			<ul>
				<?php
				foreach($list_thi as $rditem)
				{
					if($rditem['catid'] == $thi_cat)
					{
						$class_li = 'style = "background-color: #17B;"';
						$class_a = 'style = "color:#fff;"';
					}
					else
					{
						$class_li = '';
						$class_a = '';
					}
					?>
					<li <?php echo $class_li; ?> id="<?php echo $rditem['catid'] ?>">
						<a <?php echo $class_a; ?>>
							<?php echo $rditem['catname'] ?>
							
						</a>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<div id="third_answer_list">
			<form method="post" name="put_answerid_form" id="put_answerid_form" action="index.php?m=exam&c=index&a=show_jiexi_result">
				<input type="hidden" name="answer_id" id="answer_id" value="">
			</form>
			<ul>
				<?php
				foreach($answer_history_list as $history)
				{
					echo '<li id="'.$history['id'].'"><a>'.$history['cattitle'].$history['title'].'</a></li>';
				}
				?>
			</ul>
		</div>
	</div>
	<div style="clear:both;"></div>

	<?php include template("../xhc/content","footer_NEW"); ?>

</body>
</html>
