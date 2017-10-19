<?php
/**
 * 后台网站参数定义表操作模型
 * @author lifw
 */
class MSiteConfigDef extends MY_Model {

	protected $table = 'site_config_def';
	private $filter = array();

    function __construct() {
        parent::__construct();
    }
	public function getAll($where=null){
		$this->db->select("*");
		$this->db->from($this->table);
		if($where != null){
			$this->db->where($where);
		}
		$this->db->order_by('group_id');
		$this->db->order_by('sort_num');
		return $this->db->get()->result();
	}
  	public function getlist($id){
		$this->db->select("*");
		$this->db->from($this->table);
		if($id!= null){
			$this->db->where('sid',$id);
		}
		$this->db->order_by('group_id');
		$this->db->order_by('sort_num');
		return $this->db->get()->result();
	} 
	
	/**
     * 根据网站标识取配置参数
     * @param int $Id
     */
	public function getConfigDef($siteId){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('site_id',(int)$siteId);
		$this->db->or_where('site_id =', 0);
		$this->db->order_by('group_id, sort_num');
		$query = $this->db->get();
		return $query->result();
	}
}
