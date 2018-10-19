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
		<!-- <input type="hidden" name="answer_id" value="<?php echo $answer_data['id'] ?>"> -->
		<p>单项选择：<?php echo $fenshu_only; ?> 分</p>
		<p>多项选择：<?php echo $fenshu_more; ?> 分</p>
		<p>合   计：<?php echo $fenshu_more+$fenshu_only; ?> 分</p>
		<!-- <input type="hidden" name="fenshu" value="<?php echo $fenshu_more+$fenshu_only ?>"> -->
		<!-- <input type="submit" id="put_answer_ajax_button" value="提交"> -->
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
			$user_length = sizeof($answer_choice_more[$num_more_th]);
			$cankao_length = sizeof($cankao_choice_more[$num_more_th]);
			$intersect_length = sizeof(array_intersect($cankao_choice_more[$num_more_th], $answer_choice_more[$num_more_th]));
			if( ($user_length != $cankao_length) || ($intersect_length != $cankao_length) ){
				$current_color = 'style="color:red;"';
			}
			else{
				$current_color = 'style="color:green;"';
			}
			echo "<dl ".$current_color.">回答：".implode('.',$answer_choice_more[$num_more_th])."</dl>";
			echo "<dl>参考答案：".implode('.',$cankao_choice_more[$num_more_th])."</dl>";
			$num_th++;
			$num_more_th++;
			echo "</ol>";
		}?>


	</form>

</body>
</html>
