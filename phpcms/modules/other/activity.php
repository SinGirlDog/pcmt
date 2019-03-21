<?php 
defined('IN_PHPCMS') or exit('No permission resources.');
header("Access-Control-Allow-Origin: http://teacher.taiqiedu.com");

class activity {
  function __construct() {
        // 加载pinggu的数据模型
    $this->activity_tiaoji_db = pc_base::load_model('activity_tiaoji_model');
        // $this->mobile_db = pc_base::load_model('mobile_check_model');

    $this->_session_start();

        //定义站点ID常量，选择模版使用
    $siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : get_siteid();
    define("SITEID", $siteid);

          //读取配置
    $setting = new_html_special_chars(getcache('activity_tiaoji', 'commons'));
    $this->set = $setting[SITEID];
  }
  private function _session_start() {
    $session_storage = 'session_'.pc_base::load_config('system','session_storage');
    pc_base::load_sys_class($session_storage);
  }
  public function init() {


        // 加载前台模板
    include template('activity_tiaoji', 'index');
  }

    /**
     *    提交-异步
     */
    public function tijiao(){

      $arr = array();
      $arr['name'] = remove_xss($_POST['name']);
      $arr['mobile'] = remove_xss($_POST['phone']);
      $arr['wxnum'] = remove_xss($_POST['wxnum']);
      $arr['english'] = remove_xss($_POST['english']);
      $arr['zonghe'] = remove_xss($_POST['zonghe']);
      $arr['total'] = remove_xss($_POST['total']);
      $arr['addtime'] = date('Y-m-d H:i:s',time());
      if(empty($arr['name']) || empty($arr['mobile'])){
        $ret_arr['data'] = 'no_name_no_phone';
        exit();
      }
      $where = array('name'=>$arr['name'],'mobile'=>$arr['mobile']);
      $res = $this->activity_tiaoji_db->select($where);
      if($res){
        $ret_arr['data'] = 'already';
        echo json_encode($ret_arr);      
        exit();

      }
      else{
        $this->activity_tiaoji_db->insert($arr);
        $ret_arr = array();
        $ret_arr['data'] = 'success';
        echo json_encode($ret_arr); 
        exit();    
      }

    }


  }
  ?>