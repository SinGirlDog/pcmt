<?php 
class exam_index {
	var $top_catid;
    private $Alpha_Arr = array(
        'A','B','C','D','E',
        'F','G','H','I','J',
        'K','L','M','N','O',
        'P','Q','R','S','T',
        'U','V','W','X','Y','Z'
    );
    function __construct() {
        // 加载category的数据模型;查询相关分类
      $this->category_db = pc_base::load_model('category_model');
      $this->exam_db = pc_base::load_model('exam_model');
      $this->exam_data_db = pc_base::load_model('exam_data_model');
      $this->exam_paper_db = pc_base::load_model('exam_paper_model');
      $this->exam_answer_db = pc_base::load_model('exam_answer_model');
      $this->exam_file_db = pc_base::load_model('exam_file_model');
      $this->exam_visitor_db = pc_base::load_model('exam_visitor_model');

      $this->top_catid = $this->get_top_catid();
      $this->paper_setting = $this->get_paper_setting();

        // 取得当前登录会员的会员名(username)和会员ID(userid)
        // $this->_username = param::get_cookie('_username');
        // $this->_userid = param::get_cookie('_userid');

        //定义站点ID常量，选择模版使用
      $siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : get_siteid();
      define("SITEID", $siteid);

          //读取配置
      $setting = new_html_special_chars(getcache('exam', 'commons'));
      $this->set = $setting[SITEID];
  }

  private function _session_start(){
        /*V9验证码的数值是通过SESSION传递，故在这段代码中，首先加载配置文件，                 
        取出当前系统配置中SESSION的存储方式。然后根据SESSION的存储方式，来加载对应的系统类库*/
        $session_storage = 'session_'.pc_base::load_config('system','session_storage');
        pc_base::load_sys_class($session_storage);
    }

    public function show_one_paper(){
    	$paper_id = $_GET['paper_id'];
    	$paper_data = $this->get_paper_by_pid($paper_id);
    	$quest_choice_only = $this->get_quest_by_ids($paper_data['quest_choice_only']);
    	$quest_choice_more = $this->get_quest_by_ids($paper_data['quest_choice_more']);
    	include template('exam_paper', 'show');
    }

    public function ajax_select(){
    	$category_any = array();
    	if($_GET['param_id'])
    	{
    		$par_id = $_GET['param_id'];
    		$category_any = $this->get_catid_name_arr($par_id);
    	}
    	$ResultHtml = $this->make_select_options_html($category_any);
    	echo $ResultHtml;
    }

    public function get_catid_name_arr($parentid){
    	$parentid = $parentid ? $parentid : $this->top_catid;
    	$where = array('parentid'=>$parentid);
    	$data = 'catid,catname';
    	$catid_name_arr = $this->category_db->select($where,$data);
    	return $catid_name_arr;
    }
    
    private function make_select_options_html($catid_name_arr){
    	$options_html = '<option value ="">-请-选-择-</option>';
    	if(!empty($catid_name_arr))
    	{
    		foreach($catid_name_arr as $num_key=>$cate_data)
    		{
    			$options_html .= '<option value ="'.$cate_data['catid'].'">'.$cate_data['catname'].'</option>';
    		}
    	}
    	return $options_html;
    }

    public function make_paper_title($arrparentid){
    	$catid_arr = explode(',', $arrparentid);
    	$paper_title = '';
    	foreach($catid_arr as $key=>$catid)
    	{
    		$where = array('catid'=>$catid);
    		$data = 'catname';
    		$name_arr = $this->category_db->get_one($where,$data);
    		$paper_title .= $name_arr['catname'].'-';
    	}
    	$paper_title .= date('YmdHis');
    	return $paper_title;
    }

    public function get_paper_by_pid($paper_id){
    	$where = array('id' => $paper_id);
    	$paper_data = $this->exam_paper_db->select($where);
    	return $paper_data[0];
    }

    public function get_quest_by_ids($ids){
    	$id_arr = explode(',', $ids);
    	$field = 'question_body,question_answer';
    	$question_arr = array();
    	foreach($id_arr as $key=>$id){
    		$where = array('id'=>$id);
    		$exam_data_arr = $this->exam_data_db->get_one($where,$field);
    		$q_body['question_body'] = array_shift($exam_data_arr);
    		$q_thumb = $this->exam_db->get_one($where,'thumb');
    		$q_answer = $exam_data_arr;
    		$question_arr[] = array_merge($q_body,array_merge($q_thumb,$q_answer));
    	}
    	return $question_arr;
    }

