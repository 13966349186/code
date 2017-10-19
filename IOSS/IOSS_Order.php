<?php
class IOSS_Order {
	const table = 'orders';
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
	
	protected $id;
	protected  $no;
	protected $site_id;
	protected $site_name;
	protected $user_id;
	protected $user_full_name;
	protected $user_email;
	protected $user_phone;
	protected $user_ip;
	protected $user_agent;
	protected $user_state;
	protected $refer_url;
	protected $game_id;
	protected $game_name;
	protected $product_type;
	protected $currency;
	protected $amount;
	protected $type;
	protected $note;
	protected $risk;
	protected $state;
	protected $hold_reason;
	protected $payment_method;
	protected $payment_state;
	protected $payment_time;
	protected $delivery_state;
	protected $delivery_time;
	protected $create_time;
	protected $update_time;

	private $order_products;
	private $order_attributes;
	private $order_attributes_updated = false;
	private $order_index_cols = array('user_email','user_phone','user_ip','no');
	private $order_index_updated = false;

	public static $states = Array(self::STATE_UNSUBMITTED=>'unpaid', self::STATE_OPEN=>'processing', self::STATE_HOLDING=>'on hold', self::STATE_CLOSED=>'completed', self::STATE_CANCELLED=>'cancelled');
	public static $paymentStates = Array(self::PAY_STATE_UNPAIED=>'unpaid', self::PAY_STATE_PEDING=>'pending', self::PAY_STATE_PAID=>'paid', self::PAY_STATE_REFUNDED=>'refunded', self::PAY_STATE_PART=>'partially paid', self::PAY_STATE_REVERSED=>'hold');
	public static $deliveryStates = Array(self::DELEVERY_STATE_NOT_DELEVERED=>'processing', self::DELEVERY_STATE_PART_DELEVERED=>'delivered', self::DELEVERY_STATE_DELEVERED=>'delivered');
	
	public function __get($prop){ return $this->{$prop}; }

	/**
	 * 生成新订单并插入数据库
	 * @param IOSS_Site $site 网站
	 * @param IOSS_OrderCustomer $customer 顾客信息(姓名电话神马的)
	 * @param IOSS_Currency $currency 货币
	 * @param IOSS_PaymentMethod $payment_method 支付方式
	 * @param IOSS_Type $type 商品类型
	 * @param Array $order_attributes 订单发货属性
	 * @param Array $products 商品列表
	 * @param Array $order_log 订单日志信息 / 备注
	 */
	public static function create(IOSS_Site $site, IOSS_OrderCustomer $customer, IOSS_Currency $currency, IOSS_PaymentMethod $payment_method, IOSS_Type $type, $order_attributes,  $products,  $order_log=''){
		$game = IOSS_Game::getGame($type->game_id);
		$amount = 0;
		foreach ($products as $v){
			$amount +=  $currency->exchage($v->price) * $v->num;
		}
		$order = new self();
		$order->product_type = $type->id;
		$order->game_id = $game->id;
		$order->game_name = $game->name;
		$order->site_id = $site->id;
		$order->site_name = $site->name;
		$order->currency = $currency->code;
		$order->no = gmdate('ymdHis', time()).rand(1000, 9999);
		$order->type = 0;
		$order->note = '';
		$order->risk = $payment_method->default_risk;
		$order->state = self::STATE_UNSUBMITTED;
		$order->hold_reason = 0;
		$order->payment_method = $payment_method->id;
		$order->payment_state = self::PAY_STATE_UNPAIED;
		$order->payment_time = 0;
		$order->delivery_state = self::DELEVERY_STATE_NOT_DELEVERED;
		$order->delivery_time = 0;
		$order->create_time = time();
		$order->update_time = time();
		$order->amount = $amount;
		$order->order_products = $products;
	
		$order->setAttributes($order_attributes);
		$order->setOrderCustomer($customer);
		$order_log = is_array($order_log)?$order_log : array('note'=>$order_log);
	
		if($order->add($order_log)){
			return $order;
		}else {
			return null;
		}
	}
	
	/**
	* @param	int $id	
	* @return   Order|NULL
	*/
	public static function getById($id){
		$db = IOSS_DB::getInstance();
		if($vo = $db->get_where(self::table, Array('id'=>$id))->row()){
			return new self($vo);
		}
		return null;
	}
	/**
	 * 根据订单号获取订单
	 * @param string $no
	 * @return IOSS_Order|NULL
	 */
	public static function getByNo($no){
		$db = IOSS_DB::getInstance();
		if($vo = $db->get_where(self::table, Array('no'=>$no))->row()){
			return new self($vo);
		}
		return null;
	}
		
	private function __construct($vo = null){
		if($vo){
			foreach ($vo as $k=>$v){
				if(property_exists($this, $k)){
					$this->{$k} = $v;
				}
			}
		}
	}
	
	/**
	 * 创建订单表对应的 value object
	 * @param object $db
	 * @return stdClass
	 */
	private function createVo($db){
		$cols = $db->query('SHOW COLUMNS FROM ' . $db->dbprefix . self::table)->result();
		$vo = new stdClass();
		foreach($cols as $c){
			if(property_exists($this, $c->Field)) 	$vo->{$c->Field} = $this->{$c->Field};
		}
		return $vo;
	}
	
