<?php
/**
 * 后台订单商品表操作模型
 * @author heyi
 */
class MOrderProduct extends MY_Model {
	protected $table = 'order_product';
	function __construct() {
		parent::__construct();
	}
	/**
	 * 订单列表查询
	 * @param unknown_type $limit 分页参数
	 */
	public function getList($limit){
		$this->db->select($this->table.'.*, orders.no, orders.site_id, orders.site_name, orders.game_id, orders.game_name,  orders.currency, orders.risk, orders.state,  orders.payment_state, orders.create_time');
		$this->db->from($this->table);
		$this->db->join('orders', $this->table.'.order_id=orders.id', 'left');
		if($limit){
			$this->db->limit($limit['limit'],$limit['offset']);
		}
		$this->formfilter->doFilter();
		$this->db->order_by($this->table.'.id', 'desc');
		$query = $this->db->get();
		return $query->result();
	}
	/** 订单列表计数 */
	public function getCount(){
		$this->db->select('count(distinct '.$this->db->dbprefix($this->table).'.id) as num');
		$this->db->from($this->table);
		$this->db->join('orders', $this->table.'.order_id=orders.id', 'left');
		$this->formfilter->doFilter();
		$tmp = $this->db->get()->result();
		return (int)$tmp[0]->num;
	}
	public function getListByOrderId($order_id){
		$this->db->select($this->table.'.*');
		$this->db->from($this->table);
		$this->db->where($this->table.'.order_id', (int)$order_id);
		return $this->db->get()->result();
	}
	public function getDtlById($order_product_id){
		$this->db->select($this->table.'.*, game.code as game_code, game.name as game_name, category.name as category_name, types.name as type_name,types.code as type_code, types.model');
		$this->db->from($this->table);
		$this->db->join('category', $this->table.'.category_id=category.id', 'left');
		$this->db->join('types', 'types.id='.$this->table.'.type', 'left');
		$this->db->join('game', 'types.game_id=game.id', 'left');
		$this->db->where($this->table.'.id', $order_product_id);
		return $this->db->get()->row();
	}
	/**
	 * 订单发货
	 * @param 订单商品 $vo
	 * @param 订单 $order
	 * @param 订单操作记录 $log
	 * @return boolean
	 */
	public function delivery($vo,$order, $log = NULL){
		if($order->id != $vo->order_id){
			return false;
		}
		$this->load->model('MOrder');
		$this->load->model('MOrderLog');
		$vo->delivery_time = time();
		$this->db->trans_start();
		if(parent::save($vo)){
			$order->delivery_state = $this->calcOrderDeliveryState($vo->order_id);
			$order->delivery_time = time();
			if($log){
				$log->type = MOrderLog::TYPE_DELEVERY;
			}
			$this->MOrder->save($order, $log, false);
		}
		return $this->db->trans_complete();
	}
	/**
	 * 修改购物车
	 * @param object $order 订单信息
	 * @param array $remove_arr 要删除的订单商品
	 * @param array $add_arr 要添加的订单商品
	 */
	public function updateCart($order, $remove_arr, $add_arr, $log){
		$this->db->trans_start();
		$this->load->model('MType');
		foreach ($remove_arr as $del_item){
			$type = $this->MType->getOne(Array('id'=>$del_item->type));
			$modelName = 'MOrderProduct'.$type->model;
			$this->load->model($modelName);
			if(!$this->$modelName->deleteCart($del_item)){
				$this->db->_trans_status = FALSE;
				break;
			}
		}
		if($this->db->_trans_status){
			foreach ($add_arr as $add_item){
				$add_item->order_id = $order->id;
				$type = $this->MType->getOne(Array('id'=>$add_item->type_id));
				$modelName = 'MOrderProduct'.$type->model;
				$this->load->model($modelName);
				if(!$this->$modelName->addCart($add_item)){
					$this->db->_trans_status = FALSE;
					break;
				}
			}
		}
		if($this->db->_trans_status){
			$order->delivery_state = $this->calcOrderDeliveryState($order->id);
			$this->db->select('sum(num*price) as price');
			$this->db->from($this->table);
			$this->db->where('order_id', $order->id);
			$tmp = $this->db->get()->result();
			$order->amount = $tmp[0]->price;
			if(!$this->MOrder->save($order, $log)){
				$this->db->_trans_status = FALSE;
			}
		}
		return $this->db->trans_complete();
	}
	/**
	 * 查询订单商品列表发货状态，计算出订单的状态
	 * @param integer $order_id 订单标识
	 */
	public function calcOrderDeliveryState($order_id){
		$lst = $this->getAll(Array('order_id'=>$order_id));
		$nums = Array(MOrder::DELEVERY_STATE_NOT_DELEVERED=>0, MOrder::DELEVERY_STATE_DELEVERED=>0, MOrder::DELEVERY_STATE_PART_DELEVERED=>0);
		foreach ($lst as $v){
			if(array_key_exists($v->delivery_state, $nums)){
				$nums[$v->delivery_state] += 1;
			}else{
				$nums[$v->delivery_state] = 1;
			}
		}
		if($nums[MOrder::DELEVERY_STATE_PART_DELEVERED] > 0 || ($nums[MOrder::DELEVERY_STATE_DELEVERED] > 0 && $nums[MOrder::DELEVERY_STATE_NOT_DELEVERED] > 0)){
			//部分
			return MOrder::DELEVERY_STATE_PART_DELEVERED;
		}else{
			if($nums[MOrder::DELEVERY_STATE_DELEVERED] < 1){
				//未发
				return MOrder::DELEVERY_STATE_NOT_DELEVERED;
			}else{
				//完成
				return MOrder::DELEVERY_STATE_DELEVERED;
			}			
		} 
	}
}
