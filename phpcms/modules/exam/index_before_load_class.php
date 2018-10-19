<?php 
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('exam_index', 'exam', 0);
pc_base::load_app_func('global','exam');

class index {
   // var $controller;
    private $top_catid;

    private $sec_type = array(
        'lnzt' => '历年真题',
        'mryl' => '每日一练',
        'mkcsj' => '模考测试卷',
    );
    // private $choice_only = array('type'=>'choice_only','num'=>2,'fenshu'=>1);
    // private $choice_more = array('type'=>'choice_more','num'=>2);

    function __construct() {
       // $this->controller = new exam_index();
        // 加载category的数据模型;查询相关分类
        $this->category_db = pc_base::load_model('category_model');
        // 加载exam_data模型;获取试题
        $this->exam_db = pc_base::load_model('exam_model');
        $this->exam_data_db = pc_base::load_model('exam_data_model');
        $this->exam_paper_db = pc_base::load_model('exam_paper_model');
        $this->exam_file_db = pc_base::load_model('exam_file_model');
        $this->exam_answer_db = pc_base::load_model('exam_answer_model');
        $this->exam_visitor_db = pc_base::load_model('exam_visitor_model');

        $this->top_catid = $this->get_top_catid();
        $this->paper_setting = $this->get_paper_setting();
        
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
        // var_export($this->set);die;
        $allow_guest = $this->set['allow_guest'];
        if($allow_guest != 1){
            echo '此功能暂不支持游客访问。';die;
        }

        //设置分页条数
        // $pagesize = $this->set['pagesize'];
        
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
    public function make_one_exam_paper_ajax(){
        $this->unserialize_ajax_form_serializeArray();
        $this->make_exam_paper_check();
        $paper = array();
        $paper = $this->prepare_paper_data();
        $result = array();
        $result = $this->save_paper_data($paper);
        echo json_encode($result);
    }

    public function collect_exam_visitor_info(){
        $this->visitor_info_check();

        $super_info = $_SESSION['super_info'];
        $list_sec = $this->get_catid_name_arr($super_info['cat_level_1']);

        include template('exam_paper', 'welcome');

    }

    public function choose_sec_cat(){
        // var_dump($_POST);die;
        $where = array(
            'parentid' => $_POST['cat_level_1'],
            'catname' => $this->sec_type[$_POST['sec_cat']],
        );
        // var_dump($_POST['sec_cat']);die;
        $sec_catid = $this->category_db->get_one($where,'catid');

        $third_catname = $this->category_db->get_one(array('catid'=>$_POST['cat_level_2']),'catname');
        $where_again = array(
            'parentid' => $sec_catid['catid'],
            'catname' => $third_catname['catname'],
        );
        $third_catid = $this->category_db->get_one($where_again,'catid');

        $where_third = array(
            'catid' => $third_catid['catid'],
        );
        $file_list = $this->exam_file_db->listinfo($where_third, 'id DESC', $page, '15');
        $file_list = $this->make_cattitle($file_list);
        include template('exam_paper', 'sec_cat_paper_list');

    }

    public function make_one_paper_by_fileid(){
        $fileid = $_POST['fileid'];
        $paper = $this->prepare_paper_data_byfileid($fileid);
        $result = array();
        $result = $this->save_paper_data($paper);
        $_GET['paper_id'] = $result['id'];
        $this->show_one_paper();
        // var_dump($_POST);die;
    }

    public function make_one_exam_paper(){
        $this->make_exam_paper_check();
        $paper = array();
        $paper = $this->prepare_paper_data();
        $result = array();
        $result = $this->save_paper_data($paper);
        $_GET['paper_id'] = $result['id'];
        $this->show_one_paper();
    }

    public function show_one_paper(){
        // echo $_GET['paper_id'];
        $paper_id = $_GET['paper_id'];
        $paper_data = $this->get_paper_by_pid($paper_id);
        $quest_choice_only = $this->get_quest_by_ids($paper_data['quest_choice_only']);
        $quest_choice_more = $this->get_quest_by_ids($paper_data['quest_choice_more']);
        include template('exam_paper', 'show');
    }

    public function put_answer_ajax(){
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

    public function put_answer(){
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
        showmessage('答案已经提交！','/index.php?m=exam');
        $this->init();
    }

    public function ajax_select(){
        $category_any = array();
        if($_GET['param_id'])
        {
            $par_id = $_GET['param_id'];
            $category_any = $this->get_catid_name_arr($par_id);
            $category_sec = array_shift($category_any);
            // foreach($category_sec as $sec_key => $sec_val){
            $category_thi = $this->get_catid_name_arr($category_sec['catid']);
            // }
        }
        $ResultHtml = $this->make_select_options_html($category_thi);
        echo $ResultHtml;
    }

    public function ajax_select_admin(){
        $category_any = array();
        if($_GET['param_id'])
        {
            $par_id = $_GET['param_id'];
            $category_any = $this->get_catid_name_arr($par_id);
            // }
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

    private function visitor_info_check(){
        if (!( isset($_POST['name']) && trim($_POST['name']) 
            && isset($_POST['mobile']) && trim($_POST['mobile']) 
            && isset($_POST['cat_level_1']) && trim($_POST['cat_level_1']) 
            && isset($_POST['cat_level_2']) && trim($_POST['cat_level_2']) ) )
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
                // echo $visit_id;die;
            }
            else{
                $data = array('visittime'=>SYS_TIME);
                $visit_up = $this->exam_visitor_db->update($data,$where);
                // echo $visit_up;die;
            }
            $_SESSION['exam_visit_'.$_POST['mobile']] = 1;
            $_SESSION['super_info'] = $_POST;
        }
        
        // var_dump($visit_one);die;
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

    private function prepare_paper_data_byfileid($fileid){
        $paper = array();
        $super_info = $_SESSION['super_info'];
        $file = $this->exam_file_db->get_one(array('id'=>$fileid));
        // var_dump($file);die;
        if(!empty($file)){
            $catid = $file['catid'];
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

    private function save_paper_data($paper){
        $result = array();
        $this->exam_paper_db->insert($paper);
        $current_id = $this->exam_paper_db->insert_id();
        $msg = 'already_paper';
        $result['id'] = $current_id;
        $result['msg'] = $msg;
        return $result;
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

    public function make_cattitle($infos){
        foreach($infos as $key => &$val){
            $val['cattitle'] = $this->get_catnames_by_catids($val['arrcatid']);
        }
        return $infos;
    }

}
?>