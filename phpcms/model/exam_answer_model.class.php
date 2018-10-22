<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class exam_answer_model extends model {
	function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'exam_answer';
		parent::__construct();
	}

	public function select_paiming($where_id,$data='rownum',$limit='',$order=''){
		$dbname = $this->db_config[$this->db_setting]['database'];
        $pre = $this->db_config[$this->db_setting]['tablepre'];
        
        $where = $this->sqls($where);
		
		$sql = "SELECT b.rownum FROM(SELECT t.*, @rownum := @rownum + 1 AS rownum FROM (SELECT @rownum := 0) r,
(SELECT * FROM `".$this->table_name."` ORDER BY fenshu_total DESC) AS t
) AS b WHERE b.id =".$where_id;
		
		// return $sql;
		$res = $this->db->query($sql);
		$paiming = $this->fetch_array($res);
        return $paiming[0]['rownum'];
	}
	
}
?>