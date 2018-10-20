<?php
defined('IN_PHPCMS') or exit('No permission resources. - exam_answer.php');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_app_class('exam_index', 'exam', 0);
pc_base::load_sys_class('form', '', 0);


class exam_answer extends admin {
   
    public function __construct() {
        parent::__construct();//继承父类构造函数
        $setting = new_html_special_chars(getcache('exam', 'commons'));//读取考试配置缓存文件
        $this->set = $setting[$this->get_siteid()];
        $this->exam_data_db = pc_base::load_model('exam_data_model');
        $this->exam_paper_db = pc_base::load_model('exam_paper_model');//加载考试数据模型
        $this->exam_answer_db = pc_base::load_model('exam_answer_model');//加载留言本数据模型

        //预览调用前台控制器
        // require_once PC_PATH.'modules\exam\index.php';
        // $this->Pre_Index = new index();
        $this->Pre_Index = new exam_index();

    }
    
    public function init() {
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : date('Y-m-d', SYS_TIME-date('t', SYS_TIME)*86400);
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);
        
        $where = array('siteid'=>$this->get_siteid(),'isdelete'=>0);
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        $infos = $this->exam_answer_db->listinfo($where, 'id DESC', $page, '15');
        $pages = $this->exam_answer_db->pages;
        
        /* 加载后台管理模版 exam_list.tpl.php。 
         * 此文件位于phpcms/modules/模块/templates/  
         * 由此即可明白，其它后台管理模版亦位于此目录*/
        include $this->admin_tpl('exam_answer_list');
    }

    public function deal_answer() {
        $mark = $_GET['mark'] ? $_GET['mark'] : 'preview';
        $answer_data = $this->Pre_Index->get_answer_by_id($_GET['id']);
        $answer_choice_only = $this->Pre_Index->parse_choice_only_answer($answer_data['answer_choice_only']);
        $answer_choice_more = $this->Pre_Index->parse_choice_more_answer($answer_data['answer_choice_more']);
        
        $paper_data = $this->Pre_Index->get_paper_by_pid($answer_data['paper_id']);
        $quest_choice_only = $this->Pre_Index->get_quest_by_ids($paper_data['quest_choice_only']);
        $quest_choice_more = $this->Pre_Index->get_quest_by_ids($paper_data['quest_choice_more']);

        if($mark == 'piyue' || $mark == 'jiexi'){
            $cankao_choice_only = $this->Pre_Index->get_cankao_answer_by_ids($paper_data['quest_choice_only']);
            $cankao_choice_more = $this->Pre_Index->get_cankao_answer_by_ids($paper_data['quest_choice_more']);

            $fenshu_only = $this->Pre_Index->correct_fenshu_only($answer_choice_only,$cankao_choice_only);
            $fenshu_more = $this->Pre_Index->correct_fenshu_more($answer_choice_more,$cankao_choice_more);

            if($mark == 'jiexi'){
                $analysis_key_choice_only = $this->Pre_Index->get_analysis_key_by_ids($paper_data['quest_choice_only']);
                $analysis_key_choice_more = $this->Pre_Index->get_analysis_key_by_ids($paper_data['quest_choice_more']);
            }
        }

        include $this->admin_tpl($mark.'_answer');
    }

    /**
    * 删除答题卡(伪删除,更新isdelete标记)
    * @param    intval    $id    试卷ID,可递归删除
    */
    public function delete_answer() {
        if((!isset($_GET['id']) || empty($_GET['id'])) && (!isset($_POST['id']) || empty($_POST['id']))) {
            showmessage(L('未选中'), HTTP_REFERER);
        }
        if(is_array($_POST['id'])){
            foreach($_POST['id'] as $id_arr) {
                $id_arr = intval($id_arr);
                $this->exam_answer_db->update(array('isdelete'=>1),array('id'=>$id_arr)); 
            }
            showmessage(L('删除成功'),HTTP_REFERER);
        }else{
            $id = intval($_GET['id']);
            if($id < 1) return false;
            $result = $this->exam_answer_db->update(array('isdelete'=>1),array('id'=>$id));
            if($result){
                showmessage(L('删除成功'),HTTP_REFERER);
            }else {
                showmessage(L("删除失败"),HTTP_REFERER);
            }
        }
    }

    public function save_fenshu(){
        if((!isset($_GET['id']) || empty($_GET['id'])) && (!isset($_POST['id']) || empty($_POST['id']))) {
            showmessage(L('未选中'), HTTP_REFERER);
        }
        if(is_array($_POST['id'])){
            foreach($_POST['id'] as $id_arr) {
                $id_arr = intval($id_arr);
                $fenshu = $this->get_fenshu_by_answerid($id_arr);
                $result = $this->exam_answer_db->update($fenshu,array('id'=>$id_arr));
            }
            showmessage(L('核算成功'),HTTP_REFERER);
        }else{
            $id = intval($_GET['id']);
            if($id < 1) return false;
            $fenshu = $this->get_fenshu_by_answerid($id);
            $result = $this->exam_answer_db->update($fenshu,array('id'=>$id));
            if($result){
                showmessage(L('核算成功'),HTTP_REFERER);
            }else {
                showmessage(L("核算失败"),HTTP_REFERER);
            }
        }
    }

    /**
     * 答题卡搜索
     */
    function search_answer() {

        //搜索框
        // $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        // $type = isset($_GET['type']) ? $_GET['type'] : '';
        // $groupid = isset($_GET['groupid']) ? $_GET['groupid'] : '';
        // $modelid = isset($_GET['modelid']) ? $_GET['modelid'] : '';

        //答题卡信息
        $name = isset($_GET['name']) ? $_GET['name'] : '';
        $mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';

        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);


        if (isset($_GET['search'])) {

            //默认选取一个月内的答题卡，防止答题卡量过大给数据造成灾难
            $where_start_time = strtotime($start_time) ? strtotime($start_time) : 0;
            $where_end_time = strtotime($end_time) + 86400;
            //开始时间大于结束时间，置换变量
            if($where_start_time > $where_end_time) {
                $tmp = $where_start_time;
                $where_start_time = $where_end_time;
                $where_end_time = $tmp;
                $tmptime = $start_time;
                
                $start_time = $end_time;
                $end_time = $tmptime;
                unset($tmp, $tmptime);
            }
            
            
            $where = '';
            
            //如果是超级管理员角色，显示所有答题卡，否则显示当前站点答题卡
            if($_SESSION['roleid'] == 1) {
                if(!empty($siteid)) {
                    $where .= "`siteid` = '$siteid' AND ";
                }
            } else {
                $siteid = get_siteid();
                $where .= "`siteid` = '$siteid' AND ";
            }
            
            $where .= "`addtime` BETWEEN '$where_start_time' AND '$where_end_time' AND ";

            if($name) {
                $where .= "`name` like '%$name%' AND ";
            } else {
                $where .= '';
            }
            if($mobile) {
                $where .= "`mobile` like '%$mobile%' AND ";
            } else {
                $where .= '';
            }
            
        } else {
            $where = '';
        }
        // echo $where;die;
        // $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        // $memberlist = $this->db->listinfo($where, 'userid DESC', $page, 15);
        // $pages = $this->db->pages;
        // $big_menu = array('?m=member&c=member&a=manage&menuid=72', L('member_research'));

        // $where = array('siteid'=>$this->get_siteid(),'isdelete'=>0);
        $where .= "`isdelete` = 0 ";
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        $infos = $this->exam_answer_db->listinfo($where, 'id DESC', $page, '15');
        $pages = $this->exam_answer_db->pages;
        include $this->admin_tpl('exam_answer_list');
    }

    public function get_fenshu_by_answerid($id){
        $fenshu = array();
        $answer_data = $this->Pre_Index->get_answer_by_id($id);
        $answer_choice_only = $this->Pre_Index->parse_choice_only_answer($answer_data['answer_choice_only']);
        $answer_choice_more = $this->Pre_Index->parse_choice_more_answer($answer_data['answer_choice_more']);
        
        $paper_data = $this->Pre_Index->get_paper_by_pid($answer_data['paper_id']);

        $cankao_choice_only = $this->Pre_Index->get_cankao_answer_by_ids($paper_data['quest_choice_only']);
        $cankao_choice_more = $this->Pre_Index->get_cankao_answer_by_ids($paper_data['quest_choice_more']);

        $fenshu['fenshu_choice_only'] = $this->Pre_Index->correct_fenshu_only($answer_choice_only,$cankao_choice_only);
        $fenshu['fenshu_choice_more'] = $this->Pre_Index->correct_fenshu_more($answer_choice_more,$cankao_choice_more);

        return $fenshu;
    }

    

    
}
?>