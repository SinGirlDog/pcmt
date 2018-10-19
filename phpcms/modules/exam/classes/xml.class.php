<?php
class xml {
	var $dir;
	private $dir_xsl;
	private $file_title;
	private $quest_type = array();
	private $body_arr = array();
	private $answers_arr = array();
	private $true_answers_arr = array();
	private $analysis_arr = array();
	// private $keys_arr = array();
	private $reform_data = array();

	private $current_field = '';
	private $field_list = array(
		'0'=>'question_body',
		'1'=>'question_answer',
		'2'=>'true_answer',
		'3'=>'question_analysis',
		'4'=>'question_key',
	);
	private $temp_answer_arr = array();
	private $answer_alpha = ['A','B','C','D','E'];
	private $quest_type_list = ['choice_only','choice_more','fillinblank','objective'];
	private $true_answer = '';
	private $q_analysis = '';
	// private $body_b = 0;
	private $true_num = 0;
	
	public function init(){
		echo 'if there is no words;maybe the program or the file was wrong <br/>';
		echo '如果下面什么东西都不显示，那可能是程序有问题，或者是文件格式不匹配<br/><br/>';
		// header("Content-type: text/html; charset=utf-8");
		// $this->dir = "2018yjjj_fy.xml";

		// $this->xml_file_parse();
		// $this->reform_parse_result();
		$this->get_reform_data();
		echo '<pre/>';
		// var_export($this->body_arr);
		// var_export($this->answers_arr);
		// var_export($this->true_answers_arr);
		// var_export($this->analysis_arr);
		$this->show_parse_result();
		
	}

	public function get_reform_data(){
		$this->xml_file_parse();
		$this->reform_parse_result();
		return $this->reform_data;
	}



	private function xml_file_parse(){
		if (file_exists($this->dir)){
			$xml=simplexml_load_file($this->dir);
			$num = 0;
			$ti_num = 0;
			$data = $xml->Worksheet->Table->Row;
			foreach($data as $item){
				// echo $num,'<br/>';
				if($num == -678){//调试用的开关
					echo 'TreeNewBee Error!';
					var_dump($item);
					die;
				}
				else{
					$item = $this->object_to_array($item);
					$arr = $this->parse_one_item($item);
					$this->current_field = $this->judge_field($arr);
				}
				
				$str = $this->noemptyarr_implode($arr);

				$quest_type = $this->check_quest_type($str);
				if($quest_type){
					$this->quest_type[] = $quest_type;
				}
				else
				{
					if($num==0){
						$this->file_title = $str;
					}
					else if($num == -679){//调试用的开关
						echo $this->current_field;
						echo $this->q_analysis;
						echo 'TreeNewBee Error：Again!';
						die;
					}
					else{
						$this->collect_content($str,$num);
					}
					$num++;
				}
			}
		}
		else{
			echo 'no file xml:'.$this->dir;
		}
	}

	private function reform_parse_result(){
		if(!empty($this->quest_type)){
			$temp_data_item = array();
			$type_arr = $this->quest_type;
			foreach($type_arr as $key => $val){
				if(in_array($val,$this->quest_type_list)){
					if(!empty($this->body_arr[$val])){
						$question_body_arr = $this->body_arr[$val];
						$question_answers_arr = $this->answers_arr[$val];
						$question_true_answers_arr = $this->true_answers_arr[$val];
						$question_analysis_arr = $this->analysis_arr[$val];
						// $question_keys_arr = $this->keys_arr[$val];

						for($th = 0;$th<sizeof($this->body_arr[$val]);$th++){
							$temp_data_item['quest_type'] = $val;
							$temp_data_item['question_body'] = $question_body_arr[$th];
							$temp_data_item['question_answer'] = $question_answers_arr[$th];
							$temp_data_item['true_answer'] = $question_true_answers_arr[$th];
							$temp_data_item['question_analysis'] = $question_analysis_arr[$th];
							// $temp_data_item['question_key'] = $question_keys_arr[$th];

							$this->reform_data[] = $temp_data_item;
						}

					}
					else{
						echo 'There is empty body arr on '.$val.'!','<br/>';
					}
					
				}
				else{
					echo 'There is An Undefined quest_type!','<br/>';
				}
			}
			
		}
		else{
			echo 'There is no any quest_type','<br/>';
		}
	}

	private function show_parse_result(){

		$this->show_file_title();
		foreach($this->reform_data as $key => $the_one){
			echo "题干：",($key+1),'.',$the_one['question_body'].'<br/>';
			echo "备选答案：".$the_one['question_answer'].'<br/>';
			echo "参考答案：".$the_one['true_answer'].'<br/>';
			echo "解析：".$the_one['question_analysis'].'<br/>';
			// echo "考点：".$the_one['question_key'].'<br/>';
			echo "<br/>";
		}
		
		echo 'hello word!';
	}

