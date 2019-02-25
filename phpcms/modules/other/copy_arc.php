<?php 
// die;//这个功能影响较大、不可肆意执行;
defined('IN_PHPCMS') or exit('No permission resources.');
class copy_arc {
	private $mapping = array(//$key_source_catid=>$val_aim_catid
		// '247'=>'78',
        '248'=>'79',
        '249'=>'80',
        '250'=>'81',
        '158'=>'82',
        '159'=>'83',
        '160'=>'84',
        '161'=>'85',
        '162'=>'86',
        '163'=>'87',
        '164'=>'88',
        '165'=>'89',
        '166'=>'90',
        '167'=>'91',
        '168'=>'92',
        '169'=>'93',
        '170'=>'94',
        '171'=>'95',
        '172'=>'96',
        '173'=>'97',
        '174'=>'98',
        '175'=>'99',
        '176'=>'100',
        '177'=>'101',
        '178'=>'102',
        '179'=>'103',
        '180'=>'104',
        '181'=>'105',
        '187'=>'106',

	);
	private $limit = 10;//each category copy five items
    private $order = 'id desc';

    function __construct() {
        // 加载NEWs的数据模型;获取数据
      $this->dedearchives = pc_base::load_model('dedearchives_model');
      $this->dedeaddonarticle = pc_base::load_model('dedeaddonarticle_model');

		$this->news_db = pc_base::load_model('news_model');
		$this->news_data_db = pc_base::load_model('news_data_model');

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
    		$news = $this->get_archives($from_catid);
    		foreach($news as $news_k => $news_v)
    		{
    			$news_data = $this->get_addonarticle_data($news_v['id']);
                // echo "<pre/>",$news_v['id'];var_dump($news_v);
                // var_dump($news_data);die();

                $new_newsv = array();
                $new_newsv['typeid'] = '0';
                $new_newsv['title'] = $news_v['title'];
                $new_newsv['thumb'] = '';
                $new_newsv['keywords'] = $news_v['keywords'];
                $new_newsv['description'] = $news_v['description'];
                $new_newsv['status'] = '99';
                $new_newsv['sysadd'] = '1';
                $new_newsv['islink'] = '0';
                $new_newsv['username'] = 'phpcms';
                $new_newsv['inputtime'] = SYS_TIME;
                $new_newsv['updatetime'] = SYS_TIME;
                $news_id = $this->save_one_news($aim_catid,$new_newsv);

                $new_newsd = array();
                $new_newsd['content'] = $news_data['body'];
                $new_newsd['readpoint'] = 0;
                $new_newsd['groupids_view'] = '';
                $new_newsd['paginationtype'] = 0;
                $new_newsd['maxcharperpage'] = 10000;
                $new_newsd['template'] = '';
                $new_newsd['paytype'] = 0;
                $new_newsd['relation'] = '';
                $new_newsd['voteid'] = 0;
                $new_newsd['allow_comment'] = 1;
                $new_newsd['copyfrom'] = $news_v['source'];

                $data_id = $this->save_one_news_data($news_id,$new_newsd);
            }
        }

        $_SESSION['COPY_ARTICLES'] = 1;
        echo "copy_complete";
		// include template('clone_cate', 'index');
    }

    private function get_archives($from_catid){
    	$catid = $from_catid;
    	$where = array('typeid'=>$catid);
    	$news_arr = $this->dedearchives->select($where,'*',$this->limit,$this->order);
    	return $news_arr;
    }
    private function get_addonarticle_data($news_id){
    	$where = array('aid'=>$news_id);
    	$news_data_one = $this->dedeaddonarticle->get_one($where);
    	return $news_data_one;
    }

    private function save_one_news($aim_catid,$news_v){
    	$data = $news_v;
    	array_shift($data);
    	$data['catid'] = $aim_catid;
    	$news_id = $this->news_db->insert($data,true);
    	return $news_id;
    }

    private function save_one_news_data($news_id,$news_data){
    	$data = $news_data;
    	$data['id'] = $news_id;
    	$news_data_id = $this->news_data_db->insert($data,true);
    	return $news_data_id;
    }

}
?>