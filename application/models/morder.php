<?php
/**
 * 后台订单表操作模型
 * @author lifw
 */
class MOrder extends MY_Model {
	/** 未生成订单 */
	const STATE_UNSUBMITTED = 0;
	/** 打开 */
	const STATE_OPEN = 1;
	/** 问题单 */
	const STATE_HOLDING = 2;
	/** 关闭 */
	const STATE_CLOSED = 3;
	/** 取消 */
	const STATE_CANCELLED = 4;
	/** 未付款 */
	const PAY_STATE_UNPAIED = 0;
	/** 付款中 */
	const PAY_STATE_PEDING = 1;
	/** 部分付款*/
	const PAY_STATE_PART = 2;
	/** 已付款 */
	const PAY_STATE_PAID = 3;
	/** 已退款 */
	const PAY_STATE_REFUNDED = 4;
	/** 冻结 */
	const PAY_STATE_REVERSED = 5;
	/** 未发货 */
	const DELEVERY_STATE_NOT_DELEVERED = 0;
	/** 部分发货 */
	const DELEVERY_STATE_PART_DELEVERED = 1;
	/** 已发货 */
	const DELEVERY_STATE_DELEVERED = 2;
	/** 低 */
	const RISK_LOW = 0;
	/** 中 */
	const RISK_MEDIUM = 1;
	/** 高 */
	const RISK_HIGH = 2;
	/** 欺诈 */
	const RISK_FRAUD = 3;
	
	protected $table = 'orders';
	protected $index_cols = array('no', 'user_email','user_phone','user_ip');
	/** 查询中排除未生成状态的订单 */
	public $exclude_unsubmited = true;
	
	public $states = Array(self::STATE_UNSUBMITTED=>'未生成', self::STATE_OPEN=>'打开', self::STATE_HOLDING=>'问题单', self::STATE_CLOSED=>'关闭', self::STATE_CANCELLED=>'取消');
	public $paymentStates = Array(self::PAY_STATE_UNPAIED=>'未付款', self::PAY_STATE_PEDING=>'付款中', self::PAY_STATE_PAID=>'已付款', self::PAY_STATE_REFUNDED=>'已退款', self::PAY_STATE_PART=>'部分付款', self::PAY_STATE_REVERSED=>'冻结');
	public $deliveryStates = Array(self::DELEVERY_STATE_NOT_DELEVERED=>'未发货', self::DELEVERY_STATE_PART_DELEVERED=>'部分发货', self::DELEVERY_STATE_DELEVERED=>'已发货');
	public $risks = 	 Array(self::RISK_LOW=>'通过', self::RISK_MEDIUM=>'未验证', self::RISK_HIGH=>'不通过', self::RISK_FRAUD=>'欺诈');

	
	function getState($state=''){
		if($state === ''){
			return $this->states;
		}
		return element($state, $this->states, '');
	}
	function getPayState($state=''){
		if($state === ''){
			return $this->paymentStates;
		}
		return element($state, $this->paymentStates, '');
	}
	function getDeliveryState($state=''){
		if($state === ''){
			return $this->deliveryStates;
		}
		return element($state, $this->deliveryStates, '');
	}
	function getRiskState($state=''){
		if($state === ''){
			return $this->risks;
		}
		return element($state, $this->risks, '');
	}
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * 使用订单索引表 order_index 检索
	 * @param 检索条件 $index
	 */
	private function indexWhere($index){
		$where = '';		
		//订单索引表检索
		if(is_array($index)){
			foreach ($index as $k=>$v){
				list($table_name, $col_name) =array_pad(explode('.',$k,2),2,NULL);
				if($where){
					$where .= ' or '; 
				}
				$where .= ' ('
				 .  ($table_name ? '`table_name`=\''.$this->db->escape_str($table_name).'\' and ' : '')
				 .  ($col_name ? '`col_name`=\''.$this->db->escape_str($col_name).'\' and ' : '')
				 . '`col_value`=\''.$this->db->escape_str($v).'\''
				 . ') ';
			}
		}else{
			$where = "col_value = '{$this->db->escape_str($index)}' ";
		}
		$this->db->join('order_index', "order_index.order_id = {$this->table}.id",'left');
		$this->db->where('(' . $where . ')', null, false);			
	}
	
