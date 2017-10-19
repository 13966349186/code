<?php
/**
 * 类型定义表操作模型
 * @author lifw
 */
class MType extends MY_Model {

	const STATE_ENABLE = 1;
	const STATE_DISABLE = 0;
	protected $table = 'types';
	private $filter = array();
	public $states = array(self::STATE_DISABLE=>'禁用',self::STATE_ENABLE=>'启用');

    function __construct() {
        parent::__construct();
    }
    
	public function getList($limit){
		$this->db->select($this->table.'.*');
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

	public function getById($id){
		$query = $this->db->where('id',$id)->get($this->table);
		return $query->row();
	}
}
