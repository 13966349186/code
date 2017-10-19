<?php
/**
 * 销售报表查询
 * @author lifw
 */
class RSales extends MY_Model {
	private $filter = array();
	public $all = null;
	public $where = null;
	public $group = null;
	
	function __construct() {
        parent::__construct();
        $this->load->model('MOrder');
    }
	public function getList($limit=null){
		$this->db->query("set time_zone = '{$this->time_zone}'");
		$this->db->select('sum('.$this->db->dbprefix('order_product').'.price * '.$this->db->dbprefix('order_product').'.num) as amount', false);
		$this->db->select('count(distinct '.$this->db->dbprefix('orders').'.id) as num');
		$this->db->select('count(distinct if('.$this->db->dbprefix('orders').'.state = '.MOrder::STATE_OPEN.', '.$this->db->dbprefix('orders').'.id, null)) as state_open', false);
		$this->db->select('count(distinct if('.$this->db->dbprefix('orders').'.state = '.MOrder::STATE_CANCELLED.', '.$this->db->dbprefix('orders').'.id, null)) as state_cancelled', false);
		$this->db->select('count(distinct if('.$this->db->dbprefix('orders').'.state = '.MOrder::STATE_CLOSED.', '.$this->db->dbprefix('orders').'.id, null)) as state_closed', false);
		$this->db->select('count(distinct if('.$this->db->dbprefix('orders').'.state = '.MOrder::STATE_HOLDING.', '.$this->db->dbprefix('orders').'.id, null)) as state_holding', false);
		$this->db->select('count(distinct if('.$this->db->dbprefix('orders').'.risk = '.MOrder::RISK_FRAUD.', '.$this->db->dbprefix('orders').'.id, null)) as risk_fraud', false);
		$this->db->select('count(distinct if('.$this->db->dbprefix('orders').'.delivery_state = '.MOrder::DELEVERY_STATE_DELEVERED.', '.$this->db->dbprefix('orders').'.id, null)) as delivered', false);
		$this->db->select('count(distinct if('.$this->db->dbprefix('orders').'.delivery_state = '.MOrder::DELEVERY_STATE_NOT_DELEVERED.', '.$this->db->dbprefix('orders').'.id, null)) as not_delivered', false);
		$this->db->select('count(distinct if('.$this->db->dbprefix('orders').'.delivery_state = '.MOrder::DELEVERY_STATE_PART_DELEVERED.', '.$this->db->dbprefix('orders').'.id, null)) as part_delivered', false);
		$this->db->from('orders');
		$this->db->join('order_product', 'order_product.order_id=orders.id');
		$this->db->join('types', 'order_product.type=types.id');
		$this->formfilter->doFilter();
		foreach ($this->where as $k=>$v){
			if($k == 'site_id' || $k == 'game_id'){
				$this->db->where('orders.'.$k, $v);
			}else{
				$this->db->where('order_product.'.$k, $v);
			}
		}
		if($this->group == 'r_week'){
			$this->db->select('FROM_UNIXTIME('.$this->db->dbprefix('orders').'.create_time,\'%Y-W%U\') as disp_name', false);
			$this->db->group_by('disp_name');
		}else if($this->group == 'r_day'){
			$this->db->select('FROM_UNIXTIME('.$this->db->dbprefix('orders').'.create_time,\'%Y%m%d\') as disp_name', false);
			$this->db->group_by('disp_name');
		}else if($this->group == 'r_month'){
			$this->db->select('FROM_UNIXTIME('.$this->db->dbprefix('orders').'.create_time,\'%Y%m\') as disp_name', false);
			$this->db->group_by('disp_name');
		}else if($this->group == 'game_id'){
			$this->db->select('orders.game_id as disp_id, orders.game_name as disp_name');
			$this->db->group_by('orders.game_id');
		}else if($this->group == 'category_id'){
			$this->db->select('order_product.category_id as disp_id, order_product.category_name as disp_name');
			$this->db->group_by('order_product.category_id');
		}else if($this->group == 'type'){
			$this->db->select('order_product.type as disp_id, types.name as disp_name');
			$this->db->group_by('order_product.type');
		}else if($this->group == 'product_id'){
			$this->db->select('order_product.product_id as disp_id, order_product.name as disp_name');
			$this->db->group_by('order_product.product_id');
		}else{
			$this->db->select('orders.site_id as disp_id, orders.site_name as disp_name');
			$this->db->group_by('orders.site_id');
		}
		$this->db->where('orders.state <>', MOrder::STATE_UNSUBMITTED);
		if($limit){
			$this->db->limit($limit['limit'],$limit['offset']);
		}
		return $this->db->get()->result();
	}
}
