<?php
/**
 * 后台邮件模板表操作模型
 * @author lifw
 */
class MMailTemplate extends MY_Model {
	/** 可用 */
	const STATE_ENABLED = 1;
	/** 禁用 */
	const STATE_DISABLED = 0;
	protected $table = 'mail_template';
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
		return $this->db->get()->result();
	}
	public function getCount(){
		$this->db->select('count(*) as num');
		$this->db->from($this->table);
		$this->formfilter->doFilter();
		$tmp = $this->db->get()->result();
		return (int)$tmp[0]->num;
	}
}
