<?php 
defined('IN_PHPCMS') or exit('No permission resources.');
class index {
    function __construct() {
        // 加载zwxg的数据模型
        $this->mba_zwxg_db = pc_base::load_model('mba_zwxg_model');
        
        // 取得当前登录会员的会员名(username)和会员ID(userid)
        // $this->_username = param::get_cookie('_username');
        // $this->_userid = param::get_cookie('_userid');
        
        //定义站点ID常量，选择模版使用
        $siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : get_siteid();
        define("SITEID", $siteid);

          //读取配置
        $setting = new_html_special_chars(getcache('mba_zwxg', 'commons'));
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
        
        // $where = array('passed'=>1,'siteid'=>SITEID);
        // $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        // $infos = $this->mba_zwxg_db->listinfo($where, 'gid DESC', $page, $pagesize);
        // $infos = new_html_special_chars($infos);
        
        // 加载系统form类，用于前台模块文件中生成验证码
        pc_base::load_sys_class('form', '', 0);
        
        // 加载前台模板
        include template('mba_zwxg', 'index');
    }
    
    /**
     *    提交作文-异步
     */
    public function tijiao()  {
        // $data['0']=>array('name'=>'tijiao[name]','value'=>sunlijia);
        foreach($_POST['data'] as $key=>$val){
            if(strpos($val['name'],'tijiao') !== FALSE){
                $tijiao_arr = explode(']', $val['name']);
                $tijiao_key = explode('[', $tijiao_arr[0]);
                $_POST['tijiao'][$tijiao_key[1]] = $val['value'];
            }
            else{
                $_POST[$val['name']] = $val['value'];
            }
        }
        // var_dump($_POST);die;

        // if(isset($_POST['dosubmit'])){ 
            // 标题和内容不能为空
            if (!(isset($_POST['tijiao']['name']) && trim($_POST['tijiao']['name']) && 
                isset($_POST['tijiao']['phone']) && trim($_POST['tijiao']['phone']) && 
                isset($_POST['tijiao']['QQ']) && trim($_POST['tijiao']['QQ']) && 
                isset($_POST['tijiao']['zw_type']) && trim($_POST['tijiao']['zw_type']) && 
                isset($_POST['tijiao']['title']) && trim($_POST['tijiao']['title']) && 
                isset($_POST['tijiao']['content']) && trim($_POST['tijiao']['content']) && 
                isset($_POST['code']) && trim($_POST['code']))) 
            {
                echo 'fill_in_blanks';
                exit;
                // showmessage(L('需要填写完整'), "?m=mba_zwxg&c=index&siteid=".SITEID);
            }

            // 验证码
            if(isset($_POST['code'])){

                $this->_session_start();
                if(!isset($_SESSION)) {
                    session_start();
                }
                $code = isset($_POST['code']) && trim($_POST['code']) ? trim($_POST['code']) : 0;
                if ($_SESSION['code'] != strtolower($code)) {
                    echo 'code_img_error';
                    exit;
                    // showmessage(L('验证码错误'), HTTP_REFERER);
                }
            } 
            //手机验证码
            if(isset($_POST['code_mobile'])){
                $this->check_mobile_code($_POST['tijiao']['phone'],$_POST['code_mobile'],'tj');
            }
            $set = $this->set;
            $_POST['tijiao']['addtime'] = SYS_TIME;
            $_POST['tijiao']['siteid'] = SITEID;
            $this->mba_zwxg_db->insert($_POST['tijiao']);
            echo 'insert_ok';
            // showmessage(L('添加成功'), "?m=mba_zwxg&c=index&siteid=".SITEID);
        // }  else  {
        //     var_dump($_POST);die;
        //     echo  '请通过正常的方式提交作文，谢谢';
        // }
    }


     /**
     *    master_login 
     */
    public function master_login(){
        $this->_session_start();
        session_start();
        if(isset($_POST['domasterlogin'])){ 
              //手机验证码
            if(isset($_POST['code_mobile'])){
                $this->check_mobile_code($_POST['phone'],$_POST['code_mobile'],'master');
            }
            showmessage(L('登录成功'), "?m=mba_zwxg&c=index&a=xiugai&siteid=".SITEID);

        }
        // 加载master_login模板
        include template('mba_zwxg', 'master_login');
    }

