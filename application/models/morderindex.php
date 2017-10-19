<?php
/**
 * 订单索引表操作模型
 * @author lifw
 */
class MOrderIndex extends MY_Model {
	protected $table = 'order_index';
	function save($info){
			$sql = 'insert into `'.$this->db->dbprefix($this->table).'`(`order_id`, `table_name`, `col_name`, `col_value`)values(' 
					. $this->db->escape($info->order_id) 
					. ",".$this->db->escape($info->table_name)
			 		. ",".$this->db->escape($info->col_name)
			 		. ",".$this->db->escape($info->col_value)
			 		. ") ON DUPLICATE KEY UPDATE col_value=".$this->db->escape($info->col_value);
			return $this->db->query($sql);
	}
}