	private function show_file_title(){
		if($this->file_title){
			echo $this->file_title,'<br/><br/>';
		}
		else{
			echo 'There is no file_title!','<br/>';
		}
	}

	private function noemptyarr_implode($arr){
		if(is_array($arr)){
			foreach($arr as &$one){
				if(is_array($one)){
					$one = $this->noemptyarr_implode($one);
				}
			}
			return implode('',$arr);
		}
		else{
			return $arr;
		}
	}

	private function object_to_array($object){
		return json_decode(json_encode($object),true);
	}

	private function parse_one_item($item){
		// $this->body_b = 0;
		if(isset($item['Cell']['Data']['B'])){
			$arr = $item['Cell']['Data']['B']['Font'];
			// $this->body_b = 1;
		}
		else if(isset($item['Cell']['Data']['Font'])){
			$arr = $item['Cell']['Data']['Font'];
		}
		else if(isset($item['Cell']['Data'])){
			$arr = [$item['Cell']['Data'],''];
		}
		else{
			$arr = '';
		}
		return $arr;
	}

	private function make_quest_body($str){
		if(strpos($str,'.')){
			$des = '.';
		}
		else if(strpos($str,'．')){
			$des = '．';
		}
		else{
			return $str;
		}
		$arr = explode($des, $str);
		array_shift($arr);
		$new_str = implode($des, $arr);
		return $new_str;
	}

	private function check_quest_type($str){
		$quest_type = '';
		if(strpos($str,'单项选择题')){
			$quest_type = "choice_only";
		}
		else if(strpos($str,'多项选择题')){
			$quest_type = "choice_more";
		}
		return $quest_type;
	}

	private function judge_field($arr){
		$field = '';
		$str = $this->noemptyarr_implode($arr);
		switch($this->current_field){
			case 'question_body':
			$field = $this->judge_maybe_quest_answer($str);
			break;
			case 'question_answer':
			$field = $this->judge_maybe_true_answer($str);
			if(empty($field)){
				$field = $this->judge_maybe_quest_answer($str);
			}
			break;
			case 'true_answer':
			$field = $this->judge_maybe_quest_analysis($str);
			break;
			case 'question_analysis':
			$field = $this->judge_maybe_quest_body($str);
			// $field = $this->judge_maybe_quest_key($str);
			break;
			// case 'question_key'://考点不要了
			// $field = $this->judge_maybe_quest_body($str);
			// break;
			default:
			$field = $this->judge_maybe_quest_body($str);
			break;
		}
		return $field;
	}

	private function judge_maybe_quest_body($str){
		$field = '';
		// if(strpos($str,'.') || strpos($str,'．') && $this->body_b == 1){
		if(strpos($str,'.') || strpos($str,'．')){
			if(strpos($str,'.'))
			{
				$arr_body = explode('.', $str);
				$body_lenth = strlen($arr_body[0]);
				$arr_body[0] = $this->my_trim_by_ord($arr_body[0]);
				if($arr_body[0] > 0 && $arr_body[0] < 100 && $body_lenth < 5)
				{
					$field = 'question_body';
				}
				else
				{//删除考点之后
					$field = $this->current_field;
				}
			}
			else if(strpos($str,'．'))
			{
				$arr_body_Ch = explode('．', $str);
				$body_Ch_lenth = strlen($arr_body_Ch[0]);
				$arr_body_Ch[0] = $this->my_trim_by_ord($arr_body_Ch[0]);
				if($arr_body_Ch[0] > 0 && $arr_body_Ch[0] < 100 && $body_Ch_lenth < 5)
				{
					$field = 'question_body';
				}
				else
				{//删除考点之后
					$field = $this->current_field;
				}
			}
		}
		return $field;
	}

	private function judge_maybe_quest_answer($str){
		$field = '';
		$arr_answer = explode('．', $str);
		$arr_answer_En = explode('.', $str);
		$arr_answer[0] = $this->my_trim_by_ord($arr_answer[0]);
		$arr_answer_En[0] = $this->my_trim_by_ord($arr_answer_En[0]);
		if((in_array($arr_answer_En[0],$this->answer_alpha)) || (in_array($arr_answer[0],$this->answer_alpha))){
			$field = 'question_answer';
		}
		else{
			echo 'judge_maybe_quest_answer--else';var_dump($this->current_field);echo $str,'+',$this->true_num;die;
		}
		return $field;
	}

	private function judge_maybe_true_answer($str){
		$field = '';
		if(strpos($str,"答案")){
			$field = 'true_answer';
		}
		return $field;
	}

	private function judge_maybe_quest_analysis($str){
		$field = '';
		if(strpos($str,"解析")){
			$field = 'question_analysis';
		}
		return $field;
	}

