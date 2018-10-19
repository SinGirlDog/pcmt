<?php
class xml {
	private $dir;
	private $dir_xsl;
	private $file_title;
	private $quest_type;
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
	private $true_answer = '';
	private $q_analysis = '';
	private $body_b = 0;
	private $true_num = 0;
	
	public function init(){
		header("Content-type: text/html; charset=utf-8");
		$this->dir = "2018yjjj_fy.xml";
		if (file_exists($this->dir)){
			$xml=simplexml_load_file($this->dir);
			echo '<pre/>';
			$num = 0;
			$ti_num = 0;
			$data = $xml->Worksheet->Table->Row;
			foreach($data as $item){
				
				if($num == -603){//调试用的开关
					$item = $this->object_to_array($item);

					$arr = $this->parse_one_item($item);
					$str = $this->noemptyarr_implode($arr);
					echo $str,$this->current_field,'LINE-37';
					$this->current_field = $this->judge_field($arr);
					echo $this->current_field,'LINE-39';

					if(strpos($str,"解析")){
						$field = 'question_analysis';
					}
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
					echo $this->quest_type,$str;
					echo "<br/>";
				}
				else
				{
					if($num==0){
						$this->file_title = $str;
						echo $this->file_title;
						echo "<br/>";
					}
					else if($num == -43){//调试用的开关
						if(strpos($str,"考点")){
							echo 'kdkdkd';
						}
						echo $str,$this->current_field;die;
					}
					else{
						$this->show_content($str,$num);
					}
					$num++;
				}
			}
		}
		else{
			echo 'no file xml:'.$this->dir;
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
		$this->body_b = 0;
		if(isset($item['Cell']['Data']['B'])){
			$arr = $item['Cell']['Data']['B']['Font'];
			$this->body_b = 1;
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
			$this->quest_type = "choice_only";
			$quest_type = $this->quest_type;
		}
		else if(strpos($str,'多项选择题')){
			$this->quest_type = "choice_more";
			$quest_type = $this->quest_type;
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
			$field = $this->judge_maybe_quest_key($str);
			break;
			case 'question_key':
			$field = $this->judge_maybe_quest_body($str);
			break;
			default:
			$field = $this->judge_maybe_quest_body($str);
			break;
		}
		return $field;
	}

	private function judge_maybe_quest_body($str){
		$field = '';
		if(strpos($str,'.') || strpos($str,'．')){
			$arr_body = explode('.', $str);
			$arr_body_Ch = explode('．', $str);
			$arr_body[0] = $this->my_trim_by_ord($arr_body[0]);
			$arr_body_Ch[0] = $this->my_trim_by_ord($arr_body_Ch[0]);
			if(($arr_body[0] > 0 && $arr_body[0] < 100) ||
				($arr_body_Ch[0] > 0 && $arr_body_Ch[0] < 100) &&
				$this->body_b == 1)
			{
				$field = 'question_body';
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
			echo 'judge_maybe_quest_answer--else';var_dump($str);die;
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

	private function judge_field_orig($arr){
		$str = $this->noemptyarr_implode($arr);
		if(strpos($str,'.')){
			$des = '.';
		}
		else if(strpos($str,'．')){
			$des = '．';
		}

		$arr_body = explode('.', $str);
		$arr_body_Ch = explode('．', $str);
		$arr_answer = explode('．', $str);
		$arr_answer_En = explode('.', $str);
		
		$field = '';
		$arr_body[0] = $this->my_trim_by_ord($arr_body[0]);
		$arr_body_Ch[0] = $this->my_trim_by_ord($arr_body_Ch[0]);
		$arr_answer[0] = $this->my_trim_by_ord($arr_answer[0]);
		$arr_answer_En[0] = $this->my_trim_by_ord($arr_answer_En[0]);
		
		if(strpos($str,"答案")){
			$field = 'true_answer';
		}
		else if(strpos($str,"解析")){
			$field = 'question_analysis';
		}
		else if(strpos($str,"考点")){
			$field = 'question_key';
		}
		else if(($arr_body[0] > 0 && $arr_body[0] < 100) ||
			($arr_body_Ch[0] > 0 && $arr_body_Ch[0] < 100) ||
			$this->body_b == 1)
		{
			$field = 'question_body';
		}
		else if((in_array($arr_answer_En[0],$this->answer_alpha)) || (in_array($arr_answer[0],$this->answer_alpha))){
			$field = 'question_answer';
		}
		else{
			if($this->current_field == 'question_analysis'){
				$field = $this->current_field;			
			}
		}
		return $field;
	}

	private function show_content($str,$num){
		switch($this->current_field){
			case 'question_body':
			$q_body = $this->make_quest_body($str);
			echo "题干：",$this->true_num,'.',$q_body,"--行号：",$num;
			$this->temp_answer_arr = array();
			echo "<br/>";
			break;
			case 'question_answer':
			$this->make_quest_answer($str);
			break;
			case 'true_answer':
			$this->true_answer = $this->make_other($str);
			echo "备选答案：",implode(';',$this->temp_answer_arr);
			echo "<br/>";
			echo "参考答案：",$this->true_answer;
			$this->q_analysis = '';
			echo "<br/>";
			break;
			case 'question_analysis':
			$this->q_analysis .= $this->make_other($str);
			break;
			case 'question_key':
			echo "解析：",$this->q_analysis;
			echo "<br/>";
			$q_key = $this->make_other($str);
			echo "考点：",$q_key;
			echo "<br/>";
			break;
			case 'question_other':
			$q_body = $this->make_quest_body($str);
			echo "其他：",$q_body;
			echo "<br/>";
			break;
			default :
			echo "<br/>";
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

}
$my_xml = new xml();
$my_xml->init();

?>