    /**
     *    修改作文 
     */
    public function xiugai()  {

        $this->_session_start();
        session_start();
        
        if(!isset($_SESSION['master'])){
            showmessage(L('请您登陆'), "?m=mba_zwxg&c=index&a=master_login&siteid=".SITEID);
        }
        //设置分页条数
        $pagesize = $this->set['pagesize'] ? $this->set['pagesize'] : 20;

        $where = array('siteid'=>SITEID,'editstatus'=>0);
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        $infos_wei = $this->mba_zwxg_db->listinfo($where, 'zwid ASC', $page, $pagesize);
        $infos_wei = new_html_special_chars($infos_wei); 

        $where = array('siteid'=>SITEID,'editstatus'=>1);
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        $infos_gai = $this->mba_zwxg_db->listinfo($where, 'zwid DESC', $page, $pagesize);
        $infos_gai = new_html_special_chars($infos_gai);  
        include template('mba_zwxg', 'xiugai');
    }

    /**
     *    作文修改的提交
     */
    public function xiugai_sub() {

        if(isset($_POST['editsubmit'])){
            $thumb_arr = $this->xg_upload();
            if($thumb_arr['status']==0){
                // echo  '您提交的<修改图片>可能存在问题,请尝试重新上传,或令相关人员核查程序,谢谢';
                // exit;
            }

            $edit_arr = array();
            $edit_arr['editcontent'] = $_POST['content'];
            $edit_arr['edittime'] = SYS_TIME;
            $edit_arr['editstatus'] = 1;
            $edit_arr['editfenshu'] = $_POST['fenshu'];
            $edit_arr['editthumb'] = $thumb_arr['thumb'];
            $where = array('siteid'=>SITEID,'zwid'=>$_POST['zwid']);
            $this->mba_zwxg_db->update($edit_arr,$where);
            showmessage(L('修改成功'), "?m=mba_zwxg&c=index&a=xiugai&siteid=".SITEID);
        } else {
           echo  '您修改作文的提交可能存在问题,为安全起见,需要相关人员核查,谢谢';
       }
   }

   /**
     *    支付成功回调函数无误的跳转
     */
    public function pay_success(){
        $trade_no = $_GET['trade_no'];
        $mobile = $_GET['mobile'];
        $name = $_GET['name'];
        
        $url = "http://www.halouxue.com/wxpay_new/example/diycallback.php?out_trade_no=".$trade_no;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
       
        if($result==1){
            $where_pre = array('siteid'=>SITEID,'phone'=>$mobile,'trade_no'=>$trade_no);
            $trade_no_count = $this->mba_zwxg_db->count($where_pre);
            if($trade_no_count){
                //结合商户单号和手机号查询如果已经有记录。本次失效。不做任何操作
            }
            else{
                $pay_arr = array();
                $pay_arr['pay_time'] = SYS_TIME;
                $pay_arr['pay_status'] = 1;
                $pay_arr['trade_no'] = $trade_no;
                $where = array('siteid'=>SITEID,'phone'=>$mobile,'pay_status'=>0,'trade_no'=>'','pay_time'=>0);
                $not_pay_item = $this->mba_zwxg_db->get_one($where);
                $where_new = array('zwid' => $not_pay_item['zwid']);
                $this->mba_zwxg_db->update($pay_arr,$where_new);
            }
        }
        else{
        }
        showmessage(L('支付成功。请您近期关注<历史批改记录>。以便查询您作文的修改情况。'), "?m=mba_zwxg&c=index&siteid=".SITEID);
   }