	private function judge_maybe_quest_key($str){
		$field = '';
		if(strpos($str,"考点")){
			$field = 'question_key';
		}
		else{
			$field = $this->current_field;
		}
		return $field;
	}

	private function collect_content($str,$num){
		$current_q_type = $this->get_current_quest_type();
		switch($this->current_field){
			case 'question_body':
			$q_body = $this->make_quest_body($str);
			if($current_q_type){
				if(sizeof($this->body_arr[$current_q_type]) > 0 ){
					$this->analysis_arr[$current_q_type][] = $this->q_analysis;
				}
				else{
					if($current_q_type == 'choice_more'){
						$this->analysis_arr['choice_only'][] = $this->q_analysis;
					}
				}
				$this->body_arr[$current_q_type][] = $q_body;
			}
			else{
				echo $num,$str,'--';
				echo 'Not any quest_type by body!';
				die;
			}
			$this->temp_answer_arr = array();
			break;
			case 'question_answer':
			$this->make_quest_answer($str);
			break;
			case 'true_answer':
			$this->true_answer = $this->make_other($str);
			$this->true_answer = $this->separate_true_answer();
			if($current_q_type){
				$q_answers = implode(';',$this->temp_answer_arr);
				$this->answers_arr[$current_q_type][] = $q_answers;
				$this->true_answers_arr[$current_q_type][] = $this->true_answer;
			}
			else{
				echo 'Not any quest_type by true_answer!';
				die;
			}
			$this->q_analysis = '';
			break;
			case 'question_analysis':
			$this->q_analysis .= $this->make_other($str);
			break;
			// case 'question_key':
			// $q_key = $this->make_other($str);
			// if($current_q_type){
			// 	$this->analysis_arr[$current_q_type][] = $this->q_analysis;
			// 	$this->keys_arr[$current_q_type][] = $q_key;
			// }
			// else{
			// 	echo 'Not any quest_type by key!';
			// 	die;
			// }
			// break;
			default :
			if($current_q_type == 'choice_more'){
				if(sizeof($this->body_arr[$current_q_type]) == 20){
					$this->analysis_arr[$current_q_type][] = $this->q_analysis;
				}
			}
			break;
		}
	}

	private function make_quest_answer($str){
		$this->temp_answer_arr[] = $str;
	}

	private function make_other($str){
		if(strpos($str,'】')){
			$arr = explode('】',$str);
			$result = $arr[1];
		}
		else{
			$result = $str;
		}
		return $result;
	}

	private function separate_true_answer(){
		$answer = $this->true_answer;
		$answer = $this->my_trim_for_true_answer($answer);
		if(strlen($answer)==1){
			return $answer;
		}
		else{
			$answer_arr = str_split($answer);
			$answer = implode('.', $answer_arr);
			return $answer;
		}
	}

	private function my_trim_for_true_answer($answer){
		$arr = str_split($answer);
		while(list($k,$v) = each($arr)){
			if(in_array($v,$this->answer_alpha)){
				//A-E
			}
			else{
				// echo ord($v);
				array_splice($arr, $k,1);
			}
		}
		$new_answer = implode('',$arr);
		return $new_answer;
	}

	private function my_trim_by_ord($str){

		if(strlen($str)==1){
			if($str > 0 && $str < 10){
				$this->true_num = $str;
				return $str;
			}
			else if(in_array($str,$this->answer_alpha)){
				return $str;
			}
		}
		else if(strlen($str)==2){
			if($str > 9 && $str < 100){
				$this->true_num = $str;
				return $str;
			}
		}

		$arr = str_split($str);

		while(list($k,$v) = each($arr)){
			if(ord($v) <=52 && ord($v) >=49){
				//数字0-9
			}
			else if(ord($v) <=90 && ord($v) >=65){
				//字母A-Z
			}
			else{
				// echo ord($v),'mem';
				array_splice($arr, $k,1);
			}
		}
		$new_str = implode('',$arr);
		$new_str = $this->my_trim_before_ord($new_str);
		// echo ord(0),'---'; 
		// echo ord(9),'---'; 
		// echo ord('A'),'---'; 
		// echo ord('Z'),'---'; 
		// echo ord('a'),'---'; 
		// echo ord('z'),'---'; 

		// var_dump($new_str);die;
		return $new_str;
	}

	private function my_trim_before_ord($str){
		if(strlen($str)==1){
			if($str > 0 && $str < 10){
				$this->true_num = $str;
				return $str;
			}
			else if(in_array($str,$this->answer_alpha)){
				return $str;
			}
		}
		else if(strlen($str)==2){
			if($str > 9 && $str < 100){
				$this->true_num = $str;
				return $str;
			}
		}
		else{
			return $str;
		}
	}

	private function get_current_quest_type(){
		$current_q_type = '';
		$quest_type_arr = $this->quest_type;
		$current_q_type = array_pop($quest_type_arr);
		return $current_q_type;
	}

}
?>