	/**
	 * 订单列表查询
	 * @param array $limit 分页参数
	 * @param bool $sort 排序标志
	 * @param array/string $index 订单索引表检索条件
	 */
	public function getList($limit, $sort=false, $index=NULL){
		$this->db->select($this->table.'.*');
		$this->db->distinct();
		$this->db->from($this->table);
		if($limit){
			$this->db->limit($limit['limit'],$limit['offset']);
		}
		if($index){
			$this->indexWhere($index);
		}
		if($this->exclude_unsubmited){
			$this->db->where($this->table.'.state <> ', self::STATE_UNSUBMITTED);
		}
		$this->formfilter->doFilter();
		if($sort){
			$this->db->order_by($this->table.'.id', 'asc');
		}else{
			$this->db->order_by($this->table.'.id', 'desc');
		}
		$query = $this->db->get();
		return $query->result();
	}
	
	/**
	 * 关联订单列表计数
	 * @param array $indexWhere 关联索引表的条件语句
	 */
	public function getCount($index=NULL){
		$this->db->select('count(distinct '.$this->db->dbprefix($this->table).'.id) as num');
		$this->db->from($this->table);
		//订单索引表检索
		if($index){
			$this->indexWhere($index);
		}
		if($this->exclude_unsubmited){
			$this->db->where($this->table.'.state <> ', self::STATE_UNSUBMITTED);
		}
		$this->formfilter->doFilter();
		$tmp = $this->db->get()->result();
		return (int)$tmp[0]->num;
	}
	
	/**
	 * 根据风险等级统计订单数量 （按照风险等级正序排序）
	 * @param string $index  订单索引
	 * @param string $exclude_id  排除当前订单id
	 */
	public function getCountGroupByRisk($index=NULL, $exclude_id = NULL){
		if($this->exclude_unsubmited){
			$this->db->where($this->table.'.state <> ', self::STATE_UNSUBMITTED);
		}
		if($index){
			$this->indexWhere($index);
		}
		if($exclude_id !== NULL){
			$this->db->where($this->table . '.id <> ', $exclude_id);
		}
		$this->db->select('risk, count(distinct ' . $this->db->dbprefix($this->table) . '.id) as num');
		$this->db->group_by('risk');
		$this->db->from($this->table);
		$this->db->order_by('risk');
		$query = $this->db->get();
		return $query->result();
	}
	
	
	public function getById($order_id){
		$this->db->select($this->table.'.*, types.name as product_type_name, site.domain, payment_method.name as payment_method_name');
		$this->db->from($this->table);
		$this->db->join('site', $this->table.'.site_id=site.id', 'left');
		$this->db->join('types', $this->table.'.product_type=types.id', 'left');
		$this->db->join('payment_method', $this->table.'.payment_method=payment_method.id', 'left');
		
		$this->db->where($this->table.'.id', $order_id);
		$query = $this->db->get();
		return $query->row();
	}
	
	/** 更新订单信息 */
	public function save($order, $log = NULL, $update_index = TRUE){
		$this->load->model('MOrderIndex');
		$this->db->trans_start();
		$success = parent::save($order);
		if($success && $update_index){
			foreach ($this->index_cols as $col){
				if(property_exists($order,$col)){
					$this->MOrderIndex->save((object)array(
							'order_id'=>$order->id,
							'table_name'=>$this->table,
							'col_name'=>$col,
							'col_value'=>$order->{$col}
					));
				}
			}
		}
		if($success && $log){
			$this->load->model('MOrderLog');
			$log->order_id = $order->id;
			$log->payment_state = $order->payment_state;
			$log->delivery_state = $order->delivery_state;
			$log->state = $order->state;
			$log->risk = $order->risk;
			$log->create_time = $this->update_time = time();
			$this->MOrderLog->add($log);
		}
		return $this->db->trans_complete();
	}

}