    public function save_paper_data($paper){
    	$result = array();
    	$this->exam_paper_db->insert($paper);
    	$current_id = $this->exam_paper_db->insert_id();
    	$msg = 'already_paper';
    	$result['id'] = $current_id;
    	$result['msg'] = $msg;
    	return $result;
    }

    private function get_top_catid(){
    	$where = array('catname'=>'模拟考试');
    	$result = $this->category_db->get_one($where,'catid');
    	return $result['catid'];
    }

    private function get_paper_setting(){
    	$this->exam_qanda_db = pc_base::load_model('exam_qanda_model');
    	$result = $this->exam_qanda_db->select();
    	$setting = array();
    	foreach($result as $item){
    		$setting[$item['catid']]['choice_only'] = array(
    			'type' => 'choice_only',
    			'num' => $item['num_choice_only'],
    			'fenshu' => $item['fenshu_choice_only'],
    		);
    		$setting[$item['catid']]['choice_more'] = array(
    			'type' => 'choice_more',
    			'num' => $item['num_choice_more'],
    			'fenshu' => $item['fenshu_choice_more'],
    		);
    		$setting[$item['catid']]['fillinblank'] = array(
    			'type' => 'fillinblank',
    			'num' => $item['num_fillinblank'],
    			'fenshu' => $item['fenshu_fillinblank'],
    		);
    		$setting[$item['catid']]['objective'] = array(
    			'type' => 'objective',
    			'num' => $item['num_objective'],
    			'fenshu' => $item['fenshu_objective'],
    		);
    	}
    	return $setting;
    }

	//通过arrcatid获取各级栏目名称，串联成为cattitle名
    public function make_cattitle($infos){
    	foreach($infos as $key => &$val){
    		$val['cattitle'] = $this->get_catnames_by_catids($val['arrcatid']);
    	}
    	return $infos;
    }

    public function prepare_paper_data_byfileid($fileid){
    	$paper = array();
    	$super_info = $_SESSION['super_info'];
    	$file = $this->exam_file_db->get_one(array('id'=>$fileid));
    	if(!empty($file)){
    		$catid = $file['catid'];
            $paper['fileid'] = $fileid;
            $paper['arrparentid'] = $this->top_catid.','.$file['arrcatid'];
            $file['cattitle'] = $this->get_catnames_by_catids($file['arrcatid']);
            $paper['title'] = $file['cattitle'].$file['title'].date('YmdHis');
            $paper['name'] = $super_info['name'];
            $paper['mobile'] = $super_info['mobile'];
            $paper['siteid'] = SITEID;
            $paper['addtime'] = SYS_TIME;
            $paper['quest_choice_only'] = $this->get_quest_by_quest_ids('choice_only',$file['quest_ids']);
            $paper['quest_choice_more'] = $this->get_quest_by_quest_ids('choice_more',$file['quest_ids']);
        }
        return $paper;

    }

    private function prepare_paper_data(){
    	$paper = array();
    	$catid = $_POST['cat_level_2'];
    	$paper['arrparentid'] = $this->top_catid.','.$_POST['cat_level_1'].','.$_POST['cat_level_2'];
    	$paper['title'] = $this->make_paper_title($paper['arrparentid']);
    	$paper['name'] = $_POST['name'];
    	$paper['mobile'] = $_POST['mobile'];
    	$paper['siteid'] = SITEID;
    	$paper['addtime'] = SYS_TIME;
    	$paper['quest_choice_only'] = $this->get_quest_by_type($this->paper_setting[$catid]['choice_only'],$catid);
    	$paper['quest_choice_more'] = $this->get_quest_by_type($this->paper_setting[$catid]['choice_more'],$catid);
    	return $paper;
    }

    private function get_quest_by_quest_ids($qtype,$quest_ids){
    	$quest_id_arr = explode(',',$quest_ids);
    	$quest_arr = array();
    	foreach($quest_id_arr as $quest_id){
    		$qt = $this->exam_data_db->get_one(array('id'=>$quest_id),'quest_type');
    		if($qt['quest_type'] == $qtype){
    			$quest_arr[] = $quest_id;
    		}
    	}
    	$real_quest = implode(',',$quest_arr);
    	return $real_quest;

    }

