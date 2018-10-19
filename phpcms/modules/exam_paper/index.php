<?php 
defined('IN_PHPCMS') or exit('No permission resources.');
class index {
    private $top_catid = 37;
    private $choice_only = array('type'=>'choice_only','num'=>2);
    private $choice_more = array('type'=>'choice_more','num'=>2);

    function __construct() {
        // 加载category的数据模型;查询相关分类
        $this->category_db = pc_base::load_model('category_model');
        // 加载exam_data模型;获取试题
        $this->exam_db = pc_base::load_model('exam_model');
        $this->exam_data_db = pc_base::load_model('exam_data_model');
        $this->exam_paper_db = pc_base::load_model('exam_paper_model');
        $this->exam_answer_db = pc_base::load_model('exam_answer_model');

        // 取得当前登录会员的会员名(username)和会员ID(userid)
        // $this->_username = param::get_cookie('_username');
        // $this->_userid = param::get_cookie('_userid');
        
        //定义站点ID常量，选择模版使用
        $siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : get_siteid();
        define("SITEID", $siteid);

          //读取配置
        $setting = new_html_special_chars(getcache('exam_paper', 'commons'));
        $this->set = $setting[SITEID];
    }

    private function _session_start(){
        /*V9验证码的数值是通过SESSION传递，故在这段代码中，首先加载配置文件，                 
        取出当前系统配置中SESSION的存储方式。然后根据SESSION的存储方式，来加载对应的系统类库*/
        $session_storage = 'session_'.pc_base::load_config('system','session_storage');
        pc_base::load_sys_class($session_storage);
    }

    public function init() {
        //设置分页条数
        $pagesize = $this->set['pagesize'];
        
        $category_one = $this->get_catid_name_arr($this->top_catid);
        // $where = array('parentid'=>$this->top_catid);
        // $data = 'catid,catname';
        // $category_one = $this->category_db->select($where,$data);

        // $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        // $infos = $this->category_db->listinfo($where, 'gid DESC', $page, $pagesize);
        // $infos = new_html_special_chars($infos);
        
        // 加载系统form类，用于前台模块文件中生成验证码
        pc_base::load_sys_class('form', '', 0);
        
        // 加载前台模板
        include template('exam_paper', 'index');
    }

    //ajax_request
    public function make_one_exam_paper(){
        $this->unserialize_ajax_form_serializeArray();
        $this->make_exam_paper_check();
        $paper = array();
        $paper = $this->prepare_paper_data();
        $result = array();
        $result = $this->save_paper_data($paper);
        echo json_encode($result);
    }

    public function show_one_paper(){
        $paper_id = $_GET['paper_id'];
        $paper_data = $this->get_paper_by_pid($paper_id);
        $quest_choice_only = $this->get_quest_by_ids($paper_data['quest_choice_only']);
        $quest_choice_more = $this->get_quest_by_ids($paper_data['quest_choice_more']);
        include template('exam_paper', 'show');
    }

    public function put_answer(){
        // echo $_POST;die;
        // echo json_encode($_POST);die;
        // $this->unserialize_ajax_form_serializeArray();
        // echo json_encode($_POST);
        $paper_id = $_POST['paper_id'];
        $paper_data = $this->exam_paper_db->get_one(array('id'=>$paper_id));
        $answer = array();
        $answer['paper_id'] = $paper_id;
        $answer['title'] = $paper_data['title'];
        $answer['name'] = $paper_data['name'];
        $answer['mobile'] = $paper_data['mobile'];
        $answer['answer_choice_only'] = implode(',',$_POST['only']);
        $answer['answer_choice_more'] = json_encode($_POST['more']);
        $answer['addtime'] = SYS_TIME;
        $answer['siteid'] = SITEID;
        $insert_id = $this->exam_answer_db->insert($answer,true);
        $result['msg'] = 'answer_saved';
        echo json_encode($result);
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

    private function get_catid_name_arr($parentid){
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

    private function unserialize_ajax_form_serializeArray(){
        foreach($_POST['data'] as $key=>$val)
        {
            $_POST[$val['name']] = $val['value'];
        }
    }

    private function make_exam_paper_check(){
        if (!( isset($_POST['name']) && trim($_POST['name']) 
            && isset($_POST['mobile']) && trim($_POST['mobile']) 
            && isset($_POST['cat_level_1']) && trim($_POST['cat_level_1']) 
            && isset($_POST['cat_level_2']) && trim($_POST['cat_level_2']) ) )
        {
            $result['msg'] = 'data_error';
            echo json_encode($result);
            exit;
        }
    }

    private function make_paper_title($arrparentid){
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

    private function get_quest_by_type($quest_setting_arr){
        $quest_str = '';
        $where = array('quest_type'=>$quest_setting_arr['type']);
        $data = 'id';
        $limit = $quest_setting_arr['num'];
        $order = 'rand()';
        $quest_arr = $this->exam_data_db->select($where,$data,$limit,$order);
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

    private function prepare_paper_data(){
        $paper = array();

        $paper['arrparentid'] = $this->top_catid.','.$_POST['cat_level_1'].','.$_POST['cat_level_2'];
        $paper['title'] = $this->make_paper_title($paper['arrparentid']);
        $paper['name'] = $_POST['name'];
        $paper['mobile'] = $_POST['mobile'];
        $paper['siteid'] = SITEID;
        $paper['addtime'] = SYS_TIME;
        $paper['quest_choice_only'] = $this->get_quest_by_type($this->choice_only);
        $paper['quest_choice_more'] = $this->get_quest_by_type($this->choice_more);

        return $paper;
    }

    private function save_paper_data($paper){
        $result = array();
        $this->exam_paper_db->insert($paper);
        $current_id = $this->exam_paper_db->insert_id();
        $msg = 'already_paper';
        $result['id'] = $current_id;
        $result['msg'] = $msg;
        return $result;
    }

    private function get_paper_by_pid($paper_id){
        $where = array('id' => $paper_id);
        $paper_data = $this->exam_paper_db->select($where);
        return $paper_data[0];
    }

    private function get_quest_by_ids($ids){
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

}
?>