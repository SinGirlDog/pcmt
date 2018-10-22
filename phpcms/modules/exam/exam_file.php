<?php
defined('IN_PHPCMS') or exit('No permission resources. - exam_file.php');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_app_class('exam_index', 'exam', 0);
pc_base::load_app_class('xml', 'exam', 0);
pc_base::load_sys_class('form', '', 0);

class exam_file extends admin {

    public function __construct() {
        parent::__construct();//继承父类构造函数
        $setting = new_html_special_chars(getcache('exam', 'commons'));//读取考试配置缓存文件
        $this->set = $setting[$this->get_siteid()];
        $this->exam_db = pc_base::load_model('exam_model');//加载考试数据模型
        $this->exam_data_db = pc_base::load_model('exam_data_model');//加载考试数据模型
        $this->exam_file_db = pc_base::load_model('exam_file_model');//加载考试数据模型

        //预览调用前台控制器
        $this->Pre_Index = new exam_index();
    }
    
    public function init() {
        $start_time = isset($_GET['start_time']) ? $_GET['start_time'] : date('Y-m-d', SYS_TIME-date('t', SYS_TIME)*86400);
        $end_time = isset($_GET['end_time']) ? $_GET['end_time'] : date('Y-m-d', SYS_TIME);

        $where = array('siteid'=>$this->get_siteid(),'isdelete'=>0);
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        $infos = $this->exam_file_db->listinfo($where, 'id DESC', $page, '15');
        $infos = $this->Pre_Index->make_cattitle($infos);
        $pages = $this->exam_file_db->pages;
        
        $category_one = $this->Pre_Index->get_catid_name_arr();

        /* 加载后台管理模版 exam_file_list.tpl.php。 
         * 此文件位于phpcms/modules/模块/templates/  
         * 由此即可明白，其它后台管理模版亦位于此目录*/
        include $this->admin_tpl('exam_file_list');
    }

    public function upload_xml(){
        if(empty($_POST['cat_level_3'])){
            showmessage(L("请选择科目"),HTTP_REFERER);
        }
        $result = $this->xml_upload();
        if($result['status'] != 1){
            showmessage(L("上船失败"),HTTP_REFERER);
        }
        else{
            $data = array();
            $data['title'] = $_POST['title'];
            $data['catid'] = $_POST['cat_level_3'];
            $data['arrcatid'] = $_POST['cat_level_1'].','.$_POST['cat_level_2'].','.$_POST['cat_level_3'];
            $data['thumb'] = $result['thumb'];
            $data['siteid'] = SITEID;
            $data['addtime'] = SYS_TIME;

            $new_file_id = $this->exam_file_db->insert($data,true);
            if($new_file_id){
                showmessage(L("上船成功"),HTTP_REFERER);
            }
            else{
                showmessage(L("上船失败"),HTTP_REFERER);
            }

        }
    }

    public function preview_file(){
        $file_id = $_GET['id'];
        $file_data = $this->exam_file_db->get_one(array('id'=>$file_id));

        include $this->admin_tpl('preview_file');
    }
    

    /**
    * 删除卷宗(伪删除,更新isdelete标记)
    * @param    intval    $id    卷宗ID,可递归删除
    */
    public function delete_file() {
        if((!isset($_GET['id']) || empty($_GET['id'])) && (!isset($_POST['id']) || empty($_POST['id']))) {
            showmessage(L('未选中'), HTTP_REFERER);
        }
        if(is_array($_POST['id'])){
            foreach($_POST['id'] as $id_arr) {
                $id_arr = intval($id_arr);
                $this->exam_file_db->update(array('isdelete'=>1),array('id'=>$id_arr));
            }
            showmessage(L('删除成功'),HTTP_REFERER);
        }else{
            $id = intval($_GET['id']);
            if($id < 1) return false;
            $result = $this->exam_file_db->update(array('isdelete'=>1),array('id'=>$id));
                $this->Pre_Index->exam_question_del_byfile($id); 
            if($result){
                showmessage(L('删除成功'),HTTP_REFERER);
            }else {
                showmessage(L("删除失败"),HTTP_REFERER);
            }
        }
    }

