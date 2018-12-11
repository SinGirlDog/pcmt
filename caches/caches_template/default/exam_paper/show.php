<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>exam_paper_show_html</title>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/jquery1.7.1.js"></script>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/answer_validate.js"></script>

	<!-- 首尾样式 -->
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/index.css">
	<link rel="stylesheet" type="text/css" href="<?php echo APP_PATH;?>statics_EXAM/css/exam_index.css">
</head>
<body>
	<?php include template("../xhc/content","header_NEW"); ?>
	<div id="show_form">
		<form method="post" name="put_answer_form" id="put_answer_form" action="index.php?m=exam&c=index&a=put_answer">
			<b><?php echo $paper_data['title'];?></b>
			<br/>
			<br/>
			<p>姓名：<?php echo $paper_data['name'];?></p>
			<input type="hidden" name="paper_id" value="<?php echo $paper_data['id'];?>">
			<br/>
			<b>一、单选题</b>
			
			<?php $num_th = 1;
			foreach($quest_choice_only as $key=>$val)
			{
				echo '<ol class="choice_only">';
					
					if($val['question_body'])
					{
						echo "<dl>".$num_th.".".$val['question_body']."</dl>";
					}
					if($val['thumb'])
					{
						echo "<dl><img src='".$val['thumb']."'/></dl>";
					}
					if($val['question_answer'])
					{
						$answer_arr = explode(';',$val['question_answer']);
						foreach($answer_arr as $ans_k=>$ans_v)
						{
							echo "<dl>";
								echo "<label for='only_".$num_th."_".$ans_k."'>
									<input id='only_".$num_th."_".$ans_k."' type='radio' value='".($ans_k+1)."' name='only[".$num_th."]'>
								".$ans_v."</label>";
							echo "</dl>";
						}

					}
					
					$num_th++;
				echo '</ol> ';
			}?>
			
			<b>二、多选题</b>
			<?php 
			foreach($quest_choice_more as $key=>$val)
			{
				echo '<ol class="choice_more">';
					
					if($val['question_body'])
					{
						echo "<dl>".$num_th.".".$val['question_body']."</dl>";
					}
					if($val['thumb'])
					{

						echo "<dl><img src='".$val['thumb']."'/></dl>";
					}
					if($val['question_answer'])
					{
						$answer_arr = explode(';',$val['question_answer']);
						foreach($answer_arr as $ans_k=>$ans_v)
						{
							echo "<dl>";
								echo "<label for='more_".$num_th."_".$ans_k."'>
									<input id='more_".$num_th."_".$ans_k."' type='checkbox' value='".($ans_k+1)."' name='more[".$num_th."][".$ans_k."]'>
								".$ans_v."</label>";
							echo "</dl>";
						}

					}
					
					$num_th++;
				echo "</ol>";
			}?>

			<input type="button" id="put_answer_ajax_button" value="确定">

		</form>
	</div>
	<?php include template("../xhc/content","footer_NEW"); ?>

</body>
</html>
