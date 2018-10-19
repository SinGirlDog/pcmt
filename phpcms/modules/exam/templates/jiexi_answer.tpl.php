<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>exam_paper_show_html</title>
</head>
<body>
	

	<form method="" name="put_answer_form" id="put_answer_form" action="">
		<b><?php echo $paper_data['title'] ?></b>
		<br/>
		<p>姓名：<?php echo $paper_data['name'] ?></p>
		<input type="hidden" name="paper_id" value="{$paper_data['id']}">
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
			if($answer_choice_only[$num_th-1] != $cankao_choice_only[$num_th-1]){
				$current_color = 'style="color:red;"';
			}
			else{
				$current_color = 'style="color:green;"';
			}
			echo "<dl ".$current_color.">回答：".$answer_choice_only[$num_th-1]."</dl>";
			echo "<dl>参考答案：".$cankao_choice_only[$num_th-1]."</dl>";
			
			echo "<dl>解析：".$analysis_key_choice_only[$num_th-1]['question_analysis']."</dl>";
			echo "<dl>考点：".$analysis_key_choice_only[$num_th-1]['question_key']."</dl>";

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
			$cankao_length = sizeof($cankao_choice_more[$num_more_th]);
			$intersect_length = sizeof(array_intersect($cankao_choice_more[$num_more_th], $answer_choice_more[$num_more_th]));
			if( ($answer_length != $cankao_length) || ($intersect_length != $cankao_length) ){
				$current_color = 'style="color:red;"';
			}
			else{
				$current_color = 'style="color:green;"';
			}
			echo "<dl ".$current_color.">回答：".implode('.',$answer_choice_more[$num_more_th])."</dl>";
			echo "<dl>参考答案：".implode('.',$cankao_choice_more[$num_more_th])."</dl>";

			echo "<dl>解析：".$analysis_key_choice_more[$num_more_th]['question_analysis']."</dl>";
			echo "<dl>考点：".$analysis_key_choice_more[$num_more_th]['question_key']."</dl>";

			$num_th++;
			$num_more_th++;
			echo "</ol>";
		}?>

		<input type="button" id="put_answer_ajax_button" value="确定">

	</form>

</body>
</html>
