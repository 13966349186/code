<?php
/**
 * 后台管理员登录日志表操作模型
 * @author lifw
 */
class MAdminLogin extends MY_Model {

	protected $table = 'admin_login';
	
    function __construct() {
        parent::__construct();
    }
    
	public function getList($limit){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->limit($limit['limit'],$limit['offset']);
		$this->formfilter->doFilter();
		$this->db->order_by('id','desc');
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
