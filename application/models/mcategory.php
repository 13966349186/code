<?php
/**
 * 后台目录表操作模型
 * @author lifw
 */
class MCategory extends MY_Model {

	const STATE_ENABLE = 1;
	const STATE_DISABLE = 0;
	protected $table = 'category';
	private $filter = array();

    function __construct() {
        parent::__construct();
    }
	public function getList($limit){
		$this->db->select($this->table.'.*');
		$this->db->from($this->table);
		$this->db->limit($limit['limit'],$limit['offset']);
		$this->db->order_by($this->table.'.id', 'desc');
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

	public function getById($id){
		$query = $this->db->where('id',$id)->get($this->table);
		return $query->row();
	}
	
	/**
	 * 删除目录
	 * @param int $id
	 * @param string $update_time
	 * @return boolean
	 */
	public function delete($id,$update_time = NULL){
		//非空的目录不能删除
		$this->load->Model('MProduct');
		$query = $this->db->limit(1)->get_where($this->table, array('parent_id' => $id));
		$has_subcat = $query->num_rows() === 1;
		$query = $this->db->limit(1)->get_where($this->MProduct->table, array('category_id' => $id));
		$has_product = $query->num_rows() === 1;
		if($has_subcat || $has_product){
			return false;
		}
		$this->db->where('id', $id);
		if($update_time !== NULL){
			$this->db->where('update_time', $update_time);
		}
		return $ret = $this->db->delete($this->table);
	}
	/**
	 * 修改目录状态
	 * @param int $id
	 * @param int $state
	 * @param int $update_time
	 * @return boolean
	 */
	public function setState($id,$state,$update_time = NULL){
		$vo= (Object)Array('id'=>$id,  'state'=>$state);
		if($update_time !== NULL){
			$vo->update_time = $update_time;
		}
		return $this->update($vo);
	}

}
