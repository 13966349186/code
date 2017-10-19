<?php
class IOSS_OrderProduct {
	const table = 'order_product';
	protected $id;
	protected $order_id;
	protected $product_id;
	protected $category_id;
	protected $category_name;
	protected $type;
	protected $name;
	protected $description;
	protected $price;
	protected $num;
	protected $delivery_state;
	protected $delivery_time;
	protected $update_time;
	
	public function __get($prop){ return $this->{$prop}; }
	
	/**
	 * 返回订单所有的商品对象
	 * @param int $order_id
	 * @return array
	 */
	public static function getAll($order_id){
		$db = IOSS_DB::getInstance();
		$db->select('p.*, types.model');
		$db->from(self::table . '  p');
		$db->join('types', 'p.type=types.id');
		$db->where('p.order_id', $order_id);
		$list = array();
		foreach ($db->get()->result() as $row){
			$className = __CLASS__.ucfirst($row->model);
			unset($row->model);
			$p = new $className();
			$p->_initFromVo($row);
			$list[] = $p;
		}
		return $list;
	}
	
	/**
	 * @param int $order_product_id 订单商品ID
	 */
	public static function getById($order_product_id){
		$db = IOSS_DB::getInstance();
		$db->select(self::table.'.*, types.model');
		$db->from(self::table);
		$db->join('types', self::table.'.type=types.id');
		$db->where(self::table.'.id', $order_product_id);
		if($row = $db->get()->row()){
			$className = __CLASS__.ucfirst($row->model);
			unset($row->model);
			$p = new $className($row);
			$p->_initFromVo($row);
			return $p;
		}
		return null;
	}
	
	/**
	 * @param IOSS_Product $product 商品
	 */
	public static function create(IOSS_Product $product, $num){
		if(!($type = IOSS_Type::getType($product->type_id))){
			return null;
		}
		$className = __CLASS__.ucfirst($type->model);
		$p = new $className();
		$p->_initFromProduct($product, $num);
		return $p;
	}
	
	private function __construct(){	}
	
	/**
	 * 已经生成的订单，从数据库加载数据，参数为vo对象
	 * @param unknown $vo
	 */
	protected function _initFromVo($vo){
		foreach ($vo as $k=>$v){
			if(property_exists(__CLASS__, $k)){
				$this->{$k} = $v;
			}
		}
	}
	
	/**
	 * 由商品生成订单商品
	 * @param IOSS_Product $product
	 */
	protected function _initFromProduct(IOSS_Product $product, $num){
		$category = IOSS_Category::getCategory($product->category_id);
		$this->product_id = $product->id;
		$this->name = $product->name;
		$this->category_id = $product->category_id;
		$this->category_name = $category->name;
		$this->type = $product->type_id;
		$this->description = $product->description;
		$this->price = $product->price;
		$this->delivery_state = IOSS_Order::DELEVERY_STATE_NOT_DELEVERED;
		$this->delivery_time = 0;
		$this->update_time = time();
		$this->description = '';
		$this->num = $num;
	}
	
	/**
	 * 保存订单商品数据入库
	 * @return   boolean
	 */
	protected function save($order_id = null){
		$this->order_id = isset($this->order_id)?$this->order_id:$order_id;
		if(!$this->order_id){
			return false;
		}
		$db = IOSS_DB::getInstance();
		$this->update_time = time();
		foreach ($this as $k=>$v){
			if(property_exists(__CLASS__, $k)){
				$row[$k] = $v;
			}
		}
		if($this->id){
			$success = $db->where('id', $row['id'])->update(self::table, $row) && ($db->affected_rows() >= 1);
		}else{
			$success = $db->insert(self::table, $row);
			$this->id = $db->insert_id();
		}
		return $success;
	}
}
