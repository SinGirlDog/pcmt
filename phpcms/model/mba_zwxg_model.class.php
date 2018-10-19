<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class mba_zwxg_model extends model {
    public function __construct() {
        $this->db_config = pc_base::load_config('database');
        $this->db_setting = 'default';
        
        // 记得换成自己的表名
        $this->table_name = 'mba_zwxg';
        parent::__construct();
    }
}
?>