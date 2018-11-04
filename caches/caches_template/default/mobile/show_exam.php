<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>exam_paper_show_html</title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="太奇兴宏程,一级建造师,二级建造师"/>
	<meta name="description" content="太奇兴宏程作为建造师精准信息专家，一级建造师培训、二级建造师培训高通过率,零基础拿证,在中国每两个建造师就有一个来自太奇兴宏程"/>
	<meta name="viewport" content="width=device-width,initial-scale=1">

	<link rel="stylesheet" href="http://www.xhcedu.com/wap/style/new_mip.css" />
	<link rel="stylesheet" href="http://www.xhcedu.com/wap/style/exam_mobile.css" />

	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/jquery1.7.1.js"></script>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/answer_validate.js"></script>
</head>
<body>
	<?php include template("mobile","header_m"); ?>
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
					foreach($val as $k=>$v)
					{
						if($k=='question_body')
						{
							echo "<dl>".$num_th.".".$v."</dl>";
						}
						else if($k=='thumb')
						{
							if($v)
							{
								echo "<dl><img src='".$v."'/></dl>";
							}
						}
						else
						{
							$answer_arr = explode(';',$v);
							foreach($answer_arr as $ans_k=>$ans_v)
							{
								echo "<dl>";
									echo "<label for='only_".$num_th."_".$ans_k."'>
										<input id='only_".$num_th."_".$ans_k."' type='radio' value='".($ans_k+1)."' name='only[".$num_th."]'>
									".$ans_v."</label>";
								echo "</dl>";
							}

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
					foreach($val as $k=>$v)
					{
						if($k=='question_body')
						{
							echo "<dl>".$num_th.".".$v."</dl>";
						}
						else if($k=='thumb')
						{
							if($v)
							{
								echo "<dl><img src='".$v."'/></dl>";
							}
						}
						else
						{
							$answer_arr = explode(';',$v);
							foreach($answer_arr as $ans_k=>$ans_v)
							{
								echo "<dl>";
									echo "<label for='more_".$num_th."_".$ans_k."'>
										<input id='more_".$num_th."_".$ans_k."' type='checkbox' value='".($ans_k+1)."' name='more[".$num_th."][".$ans_k."]'>
									".$ans_v."</label>";
								echo "</dl>";
							}

						}
					}
					$num_th++;
				echo "</ol>";
			}?>

			<input type="button" id="put_answer_ajax_button" value="确定">

		</form>
	</div>
	<?php include template("mobile","footer_m"); ?>

</body>
</html>
