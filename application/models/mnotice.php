<?php
/**
 * 后台系统消息表操作模型
 * @author lifw
 */
class MNotice extends MY_Model {

	protected $table = 'notice';
	private $filter = array();

    function __construct() {
        parent::__construct();
    }

	public function getById($id){
		$query = $this->db->where('id',$id)->get($this->table);
		return $query->row();
	}
	public function getList($limit){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->limit($limit['limit'],$limit['offset']);
		$this->formfilter->doFilter();
		$this->db->order_by('id', 'desc');
		$query = $this->db->get();
		return $query->result();
	}
	public function getCount(){
		$this->db->select('count(*) as num');
		$this->db->from($this->table);
		$this->formfilter->doFilter();
		$tmp = $this->db->get()->result();
		return (int)$tmp[0]->num;
	}
	public function delete($id){
		$arr = $id;
		if(!is_array($id)){
			$arr = Array($id);
		}
		foreach ($arr as $v){
			$this->db->or_where('id', (int)$v);
		}
		$this->db->delete($this->table);
	}
}
