<?php
/**
 * 后台货币表操作模型
 * @author heyi
 */
class MCurrency extends MY_Model {

	
	protected $table = 'currency';
	private $filter = array();

    function __construct() {
        parent::__construct();
    }

	public function getById($id){
		$query = $this->db->where('id',$id)->get($this->table);
		return $query->row();
	}
	public function getByCode($code){
		$query = $this->db->where('code',$code)->get($this->table);
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
}
