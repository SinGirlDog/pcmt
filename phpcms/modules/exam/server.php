<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('xcx', 'exam', 0);
pc_base::load_app_func('global','exam');

class server {
	private $Alpha_Digital = array(
		'A'=>16,
		'B'=>32,
		'C'=>64,
		'D'=>128,
		'E'=>256,
	);

	function __construct(){
		$this->XCX = new xcx();
	}

	function getHotgoods(){
		$hotgoods = $this->XCX->getFocusPic(6,6);
		$before = json_encode($hotgoods);
		$middle = str_replace('thumb', 'pic_url',$before);
		$after = str_replace('title', 'name',$middle);
		echo $after;
		exit();
	}
	function getBannerList(){
		$banner_big = array();
		$banner_big = $this->XCX->getFocusPic(4,6);
		$banner_little = array();
		$banner_little[] = array(			
			"pic_url"=>"http://zqh.xhcedu.com/statics/images/18xhc/kczx_01.png",
			"title"=>"喜闻乐见",
		);

		$banner_little[] = array(	
			"pic_url"=>"http://zqh.xhcedu.com/statics/images/18xhc/kczx_02.png",
			"title"=>"大快人心",
		);
		$banner_little[] = array(
			"pic_url"=>"http://zqh.xhcedu.com/statics/images/18xhc/kczx_03.png",
			"title"=>"普天同庆",
		);
		$banner_little[] = array(
			"pic_url"=>"http://zqh.xhcedu.com/statics/images/18xhc/kczx_04.png",
			"title"=>"奔走相告",			
		);
		$banner_list[]['banner'] = $banner_big;
		$banner_list[]['banner'] = $banner_little;
		$before = json_encode($banner_list);
		$after = str_replace('thumb', 'pic_url',$before);
		echo $after;
		exit();
	}

	function getCourseList(){

		// echo json_encode($_GET);die();
		$catid = remove_xss($_GET['catid']);
		$wxvisitorid = remove_xss($_GET['wxvisitorid']);
		//戊戌年新增此参数意图用在针对每个微信访客创建各自的考卷数据库;习题+答案;无评分;暂定没人每卷一条数据;
		//Edit By Falsysun 2019-01-25 18:14:38
		$wxVdata = $this->XCX->get_data_by_wxvid($wxvisitorid);
		$curcatname = $this->XCX->get_catname_by_catid($catid);

		// $lsit_prepare['coverImgUrl'] = 'http://zqh.xhcedu.com/uploadfile/2019/0103/20190103094227280.jpg';
		$lsit_prepare['playCount'] = '3824';
		$lsit_prepare['welcomewords'] = 'Welcome to The Falsy World';
		$lsit_prepare['userId'] = 'wx001';

		$creator = array();
		$creator['catLogo'] = "http://imgsrc.baidu.com/forum/w=580/sign=9a6e2794d0b44aed594ebeec831d876a/5c9f7701a18b87d63b098fc10d0828381e30fd2d.jpg";
		// $creator['avatarUrl'] = $wxVdata['avatar'];
		$creator['cur_catname'] = $curcatname;
		
		$tracks = array();
		$one = array();
		$filelist = $this->XCX->get_filelist_by_catid($catid);
		
		foreach($filelist as $num_key => $filedata){
			$one = array();
			$one['title'] = $filedata['title'];
			$quest_id_arr = explode(',',$filedata['quest_ids']);
			$one['total'] = sizeof($quest_id_arr);

			$rec_one = array();
			$rec_one = $this->XCX->get_one_wxrec_by_condition($wxvisitorid,$filedata['id']);
			$ans_only_arr = explode(',',$rec_one['answer_choice_only']);
			// $length_only = empty($rec_one['answer_choice_only'])?0:sizeof($ans_only_arr);
			$ans_only_arr = json_decode($rec_one['answer_choice_only']);
			$ans_more_arr = json_decode($rec_one['answer_choice_more']);
			// $one['current'] = $length_only+sizeof($ans_more_arr);
			$one['current'] = sizeof($ans_only_arr)+sizeof($ans_more_arr);
			$one['recid'] = $rec_one['id'];

			$tracks[] = $one;
		}
		// echo "<pre/>";var_dump($filelist);die();

		$lsit_prepare['tracks']=$tracks;

		$lsit_prepare['creator']=$creator;

		$course = array();
		$course['list']= $lsit_prepare;
		
		// $arr['data'] = $course;
		$arr = $course;
		echo json_encode($arr);
	}

	function getCategory(){
		$courses = array();
		$category = $this->XCX->get_catid_name_arr();
		foreach($category as $key=>$cate){
			$cour_lv_1 = array();
			$cour_lv_1['title'] = $cate['catname'];
			$category_sec = $this->XCX->get_catid_name_arr($cate['catid']);
			$item = array();
			foreach($category_sec as $key_sec=>$cate_sec){
				$item['item'][]['title'] = $cate_sec['catname'];
				$category_thi = $this->XCX->get_catid_name_arr($cate_sec['catid']);
				$stdCourse = array();
				foreach($category_thi as $key_thi=>$cate_thi){
					$stdC_arr = array();					
					$stdC_arr['title'] = $cate_thi['catname'];
					$stdC_arr['url'] = $cate_thi['catid'];
					
					$stdCourse[] = $stdC_arr;
				}
				$item['item'][]['stdCourse'] = $stdCourse;
			}
			$cour_lv_1['category'] = $item;
			$courses[] = $cour_lv_1;
		}
		$arr['data']=$courses;
		echo json_encode($arr);
		// echo "<pre/>";var_dump($courses);
	}

