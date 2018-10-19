<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('db_factory', '', 0);
pc_base::load_sys_class('model', '', 0);
class exam_data_model extends model {
    public function __construct() {
        $this->db_config = pc_base::load_config('database');
        $this->db_setting = 'default';
        
        // 记得换成自己的表名
        $this->table_name = 'exam_data';
        $this->left_table_name = 'exam';
        parent::__construct();
    }


    public function left_select($where=array(),$data='id',$limit='',$order='',$catid){
        $dbname = $this->db_config[$this->db_setting]['database'];
        $pre = $this->db_config[$this->db_setting]['tablepre'];

        $select = "SELECT ".$this->table_name.".".$data." from `".$dbname."`.`".$this->table_name."`";
        $left =" left join `".$dbname."`.`".$pre.$this->left_table_name."` on ".$this->table_name.".id = ".$pre.$this->left_table_name.".id ";
        
        $where = $this->sqls($where);
        $where = "where ".$where." and catid = ".$catid;

        $order = " order by ".$order;
        $limit = " limit ".$limit;

        $sql = $select.$left.$where.$order.$limit;
        $res = $this->db->query($sql);
        return $this->fetch_array($res);
    }

}
?>