<?php 
die;//这个功能影响较大、不可肆意执行;
defined('IN_PHPCMS') or exit('No permission resources.');
class index {
	private $modelid = 82;
	private $siteid = 69;

	function __construct() {
        // 加载category的数据模型;查询相关分类
		$this->category_copy_db = pc_base::load_model('category_copy_model');
		$this->category_db = pc_base::load_model('category_model');

        //定义站点ID常量，选择模版使用
		$siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : get_siteid();
		define("SITEID", $siteid);

          //读取配置
		$setting = new_html_special_chars(getcache('clone_cate', 'commons'));
		$this->set = $setting[SITEID];
	}

	private function _session_start(){
        /*V9验证码的数值是通过SESSION传递，故在这段代码中，首先加载配置文件，                 
        取出当前系统配置中SESSION的存储方式。然后根据SESSION的存储方式，来加载对应的系统类库*/
        $session_storage = 'session_'.pc_base::load_config('system','session_storage');
        pc_base::load_sys_class($session_storage);
    }

	public function init() {
		$this->_session_start();
		if($_SESSION['CLONE_CATE'] == 1)
		{
			echo 'already_done';
			die;
		}

		$where = array('siteid'=>2,'parentid'=>0);
		$test_data_0 = $this->category_copy_db->select($where);
		foreach($test_data_0 as $key_0=>$val_0)
		{
			if($val_0['child'] == 1)
			{
				$where_1 = array('siteid'=>2,'parentid'=>$val_0['catid']);
				$test_data_1[$val_0['catid']] = $this->category_copy_db->select($where_1);

				foreach($test_data_1[$val_0['catid']] as $key_1=>$val_1)
				{
					if($val_1['child'] == 1)
					{
						$where_2 = array('siteid'=>2,'parentid'=>$val_1['catid']);
						$test_data_2[$val_1['catid']] = $this->category_copy_db->select($where_2);

						foreach($test_data_2[$val_1['catid']] as $key_2=>$val_2)
						{
							if($val_2['child'] == 1)
							{
								$where_3 = array('siteid'=>2,'parentid'=>$val_2['catid']);
								$test_data_3[$val_2['catid']] = $this->category_copy_db->select($where_3);

							}
						}
					}
				}

			}
		}

		foreach($test_data_0 as $key_0=>$val_0)
		{
			$new_cat_id = $this->save_one_item($val_0,0);

			if($val_0['child'] == 1)
			{
				$arrchildid_1 = '';
				foreach($test_data_1[$val_0['catid']] as $key_1=>$val_1)
				{
					$new_cat_id_1 = $this->save_one_item($val_1,$new_cat_id);

					if($val_1['child'] == 1)
					{
						$arrchildid_2 = '';
						foreach($test_data_2[$val_1['catid']] as $key_2=>$val_2)
						{
							$new_cat_id_2 = $this->save_one_item($val_2,$new_cat_id_1);
							$arrchildid_2[] = $new_cat_id_2;
							$this->update_one_arrparentid($new_cat_id_2,'0,'.$new_cat_id.','.$new_cat_id_1);
						}
						$this->update_one_arrchildid($new_cat_id_1,$arrchildid_2);
					}
					$arrchildid_1[] = implode(',',$arrchildid_2);
					$this->update_one_arrparentid($new_cat_id_1,'0,'.$new_cat_id);
				}
				$this->update_one_arrchildid($new_cat_id_1,$arrchildid_1);
			}
		}
		$_SESSION['CLONE_CATE'] = 1;
		include template('clone_cate', 'index');
	}

	private function save_one_item($item,$parentid){
		$data = $item;
		$new_cat_id = $this->category_copy_db->insert(array('catname'=>$data['catname']),true);
		$where = array('catid'=>$new_cat_id);
		array_shift($data);
		$data['siteid'] = $this->siteid;
		$data['modelid'] = $this->modelid;
		$data['arrchildid'] = $new_cat_id;
		$data['parentid'] = $parentid;
		$data['setting'] = 'array()';
		$this->category_copy_db->update($data,$where);

		return $new_cat_id;
	}

	private function update_one_arrparentid($catid,$arrparentid){
		$data = array();
		$data['arrparentid'] = $arrparentid;
		$where = array('catid'=>$catid);
		$this->category_copy_db->update($data,$where);
	}

	private function update_one_arrchildid($catid,$arr_childid){
		$data = array();
		$arrchildid = implode(',',$arr_childid);
		$data['arrchildid'] = $arrchildid;
		$where = array('catid'=>$catid);
		$this->category_copy_db->update($data,$where);
	}

}
?>