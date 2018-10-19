<?php
defined('IN_PHPCMS') or exit('No permission resources. - exam.php');
pc_base::load_app_class('exam_index', 'exam', 0);
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('form', '', 0);

class exam_paper extends admin {
    public function __construct() {
        parent::__construct();//继承父类构造函数
        $setting = new_html_special_chars(getcache('exam', 'commons'));//读取考试配置缓存文件
        $this->set = $setting[$this->get_siteid()];
        $this->exam_paper_db = pc_base::load_model('exam_paper_model');//加载考试数据模型

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
        $infos = $this->exam_paper_db->listinfo($where, 'id DESC', $page, '15');
        $pages = $this->exam_paper_db->pages;
        
        /* 加载后台管理模版 exam_paper_list.tpl.php。 
         * 此文件位于phpcms/modules/模块/templates/  
         * 由此即可明白，其它后台管理模版亦位于此目录*/
        include $this->admin_tpl('exam_paper_list');
    }

    public function preview_paper() {
        $paper_data = $this->Pre_Index->get_paper_by_pid($_GET['id']);
        $quest_choice_only = $this->Pre_Index->get_quest_by_ids($paper_data['quest_choice_only']);
        $quest_choice_more = $this->Pre_Index->get_quest_by_ids($paper_data['quest_choice_more']);
        include $this->admin_tpl('preview_paper');
        // include template('exam_paper', 'show');
        // var_dump($paper_data);
        // $this->Pre_Index->show_one_paper();
    }
    

    /**
    * 删除考卷(伪删除,更新isdelete标记)
    * @param    intval    $id    试卷ID,可递归删除
    */
    public function delete_paper() {
        if((!isset($_GET['id']) || empty($_GET['id'])) && (!isset($_POST['id']) || empty($_POST['id']))) {
            showmessage(L('未选中'), HTTP_REFERER);
        }
        if(is_array($_POST['id'])){
            foreach($_POST['id'] as $id_arr) {
                $id_arr = intval($id_arr);
                $this->exam_paper_db->update(array('isdelete'=>1),array('id'=>$id_arr)); 
            }
            showmessage(L('删除成功'),HTTP_REFERER);
        }else{
            $id = intval($_GET['id']);
            if($id < 1) return false;
            $result = $this->exam_paper_db->update(array('isdelete'=>1),array('id'=>$id));
            if($result){
                showmessage(L('删除成功'),HTTP_REFERER);
            }else {
                showmessage(L("删除失败"),HTTP_REFERER);
            }
        }
    }

    /**
     * 考试卷搜索
     */
    function search_paper() {

        //考试卷信息
        $name = isset($_GET['name']) ? $_GET['name'] : '';
        $mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';

        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : '';
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);


        if (isset($_GET['search'])) {

            //默认选取一个月内的考卷，防止考卷量过大给数据造成灾难
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
            
            //如果是超级管理员角色，显示所有考卷，否则显示当前站点考卷
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

        $where .= "`isdelete` = 0 ";
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        $infos = $this->exam_paper_db->listinfo($where, 'id DESC', $page, '15');
        $pages = $this->exam_paper_db->pages;
        include $this->admin_tpl('exam_paper_list');
    }
    
    /**
     * 考试模块配置
     */
    public function setting() {
        //更新模型数据库,重设setting 数据. 
        $m_db = pc_base::load_model('module_model');
        $set = $m_db->get_one(array('module'=>'exam'));
        $setting = string2array($set['setting']);
        $now_setting = $setting[$this->get_siteid()];//当前站点的配置
        if(isset($_POST['dosubmit'])) {
            $setting[$this->get_siteid()] = $_POST['setting'];
            setcache('exam', $setting, 'commons'); 
            $set = array2string($setting);
            $m_db->update(array('setting'=>$set), array('module'=>ROUTE_M));
            showmessage('配置更新成功', HTTP_REFERER);
        } else {
            extract($now_setting);
            // 加载后台管理模版 setting.tpl.php
            include $this->admin_tpl('setting');
        }
    }

    /**
     * 考试模块问答设置
     */
    public function setting_QandA() {
        $category_one = $this->Pre_Index->get_catid_name_arr();
        $this->exam_qanda_db = pc_base::load_model('exam_qanda_model');//加载考试数据模型
        if(isset($_POST['dosubmit'])) {
            // var_export($_POST);die;
            if(!isset($_POST['cat_level_2'])){
                showmessage('问答设置更新失败', HTTP_REFERER);
                exit;
            }


            $already = $this->exam_qanda_db->get_one(array('catid'=>$_POST['cat_level_2']));
            if($already['catid']){
                $_POST['setting']['updatetime'] = SYS_TIME;
                $result = $this->exam_qanda_db->update($_POST['setting'],array('catid'=>$_POST['cat_level_2']));
            }
            else{
                $_POST['setting']['catid'] = $_POST['cat_level_2'];
                $_POST['setting']['siteid'] = SITEID;
                $_POST['setting']['addtime'] = SYS_TIME;
                $_POST['setting']['title'] = $this->make_title_by_catid($_POST['cat_level_2']);
                $result = $this->exam_qanda_db->insert($_POST['setting'],true);
            }
            if($result){
                showmessage('问答设置更新成功', HTTP_REFERER);
            }
            else{
                showmessage('问答设置更新失败', HTTP_REFERER);
            }

        }
        else
        {
            $infos = $this->exam_qanda_db->select();
            include $this->admin_tpl('setting_QandA');
        }
    }

    private function make_title_by_catid($catid){
        $this->category_db = pc_base::load_model('category_model');//加载考试数据模型
        $item = $this->category_db->get_one(array('catid'=>$catid),'arrparentid');
        $item['arrparentid'] .= ','.$catid;
        $catid_arr = explode(',',$item['arrparentid']);
        array_shift($catid_arr);
        array_shift($catid_arr);
        $arrparentid = implode(',', $catid_arr);
        $paper_title = $this->Pre_Index->make_paper_title($arrparentid);
        $title_arr = explode('-',$paper_title);
        array_pop($title_arr);
        $real_title = implode('-', $title_arr);
        return $real_title;
    }
    
}
?>