	function getQuestionID(){
		$get_arr = $_GET;
		$recid = remove_xss($get_arr['recid']);
		$recdata = $this->XCX->get_recdata_by_id($recid);
		// echo json_encode($get_arr);die();
		
		$arr = array();
		$data_arr = array();
		
		$fdata = $this->XCX->get_data_by_fileid($recdata['fileid']);
		$quest_arr = explode(',',$fdata['quest_ids']);
		for($i=0;$i<sizeof($quest_arr);$i++){
			$data_arr[$i]['question_id']=$quest_arr[$i];
			$data_arr[$i]['is_answer']=0;
		}

		$arr['status'] = '1';
		$arr['msg'] = 'okay';
		$arr['data'] = $data_arr;
		echo json_encode($arr);
	}

	function getQuestion(){
		$get_arr = $_GET;
		$quest_arr = array();
		$questionID_str = remove_xss($get_arr['questionID']);
		// $temp_arr = explode('[',$questionID_str);
		// $temp_arr = explode(']',$temp_arr[1]);
		$qid_str = $this->removeMiddleBracket($questionID_str);
		$quest_arr = explode(',',$qid_str);

		$arr = array();
		$data_arr = array();

		// $fdata = $this->XCX->get_data_by_fileid(6);
		// $quest_arr = explode(',',$fdata['quest_ids']);

		for($i=0;$i<sizeof($quest_arr);$i++){
			$answer_arr = array();
			$qdata = array();
			$qid = $quest_arr[$i];
			$qdata = $this->XCX->get_data_by_questid($qid);

			$data_arr[$i]['question_id'] = $qid;
			$data_arr[$i]['question_'] = $qdata['question_body'];
			$data_arr[$i]['question_parse'] = $qdata['question_analysis'];
			
			$qtype = 0;
			if($qdata['quest_type']=="choice_only"){
				$qtype = 1;
			}
			else if($qdata['quest_type']=="choice_more"){
				$qtype = 2;
			}
			$data_arr[$i]['option_type'] = $qtype;

			$answer_arr = explode(';',$qdata['question_answer']);
			$tem_key = 'a';
			foreach($answer_arr as $key=>$answer){
				$ans_content = array();
				if(strpos($answer,'.')){
					$ans_content = explode('.',$answer);
				}
				else if(strpos($answer,'．')){
					$ans_content = explode('．',$answer);
				}
				
				$data_arr[$i]['option_'.$tem_key] = $ans_content[1];
				$tem_key++;
			}

			$answer_ = 0;
			$true_ans_arr = explode('.',$qdata['true_answer']);
			foreach($true_ans_arr as $tkey=>$tans){
				$answer_ += $this->Alpha_Digital[$tans];
			}
			$data_arr[$i]['answer_'] = $answer_;

			$data_arr[$i]['media_type']=0;
			$data_arr[$i]['media_content']=0;
			$data_arr[$i]['media_width']=0;
			$data_arr[$i]['media_height']=0;
		}
		
		$arr['data'] = $data_arr;
		// var_dump($data_arr);
		echo json_encode($arr);
	}

	function putAnswer(){
		$get_arr = $_GET;
		$recid = remove_xss($get_arr['recid']);
		$qid = remove_xss($get_arr['curqid']);
		$answer_arr = remove_xss($get_arr['answer_arr']);
		$answerStr = $this->removeMiddleBracket($answer_arr);

		$return = $this->XCX->update_wxrec_answer_by_id($recid,$qid,$answerStr);
		echo json_encode($return);
	}

	public function sendCode(){
		$APPID = 'wxbc5aad64b7af411c';
		$AppSecret = '18cc08531ca7806865c60464d7a0718c';
		$get_arr = $_GET;
		// echo json_encode($_GET);
		// die;
		$code = remove_xss($get_arr['code']);
		$url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$APPID.'&secret='.$AppSecret.'&js_code='.$code.'&grant_type=authorization_code';
		$arr = $this->vegt($url);
		// echo $arr;die;
		$arr = json_decode($arr,true);

		$get_arr['rawData'] = str_replace('\"','"',remove_xss($get_arr['rawData']));
		$rawData = explode('"',$get_arr['rawData']);
		$get_arr['rawData'] = implode('"',$rawData);
		$userinfo = json_decode($get_arr['rawData'],true);
		
		// echo $userinfo['nickName'];
		// die;

		$save = array();
		$save['openid'] = $arr['openid'];
		$save['nickname'] = $userinfo['nickName'];
		$save['avatar'] = $userinfo['avatarUrl'];
		$save['gender'] = $userinfo['gender'];
		$save['city'] = $userinfo['city'];
		$save['time'] = time();

		$wxvid = $this->XCX->wxVisitor($save);

		echo $wxvid;

 		// 数字签名校验
		// $signature = $get_arr['signature'];
		// $signature2 = sha1($get_arr['rawData'].$session_key);
		// if($signature != $signature2){
		// 	$errData = array('msg'=>'数字签名失败','sign'=>$signature,'sign2'=>$signature2);
		// 	echo json_encode($errData);
		// 	die;
		// }
		// else{
		// 	echo 'interesting';
		// }
	}

	public function vegt($url){
		$info = curl_init();
		curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($info,CURLOPT_HEADER,0);
		curl_setopt($info,CURLOPT_NOBODY,0);
		curl_setopt($info,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($info,CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($info,CURLOPT_URL,$url);
		$output= curl_exec($info);
		curl_close($info);
		return $output;
	}

	function removeMiddleBracket($MDstr){
		$temp_arr = array();
		$temp_arr = explode('[',$MDstr);
		$temp_arr = explode(']',$temp_arr[1]);
		return $temp_arr[0];
	}
}
?>
