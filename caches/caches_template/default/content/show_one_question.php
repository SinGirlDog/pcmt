<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!DOCTYPE html>
<html>
<head>
	<title>show_one_question_html</title>

	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/index.css">
	<link rel="stylesheet" type="text/css" href="<?php echo APP_PATH;?>statics_EXAM/css/exam_index.css">
</head>
<body>
	<?php include template("../xhc/content","header_NEW"); ?>
	<div id="show_form">
		<form>
			<b><?php echo $quest_type;?></b>
			<ol class="choice_only">
				<dl>
					题干：<?php echo $question_body;?>
				</dl>
				<?php if($thumb) { ?>
				<dl><img src="<?php echo $thumb;?>"/></dl>
				<?php } ?>
				<?php 
				$answer_arr = explode(';',$question_answer);
				foreach($answer_arr as $key => $answer_one)
				{
					echo "<dl><label style='display:block;' for='only_one".$key."'>
						<input name='only_one' type='radio' id='only_one".$key."'>".$answer_one;
					echo "</label></dl>";
				}
				?>
				<dl>
					参考答案：<?php echo $true_answer;?>
				</dl>
				<dl>
					解析：<?php echo $question_analysis;?>
				</dl>
			</ol>
		</form>
	</div>
	<?php include template("../xhc/content","footer_NEW"); ?>
</body>
</html>
