<?php
/**
 * 后台订单商品附加信息表操作模型
 * @author lifw
 */
class MOrderPayment extends MY_Model {
	protected $table = 'order_payment';
	/** 状态：处理中 */
	const STATE_PENDING = 0;
	/** 状态：完成 */
	const STATE_COMPLETED = 1;
	/** 状态：取消 */
	const STATE_CANCELLED = -1;
	/** 类型：付款 */
	const TYPE_PAY = 1;
	/** 类型：退款 */
	const TYPE_REFUND = 2;
	/** 类型：冻结 */
	const TYPE_CHARGEBACK = 3;
	function getState($state=''){
		$rtn = Array(self::STATE_PENDING=>'Pending', self::STATE_COMPLETED=>'Completed ', self::STATE_CANCELLED=>'Canceled');
		if($state === ''){
			return $rtn;
		}
		return $rtn[$state];
	}
	
	function getType($type=''){
		$rtn = Array(self::TYPE_PAY=>'付款', self::TYPE_REFUND=>'退款 ', self::TYPE_CHARGEBACK=>'冻结');
		if($type === ''){
			return $rtn;
		}
		return $rtn[$type];
	}
	
	function __construct() {
		parent::__construct();
	}
	public function getList($limit=null, $asc = FALSE){
		$this->db->select($this->table.'.*, orders.site_id as site_id');
		$this->db->from($this->table);
		$this->db->join('orders', $this->table.'.order_id=orders.id', 'left');
		if($limit){
			$this->db->limit($limit['limit'],$limit['offset']);
		}
		$this->formfilter->doFilter();
		if($asc){
			$this->db->order_by($this->table.'.id','asc');
		}else{
			$this->db->order_by($this->table.'.id','desc');
		}
		$query = $this->db->get();
		return $query->result();
	}
	public function getCount(){
		$this->db->select('count(*) as num');
		$this->db->from($this->table);
		$this->db->join('orders', $this->table.'.order_id=orders.id', 'left');
		$this->formfilter->doFilter();
		$tmp = $this->db->get()->result();
		return (int)$tmp[0]->num;
	}
	
	/**
	 * 统计某条订单的付款总金额
	 * @param int $order_id
	 * @return number
	 */
	function getTotalAmount($order_id){
		$this->db->select('sum(amount) as amount');
		$this->db->from($this->table);
		$this->db->where('order_id', $order_id);
		$this->db->where('state', self::STATE_COMPLETED);
		$res = $this->db->get()->row();
		return $res?$res->amount:0;
	}
	
	//根据transcation_id获取记录
	function getByTxnId($txn_id){
		$this->db->select($this->table.'.*');
		$this->db->from($this->table);
		$this->db->where($this->table.'.transcation_id', $txn_id);
		$query = $this->db->get();
		return $query->row();
	}
	
	/** 付/退款 */
	function save(&$vo,$order=NULL, $log=NULL){
		$this->load->model('MOrder');
		$this->load->model('MOrderLog');
		$order = ($order === null)?$this->MOrder->getOne(Array('id'=>$vo->order_id)):$order;
		if(!$order || $vo->order_id != $order->id){
			return false;
		}
		$this->db->trans_start();
		if(parent::save($vo)){
			$t = $this->total($vo->order_id);
			//计算当期订单的支付状态 payment_state
			if($vo->type == MOrderPayment::TYPE_PAY && $vo->state == MOrderPayment::STATE_PENDING && $t->num == 0){
				$order->payment_state = MOrder::PAY_STATE_PEDING;
			}else if($vo->type == MOrderPayment::TYPE_CHARGEBACK && $vo->state == MOrderPayment::STATE_PENDING && $t->amount > 0){
				$order->payment_state = MOrder::PAY_STATE_REVERSED;
			}else{
				$order->payment_state = $this->paymentState($t->amount, $order->amount);
			}
			//订单入库
			$order->payment_time = time();
			if($log){
				$log->type = MOrderLog::TYPE_PAY;
			}
			$this->MOrder->save($order,$log,FALSE);			
		}
		return $this->db->trans_complete();
	}
	
	/**
	 * 获取指定订单的支付记录汇总
	 * @param int $order_id
	 * @return object
	 */
	private function total($order_id){
		$this->db->select("sum(amount) as amount, count(*) as num", false);
		$this->db->from($this->table);
		$this->db->where('order_id', $order_id);
		$this->db->where('state', self::STATE_COMPLETED);
		return $this->db->get()->row();
	}
	/**
	 * 根据当前的付款总额和订单金额计算订单的支付状态
	 * @param float $total_payment
	 * @param float $order_amount
	 * @param int $chargeback
	 * @return int 订单支付状态
	 */
	private function paymentState($total_payment, $order_amount){
		if(float_compare($total_payment,$order_amount) >= 0){
			//总金额大于等于订单金额，订单付款状态改成已付款
			return MOrder::PAY_STATE_PAID;
		}else if((float_compare($total_payment,$order_amount) < 0) && (float_compare($total_payment,0.00) > 0)){
			//总金额大于0，小于订单金额，订单付款状态改成部分付款
			return MOrder::PAY_STATE_PART;
		}else{
			//总金额小于等于0，订单付款状态改成已退款
			return MOrder::PAY_STATE_REFUNDED;
		}
	}
}
