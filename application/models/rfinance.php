<?php
/**
 * 财务报表查询
 * @author lifw
 */
class RFinance extends MY_Model {
	private $filter = array();
	public $type = null;
	public $sub_id = null;
	function __construct() {
        parent::__construct();
        $this->load->model('MOrder');
        $this->load->model('MOrderPayment');
	}
	public function getList($limit=null){
		$this->db->select('sum( if ('.$this->db->dbprefix('order_payment').'.state='.MOrderPayment::STATE_COMPLETED.', '.$this->db->dbprefix('order_payment').'.amount, 0) ) as amount', false);
		$this->db->select('sum( if ('.$this->db->dbprefix('order_payment').'.state='.MOrderPayment::STATE_COMPLETED.', '.$this->db->dbprefix('order_payment').'.fee, 0) ) as fee', false);
		$this->db->select('count(distinct '.$this->db->dbprefix('order_payment').'.order_id) as num');
		$this->db->select('order_payment.currency');
		$this->db->from('order_payment');
		$this->db->join('orders', 'order_payment.order_id=orders.id');
		$this->db->group_by('order_payment.currency');
		$this->formfilter->doFilter();
		if($this->type == 'site'){
			$this->db->select('orders.site_id');
			$this->db->group_by('orders.site_id');
			if($this->sub_id){
				$this->db->where('orders.site_id', $this->sub_id);
				$this->db->select('orders.game_id');
				$this->db->group_by('orders.game_id');
				$this->db->order_by('orders.game_id');
			}else{
				$this->db->order_by('orders.site_id');
			}
		}else{
			$this->db->select('orders.game_id');
			$this->db->group_by('orders.game_id');
			if($this->sub_id){
				$this->db->where('orders.game_id', $this->sub_id);
				$this->db->select('orders.site_id');
				$this->db->group_by('orders.site_id');
				$this->db->order_by('orders.site_id');
			}else{
				$this->db->order_by('orders.game_id');
			}
		}
		$this->db->order_by('order_payment.currency');
		$this->db->where('orders.state <>', MOrder::STATE_UNSUBMITTED);
		if($limit){
			$this->db->limit($limit['limit'],$limit['offset']);
		}
		return $this->db->get()->result();
	}
}
