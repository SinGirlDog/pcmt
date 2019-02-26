<?php
class xcx{
	
	function __construct() {
        // 加载category的数据模型;查询相关分类
		$this->category_db = pc_base::load_model('category_model');
		$this->exam_db = pc_base::load_model('exam_model');
		$this->exam_data_db = pc_base::load_model('exam_data_model');
		$this->exam_paper_db = pc_base::load_model('exam_paper_model');
		$this->exam_answer_db = pc_base::load_model('exam_answer_model');
		$this->exam_file_db = pc_base::load_model('exam_file_model');
		$this->exam_visitor_db = pc_base::load_model('exam_visitor_model');
		$this->exam_qanda_db = pc_base::load_model('exam_qanda_model');
		$this->wxxcx_visitor_db = pc_base::load_model('wxxcx_visitor_model');
		$this->exam_wxrecord_db = pc_base::load_model('exam_wxrecord_model');
		$this->picture_db = pc_base::load_model('picture_model');
		$this->picture_data_db = pc_base::load_model('picture_data_model');
		$this->news_db = pc_base::load_model('news_model');
		$this->news_data_db = pc_base::load_model('news_data_model');

		$this->top_catid = $this->get_top_catid();
		// $this->paper_setting = $this->get_paper_setting();

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

	private function get_top_catid(){
		$where = array('catname'=>'模拟考试');
		$result = $this->category_db->get_one($where,'catid');
		return $result['catid'];
	}

	public function get_catid_name_arr($parentid){
		$parentid = $parentid ? $parentid : $this->top_catid;
		$where = array('parentid'=>$parentid);
		$data = 'catid,catname';
		$catid_name_arr = $this->category_db->select($where,$data);
		return $catid_name_arr;
	}

	function get_filelist_by_catid($catid){
		$filelist = array();
		$where = array('catid'=>$catid,'isdelete'=>0);
		$datafield = 'id,quest_ids,title';
		$filelist = $this->exam_file_db->select($where,$datafield);
		return $filelist;
	}

	function get_data_by_fileid($fileid){
		$fdata = $this->exam_file_db->get_one('id='.$fileid);
		return $fdata;
	}

	function get_data_by_questid($qid){
		$qdata = $this->exam_data_db->get_one('id='.$qid);
		return $qdata;
	}

	function get_recdata_by_id($recid){
		$fdata = $this->exam_wxrecord_db->get_one('id='.$recid);
		return $fdata;
	}
	function get_one_wxrec_by_condition($wxid,$fileid){
		$wxrec_one = array();
		if(!empty($wxid) && !empty($fileid)){
			$condition=array('wxid'=>$wxid,'fileid'=>$fileid,'isdelete'=>0);
			$wxrec_one = $this->exam_wxrecord_db->get_one($condition);
			if(empty($wxrec_one)){
				$this->make_one_wxrec_by_condition($wxid,$fileid);
				$wxrec_one = $this->get_one_wxrec_by_condition($wxid,$fileid);
			}
		}
		return $wxrec_one;
	}

	function make_one_wxrec_by_condition($wxid,$fileid){
		if(!empty($wxid) && !empty($fileid)){
			$data = array();
			$data = $this->prepare_record_data_byfileid($fileid);	
			$data['wxid']=$wxid;		
			$this->exam_wxrecord_db->insert($data);
		}
	}

	function update_wxrec_answer_by_id($recid,$qid,$answerStr){
		if(empty($recid) || empty($qid)){
			return false;
		}
		else{
			$temp_arr = array();
			$answer_arr = explode(',', $answerStr);
			$temp_arr[$qid] = $answer_arr;

			if(sizeof($answer_arr)<2){
				$choice_key = 'answer_choice_only';
			}
			else{
				$choice_key = 'answer_choice_more';
			}
			$recdata = $this->get_recdata_by_id($recid);
			$answer_orig_arr = json_decode($recdata[$choice_key],true);
			$answer_orig_arr[] = $temp_arr;
			// if(empty($answer_orig_arr)){
			// 	$answer_new_arr = $temp_arr;
			// }
			// else{
			// 	$answer_new_arr = array_merge($answer_orig_arr, $temp_arr);
			// }
			// $answer_json = json_encode($answer_orig_arr).json_encode($temp_arr);
			$answer_json = json_encode($answer_orig_arr);

			$data = array();
			$data[$choice_key] = $answer_json;
			$where = array('id'=>$recid);			
			$rebool = $this->exam_wxrecord_db->update($data,$where);
			return $rebool;
		}
	}

	function wxVisitor($data){
		if($data['openid']){
			$where['openid'] = $data['openid'];
			$already = $this->wxxcx_visitor_db->get_one($where);
			if(empty($already)){
				$wxvid = $this->wxxcx_visitor_db->insert($data,true);
				return $wxvid;
			}
			else{
				$this->wxxcx_visitor_db->update($data,$where);
				return $already['id'];
			}
		}		
	}

	function get_data_by_wxvid($id){
		$data = array();
		if(!empty($id)){
			$where = array('id'=>$id);
			$data = $this->wxxcx_visitor_db->get_one($where);
		}
		return $data;
	}

	function getFocusPic($limit = 4,$catid = 6){
		$where=array();
		$where['catid'] = 6;
		$fields = 'title,thumb';
		$limit = $limit;
		$order = 'id DESC';
		$res = $this->news_db->select($where,$fields,$limit,$order);
		return $res;
	}

	function getHotgoods($limit = 6){
		$where=array();
		$where['catid'] = 6;
		$fields = 'title,thumb';
		$limit = $limit;
		$order = 'listorder DESC';
		$res = $this->news_db->select($where,$fields,$limit,$order);
		return $res;
	}

	function get_catname_by_catid($catid){
		if(empty($catid)){
			return '未选择';
		}
		else{
			$where = array('catid'=>$catid);
			$result = $this->category_db->get_one($where,'catname');
			return $result['catname'];
		}
	}

	function prepare_record_data_byfileid($fileid){
		$record = array();
		$file = $this->get_data_by_fileid($fileid);
		if(!empty($file)){
			$record['fileid'] = $fileid;
			$record['arrparentid'] = $file['arrcatid'];
			$record['title'] = $file['title'];
			$record['siteid'] = SITEID;
			$record['addtime'] = SYS_TIME;
			$record['quest_choice_only'] = $this->get_quest_by_quest_ids('choice_only',$file['quest_ids']);
			$record['quest_choice_more'] = $this->get_quest_by_quest_ids('choice_more',$file['quest_ids']);			
		}
		return $record;
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

	function arr_jiangwei($arr){
		$ret_arr = array();
		if(!empty($arr)){
			array_map(function($val) use(&$ret_arr){
				foreach($val as $k=>$v){
					$ret_arr[$k] = $v;
				}
			},$arr);
		}
		return $ret_arr;
	}

	function removeMiddleBracket($MDstr){
		$temp_arr = array();
		$temp_arr = explode('[',$MDstr);
		$temp_arr = explode(']',$temp_arr[1]);
		return $temp_arr[0];
	}

	function correct_answers($answered_arr,$numth,$qtype,$true_x16_answer){
		$ret = '';
		if(!empty($answered_arr)){
			if($qtype==1){
				foreach($answered_arr as $ank=>$anv){
					$cur_answer = 16*pow(2,$numth-1);
					// return $anv.'-'.$numth.'-'.$true_x16_answer.'-'.$cur_answer;
					// return ($anv != $numth && $true_x16_answer==$cur_answer);
					if($anv == $numth && $true_x16_answer==$cur_answer){
						$ret = 'success';
					}
					else if($anv == $numth && $true_x16_answer!=$cur_answer){
						$ret = 'error';
					}
					else if($anv != $numth && $true_x16_answer==$cur_answer){
						$ret = 'active-success';
					}
				}
			}
			else if($qtype==2){

			}
		}
		return $ret;		
	}
}
?>