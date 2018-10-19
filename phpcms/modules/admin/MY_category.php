<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class MY_category extends category {
	private $db;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('category_model');
		$this->siteid = $this->get_siteid();
	}
	/**
	 * 管理栏目
	 */
	public function init () {
		$show_pc_hash = '';
		$tree = pc_base::load_sys_class('tree');
		$models = getcache('model','commons');
		$sitelist = getcache('sitelist','commons');
		$category_items = array();
		foreach ($models as $modelid=>$model) {
			$category_items[$modelid] = getcache('category_items_'.$modelid,'commons');
		}
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$categorys = array();
		//读取缓存
		$result = getcache('category_content_'.$this->siteid,'commons');
		$show_detail = count($result) < 500 ? 1 : 0;
		$parentid = $_GET['parentid'] ? $_GET['parentid'] : 0;
		$html_root = pc_base::load_config('system','html_root');
		$types = array(0 => L('category_type_system'),1 => L('category_type_page'),2 => L('category_type_link'));
		if(!empty($result)) {
			foreach($result as $r) {
				$r['modelname'] = $models[$r['modelid']]['name'];
				$r['str_manage'] = '';
				if(!$show_detail) {
					if($r['parentid']!=$parentid) continue;
					$r['parentid'] = 0;
					$r['str_manage'] .= '<a href="?m=admin&c=category&a=init&parentid='.$r['catid'].'&menuid='.$_GET['menuid'].'&s='.$r['type'].'&pc_hash='.$_SESSION['pc_hash'].'">'.L('manage_sub_category').'</a> | ';
				}
				$r['str_manage'] .= '<a href="?m=admin&c=category&a=add&parentid='.$r['catid'].'&menuid='.$_GET['menuid'].'&s='.$r['type'].'&pc_hash='.$_SESSION['pc_hash'].'">'.L('add_sub_category').'</a> | <a href="?m=admin&c=category&a=edit&catid='.$r['catid'].'&menuid='.$_GET['menuid'].'&type='.$r['type'].'&pc_hash='.$_SESSION['pc_hash'].'">'.L('edit').'</a> | ';
				
				$r['str_manage'] .= '<a href="?m=admin&c=category&a=copy&catid='.$r['catid'].'&menuid='.$_GET['menuid'].'&type='.$r['type'].'&pc_hash='.$_SESSION['pc_hash'].'">复制</a> | <a href="javascript:confirmurl(\'?m=admin&c=category&a=delete&catid='.$r['catid'].'&menuid='.$_GET['menuid'].'\',\''.L('confirm',array('message'=>addslashes($r['catname']))).'\')">'.L('delete').'</a> ';
				$r['typename'] = $types[$r['type']];
				$r['display_icon'] = $r['ismenu'] ? '' : ' <img src ="'.IMG_PATH.'icon/gear_disable.png" title="'.L('not_display_in_menu').'">';
				if($r['type'] ) {
					$r['items'] = '';
				} else {
					$r['items'] = $category_items[$r['modelid']][$r['catid']];
				}
				$r['help'] = '';
				$setting = string2array($r['setting']);
				if($r['url']) {
					if(preg_match('/http:\/\//', $r['url'])) {
						$catdir = $r['catdir'];
						$prefix = $r['sethtml'] ? '' : $html_root;
						if($this->siteid==1) {
							$catdir = $prefix.'/'.$r['parentdir'].$catdir;
						} else {
							$catdir = $prefix.'/'.$sitelist[$this->siteid]['dirname'].$html_root.'/'.$catdir;
						}
						if($r['type']==0 && $setting['ishtml'] && strpos($r['url'], '?')===false && substr_count($r['url'],'/')<4) $r['help'] = '<img src="'.IMG_PATH.'icon/help.png" title="'.L('tips_domain').$r['url'].'&#10;'.L('directory_binding').'&#10;'.$catdir.'/">';
					} else {
						$r['url'] = substr($sitelist[$this->siteid]['domain'],0,-1).$r['url'];
					}
					$r['url'] = "<a href='$r[url]' target='_blank'>".L('vistor')."</a>";
				} else {
					$r['url'] = "<a href='?m=admin&c=category&a=public_cache&menuid=43&module=admin'><font color='red'>".L('update_backup')."</font></a>";
				}
				$categorys[$r['catid']] = $r;
			}
		}
		$str  = "<tr>
					<td align='center'><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input-text-c'></td>
					<td align='center'>\$id</td>
					<td >\$spacer\$catname\$display_icon</td>
					<td>\$typename</td>
					<td>\$modelname</td>
					<td align='center'>\$items</td>
					<td align='center'>\$url</td>
					<td align='center'>\$help</td>
					<td align='center' >\$str_manage</td>
				</tr>";
		$tree->init($categorys);
		$categorys = $tree->get_tree(0, $str);
		include $this->admin_tpl('category_manage');
	}
        /**
	 * 复制栏目
	 */
	public function copy() {
		if(isset($_POST['dosubmit'])) {
			pc_base::load_sys_func('iconv');
			$catid = intval($_POST['catid']);

                        $_POST['info']['type'] = intval($_POST['type']);

			$_POST['info']['siteid'] = $this->siteid;
			$_POST['info']['module'] = 'content';
			$setting = $_POST['setting'];
			if($_POST['info']['type']!=2) {
				//栏目生成静态配置
				if($setting['ishtml']) {
					$setting['category_ruleid'] = $_POST['category_html_ruleid'];
				} else {
					$setting['category_ruleid'] = $_POST['category_php_ruleid'];
					$_POST['info']['url'] = '';
				}
			}

			//内容生成静态配置
			if($setting['content_ishtml']) {
				$setting['show_ruleid'] = $_POST['show_html_ruleid'];
			} else {
				$setting['show_ruleid'] = $_POST['show_php_ruleid'];
			}
			if($setting['repeatchargedays']<1) $setting['repeatchargedays'] = 1;
			$_POST['info']['sethtml'] = $setting['create_to_html_root'];
			$_POST['info']['setting'] = array2string($setting);
                        
			$end_str = $old_end =  '<script type="text/javascript">window.top.art.dialog({id:"test"}).close();window.top.art.dialog({id:"test",content:\'<h2>'.L("add_success").'</h2><span style="fotn-size:16px;">'.L("following_operation").'</span><br /><ul style="fotn-size:14px;"><li><a href="?m=admin&c=category&a=public_cache&menuid=43&module=admin" target="right"  onclick="window.top.art.dialog({id:\\\'test\\\'}).close()">'.L("following_operation_1").'</a></li><li><a href="'.HTTP_REFERER.'" target="right" onclick="window.top.art.dialog({id:\\\'test\\\'}).close()">'.L("following_operation_2").'</a></li></ul>\',width:"400",height:"200"});</script>';
			$catname = CHARSET == 'gbk' ? $_POST['info']['catname'] : $_POST['info']['catname'];
			$letters = gbk_to_pinyin($catname);
			//echo array2string($letters);
                        $_POST['info']['letter'] = strtolower(implode('', $letters));
			//echo array2string($_POST['info']);
                        //exit('No permission resources.');
                        if (intval($_POST['toid'])==0){
                        $parentid = $this->db->insert($_POST['info'], true);
			$this->update_priv($parentid, $_POST['priv_roleid']);
                        $this->update_priv($parentid, $_POST['priv_groupid'],0);
                        }else{
                           $parentid = $_POST['toid'];
                        }
                        $this->copy_child($catid,$parentid);
			$this->cache();
			showmessage(L('add_success').$end_str);
		} else {
			//获取站点模板信息
			pc_base::load_app_func('global');
			$template_list = template_list($this->siteid, 0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			
			
			$show_validator = '';
			$catid = intval($_GET['catid']);
			pc_base::load_sys_class('form','',0);
			$r = $this->db->get_one(array('catid'=>$catid));
			if($r) extract($r);
			$setting = string2array($setting);
			
			$this->priv_db = pc_base::load_model('category_priv_model');
			$this->privs = $this->priv_db->select(array('catid'=>$catid));
			
			$type = $_GET['type'];
			if($type==0) {
				include $this->admin_tpl('category_copy');
			} elseif ($type==1) {
				include $this->admin_tpl('category_page_copy');
			} else {
				include $this->admin_tpl('category_link');
			}
		}

	}
	/**
	 * 复制子级栏目
	 * @param $catid 要复制的栏目id
         * @param $parentid 复制到父栏目id
	 */
	private function copy_child($catid,$parentid='0') {
		$list = $this->db->select(array('parentid'=>$catid));
		foreach($list as $r ) {
                    $_POST['info']['parentid'] = $parentid;
                    $_POST['info']['catname'] = $r['catname'];
                    $_POST['info']['catdir'] = $r['catdir'];
                    $_POST['info']['image'] = $r['image'];
                    $_POST['info']['description'] = $r['description'];
                    $setting = $_POST['setting'];
			if($_POST['info']['type']!=2) {
				//栏目生成静态配置
				if($setting['ishtml']) {
					$setting['category_ruleid'] = $_POST['category_html_ruleid'];
				} else {
					$setting['category_ruleid'] = $_POST['category_php_ruleid'];
					$_POST['info']['url'] = '';
				}
			}

			//内容生成静态配置
			if($setting['content_ishtml']) {
				$setting['show_ruleid'] = $_POST['show_html_ruleid'];
			} else {
				$setting['show_ruleid'] = $_POST['show_php_ruleid'];
			}
                        $rsetting = string2array($r['setting']);
                        $setting['meta_title'] = $rsetting['meta_title'];
                        $setting['meta_keywords'] = $rsetting['meta_keywords'];
                        $setting['meta_description'] = $rsetting['meta_description'];
			if($setting['repeatchargedays']<1) $setting['repeatchargedays'] = 1;
			$_POST['info']['sethtml'] = $setting['create_to_html_root'];
			$_POST['info']['setting'] = array2string($setting);

                    $catid = $this->db->insert($_POST['info'], true);
		    $this->update_priv($catid, $_POST['priv_roleid']);			
                    $this->update_priv($catid, $_POST['priv_groupid'],0);
                    $this->copy_child($r['catid'],$catid);
		}
		return true;
	}
	public function recatname() {
		$list = $this->db->select();
		foreach($list as $r ) {
                    $catname = safe_replace($r['catname']);
                    echo $catname.'-';
                    $catname = str_replace(chr(13),'',safe_replace($catname));
                    echo $catname;
                    $this->db->update(array('catname'=>$catname),array('catid'=>$r['catid']));
		}
	}
        /**
	 * 检查目录是否存在
	 * @param  $return_method 返回方法
	 * @param  $catdir 目录
	 */
	public function public_check_catdir($return_method = 1,$catdir = '') {
		$catdir = $catdir ? $catdir : $_GET['catdir'];
		$old_dir = $_GET['old_dir'];
		$r = $this->db->get_one(array('siteid'=>$this->siteid,'module'=>'content','catdir'=>$catdir,'parentid'=>$_POST['info']['parentid']));
		if($r && $old_dir != $r['catdir']) {
			//目录存在
			if($return_method) {
				exit('0');
			} else {
				return false;
			}
		} else {
			if($return_method) {
				exit('1');
			} else {
				return true;
			}
		}
	}
	/**
	 * 更新权限
	 * @param  $catid
	 * @param  $priv_datas
	 * @param  $is_admin
	 */
	private function update_priv($catid,$priv_datas,$is_admin = 1) {
		$this->priv_db = pc_base::load_model('category_priv_model');
		$this->priv_db->delete(array('catid'=>$catid,'is_admin'=>$is_admin));
		if(is_array($priv_datas) && !empty($priv_datas)) {
			foreach ($priv_datas as $r) {
				$r = explode(',', $r);
				$action = $r[0];
				$roleid = $r[1];
				$this->priv_db->insert(array('catid'=>$catid,'roleid'=>$roleid,'is_admin'=>$is_admin,'action'=>$action,'siteid'=>$this->siteid));
			}
		}
	}
        /**
	 * 检查栏目权限
	 * @param $action 动作
	 * @param $roleid 角色
	 * @param $is_admin 是否为管理组
	 */
	private function check_category_priv($action,$roleid,$is_admin = 1) {
		$checked = '';
		foreach ($this->privs as $priv) {
			if($priv['is_admin']==$is_admin && $priv['roleid']==$roleid && $priv['action']==$action) $checked = 'checked';
		}
		return $checked;
	}
	
}
?>