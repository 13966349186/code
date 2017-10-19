<?php
/**
 * FIFA报表查询
 * @author lifw
 */
class RFIFA extends MY_Model {
	private $filter = array();
	public $game_id = null;
	public $category_id = null;
	function __construct() {
        parent::__construct();
        $this->load->model('MOrderPayment');
    }
	public function getList(){
		$this->db->select('sum('.$this->db->dbprefix('order_product').'.price * '.$this->db->dbprefix('order_product').'.num) as amount', false);
		$this->db->select('sum('.$this->db->dbprefix('order_product_gold').'.gold_num) as gold_num', false);
		$this->db->select('count(distinct '.$this->db->dbprefix('orders').'.id) as num');
		$this->db->from('orders');
		$this->db->join('order_product', 'order_product.order_id=orders.id');
		$this->db->join('order_product_gold', 'order_product_gold.order_product_id=order_product.id');
		$this->db->join('types', 'order_product.type=types.id');
		$this->formfilter->doFilter();
		if($this->category_id){
			$this->db->where('order_product.category_id', $this->category_id);
			$this->db->group_by('order_product.type');
			$this->db->select('order_product.type as type_id, types.name as type_name');
		}else{
			$this->db->group_by('order_product.category_id');
			$this->db->select('order_product.category_id');
			$this->db->select('order_product.category_name');
		}
		$this->db->where('orders.state <>', MOrder::STATE_UNSUBMITTED);
		$this->db->where('orders.game_id', $this->game_id);
		return $this->db->get()->result();
	}
	public function getAmountSum($currency){
		$this->db->select('sum('.$this->db->dbprefix('order_payment').'.amount) as amount', false);
		$this->db->from('orders');
		$this->db->join('order_product', 'order_product.order_id=orders.id');
		$this->db->join('order_payment', 'order_payment.order_id=orders.id');
		$this->db->join('types', 'order_product.type=types.id');
		$this->formfilter->doFilter();
		if($this->category_id){
			$this->db->where('order_product.category_id', $this->category_id);
			$this->db->group_by('order_product.type');
			$this->db->select('order_product.type as type_id, types.name as type_name');
		}else{
			$this->db->group_by('order_product.category_id');
			$this->db->select('order_product.category_id');
			$this->db->select('order_product.category_name');
		}
		$this->db->where('order_payment.state', MOrderPayment::STATE_COMPLETED);
		$this->db->where('order_payment.currency', $currency);
		$this->db->where('orders.state <>', MOrder::STATE_UNSUBMITTED);
		$this->db->where('orders.game_id', $this->game_id);
		return $this->db->get()->result();
	}
}