    /**
     * 解析文件，习题入库
     */
    public function parsing_file(){
        $file_id = $_GET['id'];
        $file_data = $this->exam_file_db->get_one(array('id'=>$file_id));
        // var_dump($file_data);die;
        $xml = new xml();
        $xml->dir = $file_data['thumb'];
        $reform_data = $xml->get_reform_data();

        $file_quest_ids = array();
        foreach($reform_data as $key => $reform_one)
        {
            $exam_data = array(
                'catid'=>$file_data['catid'],
                'title'=>substr($reform_one['question_body'],0,80),
                'status'=>99,
                'inputtime'=>SYS_TIME,
                'updatetime'=>SYS_TIME,
            );
            $exam_id = $this->exam_db->insert($exam_data,true);
            $file_quest_ids[] = $exam_id;
            $reform_one['id'] = $exam_id;
            $one_id = $this->exam_data_db->insert($reform_one);
        }
        $item_num = sizeof($file_quest_ids);
        $quest_ids = implode(',', $file_quest_ids);
        $result = $this->exam_file_db->update(array('quest_ids'=>$quest_ids),array('id'=>$file_id));
        $cat_item_res = $this->Pre_Index->examquestion_category_item_plus($item_num,$file_data['catid']);
        if($result){
            showmessage(L('入库成功'),HTTP_REFERER);
        }
        else{
            showmessage(L('入库失败'),HTTP_REFERER);
        }
    }

    /**
     * 习题文件搜索？
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
        $infos = $this->exam_file_db->listinfo($where, 'id DESC', $page, '15');
        $pages = $this->exam_file_db->pages;
        include $this->admin_tpl('exam_paper_list');
    }

    
    
    /**
     *  习题文件上传XML
    */
    private function xml_upload(){
        $result = array('status'=>0,'thumb'=>'');
        if ($_FILES["file"]["error"] > 0)
        {
            echo "Error: " . $_FILES["file"]["error"] . " file_error <br />";
            $result['thumb'] = $_FILES["file"]["error"] . " file_error ";
        }
        else
        {
            if($_FILES['file']['type'] !='text/xml')
            {
                echo "Error: " . $_FILES["file"]["type"] . " is not xml type<br />";
                $result['thumb'] = $_FILES["file"]["type"] . " is not xml type ";
            }
            else{
                if($_FILES["file"]["size"] >= 1024*1024)
                {
                    echo "Error: " . $_FILES["file"]["size"] . " is bigger than 1.0MB <br />";
                    $result['thumb'] = $_FILES["file"]["size"] . " is bigger than 1.0MB ";
                }
                else{
                    if(!is_uploaded_file($_FILES['file']['tmp_name'])) { 
                        echo "Error:  tmp_name is not exists <br />";
                        $result['thumb'] = $_FILES["file"]["name"] . " tmp_name is not exists ";
                    }
                    else
                    {
                        $uploaded_file = $_FILES['file']['tmp_name'];
                        $document_root = $_SERVER['DOCUMENT_ROOT'];
                        //我们给每天的上传动态的创建一个文件夹  
                        $everyday_path = "uploadfile/exam_files/".date('Ymd');  
                        //判断该文件夹是否已经存在  
                        if(!file_exists($document_root .'/' . $everyday_path)) {  
                            mkdir($document_root .'/' . $everyday_path);  
                        }
                        //用-时间-随机数-生成唯一-文件名-
                        $file_name_arr = explode('.', $_FILES["file"]["name"]); 
                        $file_name_arr[0] = date('His').rand(100,999);
                        $file_name = implode('.', $file_name_arr);
                        
                        $after_move = move_uploaded_file($uploaded_file, $document_root .'/' . $everyday_path . "/" . $file_name);

                        if(!$after_move){
                            echo " Stored in no where!!! <br />";
                            $result['thumb'] = "  Stored in no where!!! ";
                        }
                        else{
                            // echo "Stored in: " . $document_root .'/' . $everyday_path . "/" . $file_name . "<br />";
                            $result['thumb'] = $everyday_path . "/" . $file_name;
                            $result['status'] = 1;
                        }
                    }
                }
            }
        }
        return $result;
    }
    //end xml_upload;
    
}
?>