    /**
     *    历史批改记录查询
     */
    public function history() {
        if(isset($_POST['historysubmit'])){ 
            // var_dump($_POST);die;
            // 验证码
            if(isset($_POST['code'])){
                $session_storage = 'session_'.pc_base::load_config('system','session_storage');
                pc_base::load_sys_class($session_storage);
                if(!isset($_SESSION)) {
                    session_start();
                }
                $code = isset($_POST['code']) && trim($_POST['code']) ? trim($_POST['code']) : showmessage(L('请输入验证码'), HTTP_REFERER);
                if ($_SESSION['code'] != strtolower($code)) {
                    showmessage(L('验证码错误'), HTTP_REFERER);
                }
            }
             //手机验证码
            if(isset($_POST['code_mobile'])){
                $this->check_mobile_code($_POST['history_mobile'],$_POST['code_his'],'his');
            }

            $where_name = array('siteid' => SITEID,'phone' => $_POST['history_mobile']);
            $infos_name = $this->mba_zwxg_db->get_one($where_name);
            if(!$infos_name['name']){
                $infos_name['name'] = $_POST['history_mobile'];
            }

            //设置分页条数
            $pagesize = $this->set['pagesize'] ? $this->set['pagesize'] : 20;
            $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;

            $where = array('siteid'=>SITEID,'editstatus'=>1,'pay_status'=>1,'phone'=>$_POST['history_mobile']);
            $infos_history = $this->mba_zwxg_db->listinfo($where, 'zwid ASC', $page, $pagesize);
            $infos_history = new_html_special_chars($infos_history); 

            include template('mba_zwxg', 'jieguo');

        } else {
           echo  '您查询历史批改记录的申请方式可能存在问题,请咨询相关人员,谢谢';
       }
   }

    /**
     *    手机验证码校验
     */
    private function check_mobile_code($mobile,$code,$type=''){
        if(isset($_SESSION['zwxg_'.$type.'_'.$mobile])){
            $ses_data = $_SESSION['zwxg_'.$type.'_'.$mobile];
            if(($ses_data['mobile'] == $mobile) && ($ses_data['code'] == $code) && ($ses_data['status'] == 1)){
                $current_time = time();
                if(($current_time - $ses_data['add_time']) > 120)
                {
                    echo '手机验证码已失效，请在2分钟内使用';
                    // showmessage('手机验证码已失效，请在2分钟内使用',HTTP_REFERER);
                    die;
                }
                else
                {
                    $_SESSION['zwxg_'.$type.'_'.$mobile]['status'] = 0;
                }
            }
            else
            {
                echo '手机验证码错误-1';
                // showmessage('手机验证码错误-1',HTTP_REFERER);
                die;
            }
        }
        else
        {
            echo '手机验证码错误';
            // showmessage('手机验证码错误',HTTP_REFERER);
            die;
        }
    }

    /**
     *    作文修改-的-批改结果图片-上传
    */
    private function xg_upload(){
        $result = array('status'=>0,'thumb'=>'');
        if ($_FILES["file"]["error"] > 0)
        {
            // echo "Error: " . $_FILES["file"]["error"] . " file_error <br />";
            $result['thumb'] = $_FILES["file"]["error"] . " file_error ";
        }
        else
        {
            if(substr($_FILES['file']['type'],0,6) !='image/')
            {
                // echo "Error: " . $_FILES["file"]["type"] . " is not image type<br />";
                $result['thumb'] = $_FILES["file"]["type"] . " is not image type ";
            }
            else{
                if($_FILES["file"]["size"] >= 1024*1024*10)
                {
                    // echo "Error: " . $_FILES["file"]["size"] . " is bigger than 10MB <br />";
                    $result['thumb'] = $_FILES["file"]["size"] . " is bigger than 10MB ";
                }
                else{
                    if(!is_uploaded_file($_FILES['file']['tmp_name'])) { 
                        // echo "Error:  tmp_name is not exists <br />";
                        $result['thumb'] = $_FILES["file"]["name"] . " tmp_name is not exists ";
                    }
                    else
                    {
                        $uploaded_file = $_FILES['file']['tmp_name'];
                        $document_root = $_SERVER['DOCUMENT_ROOT'];
                        //我们给每天的上传动态的创建一个文件夹  
                        $everyday_path = "uploadfile/zwxg/".date('Ymd');  
                        //判断该文件夹是否已经存在  
                        if(!file_exists($document_root .'/' . $everyday_path)) {  
                            mkdir($document_root .'/' . $everyday_path);  
                        }
                        //用-时间-组合-作文数据ID-生成唯一指定结果图片-文件名-
                        $file_name_arr = explode('.', $_FILES["file"]["name"]); 
                        $file_name_arr[0] = date('His').'_zwid_'.$_POST['zwid'];
                        $file_name = implode('.', $file_name_arr);
                        
                        $after_move = move_uploaded_file($uploaded_file, $document_root .'/' . $everyday_path . "/" . $file_name);

                        if(!$after_move){
                            // echo " Stored in no where!!! <br />";
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
    //end xg_upload;

}
?>