    private function get_quest_by_type($quest_setting_arr,$catid){
    	$quest_str = '';
    	if(empty($quest_setting_arr)){
    		return $quest_str;
    	}
    	$where = array('quest_type'=>$quest_setting_arr['type']);
    	$data = 'id';
    	$limit = $quest_setting_arr['num'];
    	$order = 'rand()';

        //调试应用的实例赋值
        // $where = array('quest_type'=>'choice_only');
        // $limit = 20;
        // $catid = 39;

    	$quest_arr = $this->exam_data_db->left_select($where,$data,$limit,$order,$catid);
    	foreach($quest_arr as $num_key => $id_val)
    	{
    		$quest_str .= $id_val['id'];
    		if(($num_key+1) != $quest_setting_arr['num'])
    		{
    			$quest_str .= ',';
    		}
    	}
    	return $quest_str;
    }

    public function visitor_info_check(){
    	if (!( isset($_POST['name']) && trim($_POST['name']) 
    		&& isset($_POST['mobile']) && trim($_POST['mobile']) 
    		&& isset($_POST['cat_level_1']) && trim($_POST['cat_level_1']) ) )
    	{
    		$result['msg'] = 'data_error';
    		echo json_encode($result);
    		exit;
    	}

    	if(empty($_SESSION['exam_visit_'.$_POST['mobile']])){
    		$where = array('mobile'=>$_POST['mobile']);
    		$visit_one = $this->exam_visitor_db->get_one($where);
    		if(empty($visit_one)){
    			$data = array(
    				'name'=>$_POST['name'],
    				'mobile'=>$_POST['mobile'],
    				'addtime'=>SYS_TIME,
    			);
    			$visit_id = $this->exam_visitor_db->insert($data,true);
    		}
    		else{
    			$data = array('visittime'=>SYS_TIME);
    			$visit_up = $this->exam_visitor_db->update($data,$where);
    		}
    		$_SESSION['exam_visit_'.$_POST['mobile']] = 1;
    		$_SESSION['super_info'] = $_POST;
    	}
    }

    public function visitor_paper_check(){
        if(empty($_SESSION['super_info'])){
            $this->show_my_message('无效的信息','/index.php?m=exam');
        }
        else{
            $mobile = $_SESSION['super_info']['mobile'];
            if($_SESSION['exam_visit_'.$mobile] != 1){
                $this->show_my_message('无效的信息','/index.php?m=exam');
            }
        }
    }

    public function get_catnames_by_catids($catids){
    	$cattitle = '';
    	$catid_arr = array();
    	$catid_arr = explode(',',$catids);
    	foreach($catid_arr as $catid){
    		$result = $this->category_db->get_one(array('catid'=>$catid),'catname');
    		$cattitle .= $result['catname'];
    		$cattitle .= '-';
    	}
    	return $cattitle;
    }

    public function get_paper_data_byid($paper_id) {
        if($paper_id)
        {
            $paper_data = array();
            $where = array(
                'id'=>$paper_id
            );
            $paper_data = $this->exam_paper_db->get_one($where);
            return $paper_data;  
        }
        else{
            showmessage('无效的paper_id',HTTP_REFERER);
        }

    }

    public function save_answer_data($paper_data){
        if(empty($paper_data)){
            showmessage('无效的paper_data',HTTP_REFERER);
        }
        else{
            $answer = array();

            $cankao_choice_only = $this->get_cankao_answer_by_ids($paper_data['quest_choice_only']);
            $cankao_choice_more = $this->get_cankao_answer_by_ids($paper_data['quest_choice_more']);
            $answer_only = $this->map_number_to_alphabet($_POST['only']);
            $answer_more = $this->parse_choice_more_answer(json_encode($_POST['more']));
            $fenshu_only = $this->correct_fenshu_only($answer_only,$cankao_choice_only);
            $fenshu_more = $this->correct_fenshu_more($answer_more,$cankao_choice_more);

            $answer['fenshu_choice_only'] = $fenshu_only;
            $answer['fenshu_choice_more'] = $fenshu_more;
            $answer['fenshu_total'] = $fenshu_more+$fenshu_only;
            $answer['paper_id'] = $paper_data['id'];
            $answer['paper_id'] = $paper_data['id'];
            $answer['fileid'] = $paper_data['fileid'];
            $answer['title'] = $paper_data['title'];
            $answer['name'] = $paper_data['name'];
            $answer['mobile'] = $paper_data['mobile'];
            $answer['answer_choice_only'] = implode(',',$_POST['only']);
            $answer['answer_choice_more'] = json_encode($_POST['more']);
            $answer['addtime'] = SYS_TIME;
            $answer['siteid'] = SITEID;
            $insert_id = $this->exam_answer_db->insert($answer,true);
            if($insert_id){
                $result['msg'] = 'answer_saved';
                $result['answer_id'] = $insert_id;
            }
            else{
                $result['msg'] = 'answer_unsave';
            }
            return $result;
        }
    }

