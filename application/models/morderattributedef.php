<?php
/**
 * 订单属性定义表操作模型
 * @author lifw
 */
class MOrderAttributeDef extends MY_Model {
	protected $table = 'order_attribute_def';
	private $filter = array();
	function __construct() {
		parent::__construct();
	}
	function getListByTypeId($type_id){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('type_id', $type_id);
		$this->db->order_by('sort', 'asc');
		$query = $this->db->get();
		return $query->result();
	}
	function update($type, $lst){
		$this->db->trans_start();
		$this->db->where('type_id', $type->id);
		foreach ($lst as $v){
			if(strlen($v->id) > 0){
				$this->db->where('id <>', $v->id);
			}
		}
		$this->db->delete($this->table);
		foreach ($lst as $v) {
			if(strlen($v->id) > 0){
				$this->db->where('id',$v->id);
				$this->db->where('update_time',$v->update_time);
				$v->update_time = time();
				if(!$this->db->update($this->table,$v) || $this->db->affected_rows() < 1){
					$this->db->_trans_status = FALSE;
					break;
				}
			}else{
				$v->update_time = time();
				$v->type_id = $type->id;
				if(!$this->db->insert($this->table,$v)){
					$this->db->_trans_status = FALSE;
					break;
				}
			}
		}
		return $this->db->trans_complete();
	}
}
