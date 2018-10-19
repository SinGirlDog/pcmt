<?php 
die;//这个功能影响较大、不可肆意执行;
defined('IN_PHPCMS') or exit('No permission resources.');
class copy_article {
	private $mapping = array(//$key_source_catid=>$val_aim_catid
		'723'=>'8132',
	);
	private $limit = 5;//each category copy five items
    private $order = 'id desc';

	function __construct() {
        // 加载NEWs的数据模型;获取数据
		$this->bj_news_db = pc_base::load_model('bj_news_model');
		$this->bj_news_data_db = pc_base::load_model('bj_news_data_model');

		$this->sq_news_db = pc_base::load_model('sq_news_model');
		$this->sq_news_data_db = pc_base::load_model('sq_news_data_model');

        //定义站点ID常量，选择模版使用
		$siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : get_siteid();
		define("SITEID", $siteid);

          //读取配置
		$setting = new_html_special_chars(getcache('clone_cate', 'commons'));
		$this->set = $setting[SITEID];
	}

	private function _session_start(){
        /*V9验证码的数值是通过SESSION传递，故在这段代码中，首先加载配置文件，                 
        取出当前系统配置中SESSION的存储方式。然后根据SESSION的存储方式，来加载对应的系统类库*/
        $session_storage = 'session_'.pc_base::load_config('system','session_storage');
        pc_base::load_sys_class($session_storage);
    }

    public function init() {
    	$this->_session_start();
    	if($_SESSION['COPY_ARTICLES'] == 1)
    	{
    		echo 'already_done';
    		die;
    	}

    	foreach($this->mapping as $from_catid => $aim_catid)
    	{
    		$news = $this->get_news($from_catid);
    		foreach($news as $news_k => $news_v)
    		{
    			$news_data = $this->get_news_data($news_v['id']);
    			$news_id = $this->save_one_news($aim_catid,$news_v);
    			$data_id = $this->save_one_news_data($news_id,$news_data);
    		}
    	}

    	$_SESSION['COPY_ARTICLES'] = 1;
    	echo "copy_complete";
		// include template('clone_cate', 'index');
    }

    private function get_news($from_catid){
    	$catid = $from_catid;
    	$where = array('catid'=>$catid);
    	$news_arr = $this->bj_news_db->select($where,'*',$this->limit,$this->order);
    	return $news_arr;
    }
    private function get_news_data($news_id){
    	$where = array('id'=>$news_id);
    	$news_data_one = $this->bj_news_data_db->get_one($where);
    	return $news_data_one;
    }

    private function save_one_news($aim_catid,$news_v){
    	$data = $news_v;
    	array_shift($data);
    	$data['catid'] = $aim_catid;
    	$news_id = $this->sq_news_db->insert($data,true);
    	return $news_id;
    }

    private function save_one_news_data($news_id,$news_data){
    	$data = $news_data;
    	$data['id'] = $news_id;
    	$news_data_id = $this->sq_news_data_db->insert($data,true);
    	return $news_data_id;
    }

}
?>