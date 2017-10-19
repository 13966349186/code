<?php
/**
 * 内容管理-目录操作模型
 * @author lifw
 */
class MCmsCategory extends MY_Model {
	public $table = 'cms_category';
	private $filter = array();
	function __construct() {
		parent::__construct();
	}
	public function getList($limit){
		$this->db->select($this->table.'.*, site.name as site_name, cms_model.name as model_name, cat1.name as parent_name');
		$this->db->from($this->table);
		$this->db->join('site', $this->table.'.site_id=site.id','left');
		$this->db->join('cms_model', $this->table.'.model_id=cms_model.id','left');
		$this->db->join($this->table.' as cat1', $this->table.'.parent_id=cat1.id', 'left');
		$this->db->limit($limit['limit'], $limit['offset']);
		$this->db->order_by('site_id', 'asc');
		$this->db->order_by('disp_sort', 'asc');
		$this->formfilter->doFilter();
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
	public function delete($id, $update_time){
		$this->db->where('id', $id);
		$this->db->where('update_time', $update_time);
		$ret = $this->db->delete($this->table);
		if($ret || $this->db->affected_rows() >= 1){
			return true;
		}
		return false;
	}
	
	public function getListBySiteId($site_id){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('site_id', $site_id);
		$this->db->order_by('disp_sort', 'asc');
		return $this->db->get()->result();
	}
}