	private function createIndex(){
		foreach ($this->order_index_cols as $col){
			$index[$col] = $this->$col;
		}
		return isset($index)?$index:array();
	}

	/**
	 * 添加订单操作日志
	 * @param int $order_log
	 */
	private function addOrderLog($db, $order_log){
		$order_log['order_id'] = $this->id;
		$order_log['state'] = $this->state;
		$order_log['payment_state'] = $this->payment_state;
		$order_log['delivery_state'] = $this->delivery_state;
		$order_log['type'] = isset($order_log['type']) ? $order_log['type'] : 4;   //默认操作类型为“编辑”
		$order_log['create_time'] = time();
		$order_log['update_time'] = time();
		$order_log['admin_id'] = isset($order_log['admin_id']) ? $order_log['admin_id'] : 0;
		$order_log['admin'] = isset($order_log['admin'])? $order_log['admin'] : 'system';
		$db->insert('order_log', $order_log);
	}
	
	private function saveOrderAttribute($db, $is_new_order = false){
		if($this->order_attributes){
			$defs = IOSS_Type::getType($this->product_type)->getAttributesDef();
			foreach ($this->order_attributes as $code=>$value){
				$attributes[] = Array('order_id'=>$this->id, 'code'=>$code, 'name'=>$defs[$code]->name, 'value'=>$value);
				if($defs[$code]->index_flg){ //设置订单索引
					$index[$code] = $value;
				}
			}
		}
		if(!$is_new_order){
			$db->where('order_id', $this->id)->delete('order_attributes');
		}
		if(isset($attributes)){
			$db->insert_batch('order_attributes',$attributes);
		}
		$order_index = isset($index)?$index:array();
		$this->saveOrderIndex($db, $order_index, 'order_attributes', $is_new_order);
	}
	
	/**
	 * 更新订单索引
	 * @param array $index  订单索引数组
	 * @param string $table_name  表名
	 * @return boolean
	 */
	private function saveOrderIndex($db, $index, $table_name, $is_new_order = false){
		foreach ($index as $k=>$v){
			$rows[] = Array('order_id'=>$this->id, 'table_name'=>$table_name, 'col_name'=>$k, 'col_value'=>$v);
		}
		if(!$is_new_order){
			$db->where('table_name', $table_name)->where('order_id', $this->id)->delete('order_index');
		}
		if(isset($rows)){
			$db->insert_batch('order_index', $rows);
		}
	}

	/**
	 * 插入订单数据
	 * @param array $log
	 */
	private function add($log = array()){
		$db = IOSS_DB::getInstance();
		$this->update_time = time();
		$vo= $this->createVo($db);
		if($db->insert(self::table, $vo)){
			$this->id = $db->insert_id();
			$this->saveOrderIndex($db,  $this->createIndex(), self::table, true);
			$this->saveOrderAttribute($db, true);
			$this->addOrderLog($db, $log);
			foreach ($this->order_products as $v){
				$v->save($this->id);
			}
			$this->order_index_updated = false;
			$this->order_attributes_updated = false;
			return true;
		}
		return false;
	}
	
	/**
	 * 更新订单数据
	 * @param array $log
	 * @return boolean
	 */
	public function update($log = array()){
		$db = IOSS_DB::getInstance();
		$this->update_time = time();
		$vo= $this->createVo($db);
		$sucess = $db->where('id', $this->id)->update(self::table, $vo) && ($db->affected_rows() >= 1);
		if($sucess){
			if($this->order_index_updated){
				$order_index = $this->createIndex();
				$this->saveOrderIndex($db, $order_index, self::table);
				$this->order_index_updated = false;
			}
			//订单发货属性更新
			if($this->order_attributes_updated){
				$this->saveOrderAttribute($db);
				$this->order_attributes_updated = false;
			}
			$this->addOrderLog($db, $log);
			return true;
		}
		return false;
	}
	
	/**
	 * 设置用户信息
	 * @param IOSS_OrderCustomer $customer
	 */
	public function setOrderCustomer(IOSS_OrderCustomer $customer){
		foreach ($customer as $k=>$v){
			if(property_exists($this, $k)){
				$this->{$k} = $v;
				if(in_array($k, $this->order_index_cols)){
					$this->order_index_updated = true;
				}
			}
		}
	}
	
	/**
	 * 获取订单商品列表
	* @return   array
	*/
	public function getProducts(){
		if($this->order_products == null){
			$this->order_products = IOSS_OrderProduct::getAll($this->id);
		}
		return $this->order_products;
	}
	
	/**
	 * 获取订单发货信息
	* @return   array
	*/
	public function getAttributes(){
		if($this->order_attributes == null){
			$db = IOSS_DB::getInstance();
			$this->order_attributes = Array();
			$db->order_by('id');
			$db->where('order_id', $this->id);
			if($lst = $db->get('order_attributes')->result()){
				foreach ($lst as $v){
					$this->order_attributes[$v->code] = $v->value;
				}
			}
		}
		return $this->order_attributes;
	}
	
	/**
	 *  设置订单发货信息
	 * @param array $attribute
	 */
	public function setAttributes($attributes){
		$defs = IOSS_Type::getType($this->product_type)->getAttributesDef();
		$this->order_attributes_updated = true;
		$this->order_attributes = array_intersect_key($attributes, $defs);
	}
}
