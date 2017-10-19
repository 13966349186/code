<?php
class IOSS_OrderProductGold extends IOSS_OrderProduct {
	const table = 'order_product_gold';
	protected $gold_num;
	protected $discount;
	
	public function __get($prop){ return $this->{$prop}; }
	
	protected function __construct(){	}
	
	/**
	 * (non-PHPdoc)
	 * @see IOSS_OrderProduct::_initFromVo()
	 */
	protected function _initFromVo($vo){
		parent::_initFromVo($vo);
		if($row = IOSS_DB::getInstance()->get_where(self::table, Array('order_product_id'=>$this->id))->row()){
			$this->gold_num = $row->gold_num;
			$this->discount = $row->discount;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IOSS_OrderProduct::_initFromProduct()
	 */
	protected function _initFromProduct(IOSS_Product $product, $num){
		parent::_initFromProduct($product, $num);
		$this->gold_num = $product->gold_num;
		$this->discount = $product->discount;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IOSS_OrderProduct::save()
	 */
	public function save($order_id = null){
		$db = IOSS_DB::getInstance();
		$row = array('order_product_id'=>$this->id, 'gold_num'=>$this->gold_num, 'discount'=>$this->discount);
		if(parent::save($order_id)){
			if($row['order_product_id']){
				return $db->where('order_product_id',$row['order_product_id'])->update(self::table, $row);
			}else{
				$row['order_product_id'] = $this->id;
				return $db->insert(self::table, $row);
			}
		}
		return false;
	}
}
