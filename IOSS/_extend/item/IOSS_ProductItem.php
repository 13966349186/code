<?php
/**
 * 
 * @author zhangyk
 *
 */
class IOSS_ProductItem extends IOSS_Product {
	const table = 'product_item';
	protected $stock;
	protected $image;
	public function __get($prop){ return $this->{$prop}; }
	protected function __construct($row){
		parent::__construct($row);
		if($dtl = IOSS_DB::getInstance()->get_where(self::table, Array('product_id'=>$row->id))->row()){
			foreach ($dtl as $k=>$v){
				if(property_exists($this, $k)){
					$this->{$k} = $v;
				}
			}
		}
	}
}