    public function show_my_message($msg='',$url=''){
        include template('exam_paper', 'show_my_message');
    }

    public function get_answer_by_id($answer_id){
        $where = array('id'=>$answer_id);
        $answer_data = $this->exam_answer_db->get_one($where);
        return $answer_data;
    }

    public function parse_choice_only_answer($answer_choice_only){
        $number_arr = explode(',', $answer_choice_only);
        $alpha_arr = $this->map_number_to_alphabet($number_arr);
        return $alpha_arr;
    }

    public function parse_choice_more_answer($answer_choice_more){
        $number_arr = json_decode($answer_choice_more);
        $answer_arr = array();
        foreach($number_arr as $key => $val){
            $answer_arr[] = $this->map_number_to_alphabet($val);
        }
        return $answer_arr;
    }

    private function map_number_to_alphabet($number_arr){
        $alpha_arr = array();
        foreach($number_arr as $key=>$val){
            $alpha_arr[] = $this->Alpha_Arr[$val-1];
        }
        return $alpha_arr;
    }

    public function get_cankao_answer_by_ids($quest_ids_arr){
        $id_arr = explode(',', $quest_ids_arr);
        $true_answer = array();
        foreach($id_arr as $key=>$id){
            $where = array('id'=>$id);
            $fields ='true_answer, quest_type';
            $answer = $this->exam_data_db->get_one($where,$fields);
            if($answer['quest_type'] == 'choice_only'){
                $true_answer[] = $answer['true_answer'];
            }
            else if($answer['quest_type'] == 'choice_more'){
                $true_answer[] = explode('.',$answer['true_answer']);
            }
        }
        return $true_answer;
    }

    public function get_analysis_key_by_ids($quest_ids_arr){
        $id_arr = explode(',', $quest_ids_arr);
        $analysis_key = array();
        foreach($id_arr as $key=>$id){
            $where = array('id'=>$id);
            $fields ='question_analysis, question_key';
            $analysis_key[] = $this->exam_data_db->get_one($where,$fields);
        }
        return $analysis_key;

    }

    public function correct_fenshu_only($answer_arr,$cankao_arr){
        $fenshu = 0;
        foreach($answer_arr as $key => $answer){
            if($answer == $cankao_arr[$key]){
                $fenshu++;
            }
        }
        return $fenshu;
    }

    public function correct_fenshu_more($answer_arr,$cankao_arr){
        $fenshu = 0;
        if(is_array($answer_arr[0])){
            foreach($answer_arr as $key => $answer){
                $answer_length = sizeof($answer);
                if($answer_length > 4 or $answer_length < 2){
                    //选项太少或者太多直接零分;
                    continue;
                }
                $cankao_length = sizeof($cankao_arr[$key]);
                $intersect_length = sizeof(array_intersect($answer, $cankao_arr[$key]));
                if( ($answer_length == $cankao_length) && ($intersect_length == $cankao_length) ){
                    //选项完全匹配则满分;
                    $fenshu += 2;
                    continue;
                }
                $temp_fen = 0;
                foreach($answer as $ans){
                    //选对一个半分;选错一个零蛋;
                    if(in_array($ans,$cankao_arr[$key])){
                        $temp_fen += 0.5;
                    }
                    else{
                        $temp_fen = 0;
                        break;
                    }
                }
                $fenshu += $temp_fen;
            }
        }
        return $fenshu;
    }

    public function get_paiming($answer_data){
        $paiming = array();
        $paiming['rownum'] = $this->exam_answer_db->select_paiming($answer_data['id']);
        $allnum = $this->exam_answer_db->count(array('fileid'=>$answer_data['fileid']));
        if($paiming['rownum'] == 1){
            $paiming['percent'] = 98.76;
        }
        else{
            $paiming['percent'] = 100*($allnum - $paiming['rownum'])/($allnum - 1);
        }
        return $paiming;
    }


}
?>