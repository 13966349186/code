<?php
/**
 * 订单操作日志统计
 * @author zhangyk
 */
class ROrderLog extends MY_Model {
	private $table = 'order_log';
	public $group = null;
	
	function __construct() {
		parent::__construct();
		$this->load->model('MOrderLog');
	}
	
	function getVerifyList($group = ''){
		$this->db->query("set time_zone = '{$this->time_zone}'");
		$this->formfilter->doFilter();
		$this->db->select('COUNT(DISTINCT order_id) as num');
		$this->db->from($this->table);
		switch ($group){
			case 'm':
				$this->db->select("FROM_UNIXTIME(create_time,'%Y-%m') as title", false);
				$this->db->group_by('title');
				break;
			case 'w':
				$this->db->select("FROM_UNIXTIME(create_time,'%Y-W%U') as title", false);
				$this->db->group_by('title');
				break;
			case 'd':
				$this->db->select("FROM_UNIXTIME(create_time,'%Y-%m-%d') as title", false);
				$this->db->group_by('title');
				break;
			default:
				$this->db->select('admin as title');
				$this->db->group_by('admin_id');				
		}
		$this->db->where('type', MOrderLog::TYPE_VERIFY);
		return $this->db->get()->result();
	}
}