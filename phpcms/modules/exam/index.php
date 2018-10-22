<?php 
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('exam_index', 'exam', 0);
pc_base::load_app_func('global','exam');

class index {
    private $top_catid;
    function __construct() {

        $this->Pre_Index = new exam_index();
        
        $this->_session_start();

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

    public function init() {
        $allow_guest = $this->set['allow_guest'];
        if($allow_guest != 1){
            echo '此功能暂不支持游客访问。';die;
        }

        //设置分页条数
        // $pagesize = $this->set['pagesize'];
        
        $category_one = $this->get_catid_name_arr($this->Pre_Index->top_catid);
        
        // 加载系统form类，用于前台模块文件中生成验证码
        pc_base::load_sys_class('form', '', 0);
        
        // 加载前台模板
        include template('exam_paper', 'index');
    }

    //填写手机号姓名之后第一轮跳转
    public function collect_exam_visitor_info(){
        $this->Pre_Index->visitor_info_check();
        $super_info = $_SESSION['super_info'];
        $list_sec = $this->get_catid_name_arr($super_info['cat_level_1']);
        include template('exam_paper', 'welcome');
    }

    public function choose_sec_cat(){
        $this->Pre_Index->visitor_paper_check();
        $super_info = $_SESSION['super_info'];
        $list_sec = $this->get_catid_name_arr($super_info['cat_level_1']);

        if($_POST['sec_cat'] && !empty($_POST['sec_cat'])){
            $sec_cat = $_POST['sec_cat'];
        }
        else{
            $item_temp = $list_sec;
            $list_sec_one = array_shift($item_temp);
            $sec_cat = $list_sec_one['catid'];
        }

        $list_thi = $this->get_catid_name_arr($sec_cat);
        
        if($_POST['thi_cat']){
            $thi_cat = $_POST['thi_cat'];
        }
        else{
            $temp_item = $list_thi;
            $list_third_one = array_shift($temp_item);
            $thi_cat = $list_third_one['catid'];
        }

        $where_third = array(
            'catid' => $thi_cat,
            'isdelete' => 0,
        );
        $file_list = $this->Pre_Index->exam_file_db->listinfo($where_third, 'id DESC', $page, '15');
        $file_list = $this->Pre_Index->make_cattitle($file_list);
        include template('exam_paper', 'sec_cat_paper_list');

    }

    public function make_one_paper_by_fileid(){
        $fileid = $_POST['fileid'];
        $paper = $this->Pre_Index->prepare_paper_data_byfileid($fileid);
        $result = array();
        $result = $this->Pre_Index->save_paper_data($paper);
        header('Location:/index.php?m=exam&c=index&a=show_one_paper&paper_id='.$result['id']);
    }

    public function make_one_exam_paper(){
        $this->make_exam_paper_check();
        $paper = array();
        $paper = $this->Pre_Index->prepare_paper_data();
        $result = array();
        $result = $this->Pre_Index->save_paper_data($paper);
        $_GET['paper_id'] = $result['id'];
        $this->show_one_paper();
    }

    public function show_one_paper(){
        $this->Pre_Index->visitor_paper_check();
        $paper_id = $_GET['paper_id'];
        $paper_data = $this->Pre_Index->get_paper_by_pid($paper_id);
        $quest_choice_only = $this->Pre_Index->get_quest_by_ids($paper_data['quest_choice_only']);
        $quest_choice_more = $this->Pre_Index->get_quest_by_ids($paper_data['quest_choice_more']);
        include template('exam_paper', 'show');
    }

    public function put_answer(){
        $paper_id = $_POST['paper_id'];
        $paper_data = $this->Pre_Index->get_paper_data_byid($paper_id);
        $result = $this->Pre_Index->save_answer_data($paper_data);
        
        header('Location:/index.php?m=exam&c=index&a=show_jiexi_result&answer_id='.$result['answer_id']);

        //服务器用不上这个函数
        // showmessage('答案已经提交！','/index.php?m=exam&c=index&a=choose_sec_cat');
    }

    public function show_jiexi_result(){
        $this->Pre_Index->visitor_paper_check();
        $answer_id = $_GET['answer_id'];

        $answer_data = $this->Pre_Index->get_answer_by_id($answer_id);
        $answer_choice_only = $this->Pre_Index->parse_choice_only_answer($answer_data['answer_choice_only']);
        $answer_choice_more = $this->Pre_Index->parse_choice_more_answer($answer_data['answer_choice_more']);
        
        $paper_data = $this->Pre_Index->get_paper_by_pid($answer_data['paper_id']);
        $quest_choice_only = $this->Pre_Index->get_quest_by_ids($paper_data['quest_choice_only']);
        $quest_choice_more = $this->Pre_Index->get_quest_by_ids($paper_data['quest_choice_more']);

        $cankao_choice_only = $this->Pre_Index->get_cankao_answer_by_ids($paper_data['quest_choice_only']);
        $cankao_choice_more = $this->Pre_Index->get_cankao_answer_by_ids($paper_data['quest_choice_more']);

        $analysis_key_choice_only = $this->Pre_Index->get_analysis_key_by_ids($paper_data['quest_choice_only']);
        $analysis_key_choice_more = $this->Pre_Index->get_analysis_key_by_ids($paper_data['quest_choice_more']);

        $paiming = $this->Pre_Index->get_paiming($answer_data);

        include template('exam_paper', 'show_jiexi_result');
    }

    public function show_message(){
        $msg = $_GET['msg'];
        $this->Pre_Index->show_my_message($msg);
    }

    public function ajax_select_admin(){
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
        $catid_name_arr = $this->Pre_Index->category_db->select($where,$data);
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

}
?>