<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>exam_paper_show_html</title>
	<script type="text/javascript" src="<?php echo APP_PATH;?>statics_EXAM/js/jquery1.7.1.js"></script>
	<!-- 首尾样式 -->
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH_XHC;?>18xhc/index.css">
	<link rel="stylesheet" type="text/css" href="<?php echo APP_PATH;?>statics_EXAM/css/exam_index.css">
	<script type="text/javascript">
		$(function() {
			　　if (window.history && window.history.pushState){
				$(window).on('popstate', function () {
					window.history.pushState('forward', null, '#');
					window.history.forward(1);
				});
			}
			window.history.pushState('forward', null, '#'); //在IE中必须得有这两行
			window.history.forward(1);
		});
	</script>
</head>
<body>
	<?php include template("../xhc/content","header_NEW"); ?>
	<div id="jiexi_form">
		<input id="msg" type="hidden" value="<?php echo $msg; ?>">
		<input id="url" type="hidden" value="<?php echo $url; ?>">
		<form method="post" name="" id="jiexi_answer_form" action="<?php echo APP_PATH;?>index.php?m=exam&c=index&a=answer_history_init">
			<p>单项选择：<?php echo $answer_data['fenshu_choice_only']; ?> 分</p>
			<br/>
			<p>多项选择：<?php echo $answer_data['fenshu_choice_more']; ?> 分</p>
			<br/>
			<p>合   计：<?php echo $answer_data['fenshu_choice_more']+$answer_data['fenshu_choice_only']; ?> 分</p>
			<br/>
			<p>排   名：第 <?php echo $paiming['rownum']; ?> 名；
				成功战胜了 <?php echo substr($paiming['percent'],0,5); ?>% 的对手；再接再厉； </p>
			<br/>
			<b><?php echo $paper_data['title'] ?></b>
			<br/>
			<br/>
			<p>姓名：<?php echo $paper_data['name'] ?></p>
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
										<input style='display:none;' id='only_".$num_th."_".$ans_k."' type='radio' value='".($ans_k+1)."' name='only[".$num_th."]'>
									".$ans_v."</label>";
								echo "</dl>";
							}

						}
					}
					if($answer_choice_only[$num_th-1] != $cankao_choice_only['answer'][$num_th-1])
					{
						$current_color = 'style="color:red;"';
					}
					else
					{
						$current_color = 'style="color:green;"';
					}
					echo "<dl ".$current_color.">回答：".$answer_choice_only[$num_th-1]."</dl>";
					echo "<dl>参考答案：".$cankao_choice_only['answer'][$num_th-1]."</dl>";

					echo "<dl>解析：".$analysis_key_choice_only[$num_th-1]['question_analysis']."</dl>";

					$num_th++;
				echo '</ol> ';
			}?>

			<b>二、多选题</b>
			<?php $num_more_th = 0;
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
										<input style='display:none;' id='more_".$num_th."_".$ans_k."' type='checkbox' value='".($ans_k+1)."' name='more[".$num_th."][".$ans_k."]'>
									".$ans_v."</label>";
								echo "</dl>";
							}

						}
					}
					$answer_length = sizeof($answer_choice_more[$num_more_th]);
					$cankao_length = sizeof($cankao_choice_more['answer'][$num_more_th]);
					$intersect_length = sizeof(array_intersect($cankao_choice_more['answer'][$num_more_th], $answer_choice_more[$num_more_th]));
					if( ($answer_length != $cankao_length) || ($intersect_length != $cankao_length) )
					{
						$current_color = 'style="color:red;"';
					}
					else
					{
						$current_color = 'style="color:green;"';
					}
					echo "<dl ".$current_color.">回答：".implode('.',$answer_choice_more[$num_more_th])."</dl>";
					echo "<dl>参考答案：".implode('.',$cankao_choice_more['answer'][$num_more_th])."</dl>";

					echo "<dl>解析：".$analysis_key_choice_more[$num_more_th]['question_analysis']."</dl>";

					$num_th++;
					$num_more_th++;
				echo "</ol>";
			}?>

			<input type="button" id="jiexi_answer_button" value="收起">

		</form>
	</div>
	<?php include template("../xhc/content","footer_NEW"); ?>
</body>
<script type="text/javascript">
	$(document).ready(function(){
		var msg = $('#msg').val();
		var url = $('#url').val();
		$('#jiexi_answer_button').click(function(){
			$('#jiexi_answer_form').submit();
		});
		// var r = confirm(msg);
		// if (r==true){
		// 	window.location.replace(url);
		// }
		// else{
		// 	window.location.replace(url);
		// }
	});
</script>
</html>
