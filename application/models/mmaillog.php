<?php
/**
 * 后台邮件日志表操作模型
 * @author lifw
 */
class MMailLog extends MY_Model {
	protected $table = 'mail_log';
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
	
	public function  getTitles($where=null){
		$this->db->select("id,site_id, tmp_code, email_from, email_replay_to,email_to,email_subject,send_state,create_time");
		$this->db->from($this->table);
		if($where != null){
			$this->db->where($where);
		}
		return $this->db->get()->result();
	}

}
