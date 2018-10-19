<?php
defined('IN_PHPCMS') or exit('No permission resources. - exam.php');
pc_base::load_app_class('admin', 'admin', 0);

class exam_paper extends admin {
    public function __construct() {
        parent::__construct();//继承父类构造函数
        $setting = new_html_special_chars(getcache('exam', 'commons'));//读取留言本配置缓存文件
        $this->set = $setting[$this->get_siteid()];
        $this->exam_paper_db = pc_base::load_model('exam_paper_model');//加载留言本数据模型
    }
    
    public function init() {
        $where = array('siteid'=>$this->get_siteid());
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        $infos = $this->exam_paper_db->listinfo($where, 'gid DESC', $page, '15');
        
        /* 加载后台管理模版 exam_list.tpl.php。 
         * 此文件位于phpcms/modules/模块/templates/  
         * 由此即可明白，其它后台管理模版亦位于此目录*/
         include $this->admin_tpl('exam_list');
    }
    
    /* 未回复列表 */
    public function unreplylist() {
        $where = array('reply'=>'','siteid'=>$this->get_siteid());        
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        $infos = $this->exam_paper_db->listinfo($where, 'gid DESC', $page, '15');
        include $this->admin_tpl('exam_list');
    }
    
    /**
     * 回复留言
     */
    public function reply() {
        if(isset($_POST['dosubmit'])){
             $gid = intval($_GET['gid']);
            if($gid < 1) return false;  
             $_POST['reply']['replytime'] = SYS_TIME;
            $_POST['reply']['reply_status'] = '1';
            $this->exam_paper_db->update($_POST['reply'], array('gid'=>$gid)); 
            showmessage(L('回复成功'),'?m=exam&c=exam&a=init');
         } else {
             $gid = intval($_GET['gid']);
            if($gid < 1) return false; 
             $show_validator = $show_scroll = $show_header = true;
             $info = $this->exam_paper_db->get_one(array('gid'=>$_GET['gid']));
            if(!$info) showmessage(L('exam_exit'),'?m=exam&c=exam&a=init');
            extract($info); 
            // 加载后台管理模版 exam_reply.tpl.php
             include $this->admin_tpl('exam_reply');
        }
    }
    
    /**
    * 删除留言 
    * @param    intval    $gid    留言ID，递归删除
    */
    public function delete() {
        if((!isset($_GET['gid']) || empty($_GET['gid'])) && (!isset($_POST['gid']) || empty($_POST['gid']))) {
            showmessage(L('未选中'), HTTP_REFERER);
        }
        if(is_array($_POST['gid'])){
            foreach($_POST['gid'] as $gid_arr) {
                $gid_arr = intval($gid_arr);
                $this->exam_paper_db->delete(array('gid'=>$gid_arr)); 
            }
            showmessage(L('删除成功'),'?m=exam&c=exam');
        }else{
            $gid = intval($_GET['gid']);
            if($gid < 1) return false;
            $result = $this->exam_paper_db->delete(array('gid'=>$gid));
            if($result){
                showmessage(L('删除成功'),'?m=exam&c=exam');
            }else {
                showmessage(L("删除失败"),'?m=exam&c=exam');
            }
        }
    }
    
    /**
     * 留言本模块配置
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
            showmessage('配置更新成功', '?m=exam&c=exam&a=init');
        } else {
            extract($now_setting);
            // 加载后台管理模版 setting.tpl.php
            include $this->admin_tpl('setting');
        }
    }
}
?>