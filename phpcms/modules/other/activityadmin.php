<?php
defined('IN_PHPCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
//定义在单独操作内容的时候，同时更新相关栏目页面
define('RELATION_HTML',true);

pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);
pc_base::load_app_func('util');
pc_base::load_sys_class('format','',0);

class activityadmin extends admin {
	private $db,$priv_db;
	public $siteid;
	public function __construct() {
		//parent::__construct();
		$this->db = pc_base::load_model('activity_tiaoji_model');
		$this->siteid = $this->get_siteid();
	}

	public function init() {

		$where = "1 = 1";
		if($_GET['search'] == 1)
		{
			if(isset($_GET['start_time']) && $_GET['start_time']) {
				// $start_time = strtotime($_GET['start_time']);
				$start_time = $_GET['start_time'];
				$where .= " AND `addtime` > '$start_time'";
			}
			if(isset($_GET['end_time']) && $_GET['end_time']) {
				// $end_time = strtotime($_GET['end_time']);
				$end_time = $_GET['end_time'];
				$where .= " AND `addtime` <= '$end_time'";
			}
			if($_GET['city'] != '')
			{
				$city = trim($_GET['city']);
				$where .= " AND `city` = '$city'";
			}
			if($_GET['test'] != '')
			{
				$city = trim($_GET['test']);
				$where .= " AND `test` = '$city'";
			}
			if($_GET['name'] != '')
			{
				$name = trim($_GET['name']);
				$where .=" AND `name` = '$name'";
			}
			if($_GET['mobile'] != '')
			{
				$mobile = trim($_GET['mobile']);
				$where .= " AND `mobile` = '$mobile'";
			}
			if($_GET['qq'] != '')
			{
				$qq = trim($_GET['qq']);
				$where .= " AND `qq` = '$qq'";
			}
		}

		$datas = $this->db->listinfo($where,'id desc',$_GET['page']);
		//	var_dump($datas);
		$pages = $this->db->pages;
		$template = $MODEL['admin_list_template'] ? $MODEL['admin_list_template'] : 'activity_tiaoji_list';
		// var_dump($pages);die;
		include $this->admin_tpl($template);
	}

	public function excel_export() {
		$where = "1 = 1";
		if($_GET['search'] == 1)
		{
			if(isset($_GET['start_time_down']) && $_GET['start_time_down']) {
				$start_time = strtotime($_GET['start_time_down']);
				$where .= " AND `addtime` > '$start_time'";
			}
			if(isset($_GET['end_time_down']) && $_GET['end_time_down']) {
				$end_time = strtotime($_GET['end_time_down']);
				$where .= " AND `addtime` <= '$end_time'";
			}

			if($_GET['city'] != '')
			{
				$city = trim($_GET['city']);
				$where .= " AND `city` = '$city'";
			}

			if($_GET['test'] != '')
			{
				$city = trim($_GET['test']);
				$where .= " AND `test` = '$city'";
			}

			if($_GET['name'] != '')
			{
				$name = trim($_GET['name']);
				$where .=" AND `name` = '$name'";
			}

			if($_GET['mobile'] != '')
			{
				$mobile = trim($_GET['mobile']);
				$where .= " AND `mobile` = '$mobile'";
			}
			if($_GET['qq'] != '')
			{
				$qq = trim($_GET['qq']);
				$where .= " AND `qq` = '$qq'";
			}
		}

		$datas = $this->db->select($where,'*','','id desc');

		if(empty($datas))
		{
			showmessage('请重新导出客户资料',HTTP_REFERER);
		}
		$file_name = iconv('utf-8', 'gbk', '客户资料').date('Y-m-d');
		header("Content-type:application/vnd.ms-excel"); 
		header("Content-Disposition:filename=".$file_name.".xls");
		echo iconv('utf-8','gbk',"序号\t姓名\t手机\t报考项目\t添加时间\r"); 
		$xm = array('kindergarten'=>'幼儿','primary'=>'小学','middle'=>'中学');
		foreach ($datas as $key=>$val)
		{

			echo $val['id']."\t";
			
			echo iconv('utf-8','gbk',$val['name'])."\t";
			echo iconv('utf-8','gbk',$val['mobile'])."\t";
			echo iconv('utf-8','gbk',$xm[$val['bkxm']])."\t";

			echo iconv('utf-8','gbk',date('Y-m-d H:i:s',$val['addtime']))."\t\r";

		}
		exit;
	}

}

?>