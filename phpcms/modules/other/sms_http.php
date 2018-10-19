<?php
defined('IN_PHPCMS') or exit('No permission resources.');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class sms_http
{
    function __construct() {
        // $this->mobile_db = pc_base::load_model('mobile_check_model');
        // $this->db_member = pc_base::load_model('member_model');
        // $this->_userid = param::get_cookie('_userid');
        // $this->_username = param::get_cookie('_username');
        // $this->_groupid = param::get_cookie('_groupid');
        
    }

    private function curl_post($curlPost,$url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }
    private function _session_start() {
		$session_storage = 'session_'.pc_base::load_config('system','session_storage');
		pc_base::load_sys_class($session_storage);
    }
    //mba作文提交时需要的手机验证码
    function zwxg_mobile_code()
    {
        $this->_session_start();
        $mark = remove_xss(trim($_GET['mark']));
        $code = remove_xss(trim($_GET['code']));
        if(strtolower($code) != $_SESSION['code']){
            echo '-2';
            die;
        }
        $preg_mobile = '/^1[3|4|5|7|8][0-9]\d{8}$/';
        $mobile = remove_xss(trim($_GET['mobile']));
        $is_mobile = preg_match($preg_mobile,$mobile);
        if($is_mobile < 1)
        {
            echo '-1'; //手机格式错误
            die;
        }
        $current_time = time();

        if(isset($_SESSION['zwxg_'.$mark.'_'.$mobile])){
            $ses_data = $_SESSION['zwxg_'.$mark.'_'.$mobile];
            if(($ses_data['mobile'] == $mobile) && ($ses_data['code'] == $code) && ($ses_data['status'] == 1)){
                $is_time = $current_time - $res_mobile['add_time'];
                if($is_time < 120)
                {
                    $next_time = 120 - $is_time;
                    echo '请在'.$next_time.'秒后重试';
                    die;
                }
            }
        }

        // $where_mobile = "mobile = '$mobile' AND code_type = 3";
        // $res_mobile = $this->mobile_db->get_one($where_mobile,'id,add_time','id desc');

        // if($res_mobile)
        // {
        //     $is_time = $current_time - $res_mobile['add_time'];
        //     if($is_time < 120)
        //     {
        //         $next_time = 120 - $is_time;
        //         echo '请在'.$next_time.'秒后重试';
        //         die;
        //     }
        // }
        $target = "http://106.ihuyi.com/webservice/sms.php?method=Submit";
        //替换成自己的测试账号,参数顺序和wenservice对应
        $code = rand(1000,9999);
        $content = "您的验证码是：".$code."。请不要泄露给其他人。";
        $content_encode = rawurlencode($content);
        $post_data = "account=cf_smtq&password=gcUTsg&mobile=".$mobile."&content=".$content_encode;

        // $res_huyi = $this->curl_post($post_data, $target);
        $obj_huyi = simplexml_load_string($res_huyi); 
        // var_dump($_POST,$obj_huyi);die;
        //本地测试强制赋值
        $obj_huyi->code = 2;
        $code = 2468;
       if($obj_huyi->code != 2)
        {
            echo $obj_huyi->msg;
           // echo '-2'; //手机短信发送失败
            die;
        }
        else 
        {
            $data['mobile'] = $mobile;
            // $data['user_id'] = $this->_userid;
            $data['code'] = $code;
            $data['status'] = 1;
            // $data['ip'] = ip();
            $data['add_time'] = time();

            //会话形式保存手机验证码相关信息
            if($mark=='master'){
                $master_arr = array(
                    '0'=>'',
                    '1'=>'18710974414'
                );
                if(in_array($mobile,$master_arr))
                    $_SESSION['master'] = $data;
            }
           
            $_SESSION['zwxg_'.$mark.'_'.$mobile] = $data;
            
            // $res_insert = $this->mobile_db->insert($data,true);
            // if($res_insert < 1)
            // {
            //     echo '-3'; //手机验证码记录失败
            //     die;
            // }
            // else
            // {
            // var_dump($_SESSION['zwxg_tj_'.$mobile]);die;
                echo '1';//短信验证码发送成功
            //     die;
            // }
            
        }
    }
}

