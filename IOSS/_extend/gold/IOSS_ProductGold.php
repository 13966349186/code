<?php

class IOSS_ProductGold extends IOSS_Product {
	const MODEL = 'gold';
	public  $gold_num;
	public $discount;

	protected function __construct($row){
		parent::__construct($row);
		$dtl = property_exists($row, 'product_id')?$row:IOSS_DB::getInstance()->get_where('product_gold', Array('product_id'=>$row->id))->row();
		$this->gold_num =$dtl->gold_num;
		$this->discount = $dtl->discount;
	}
	

	/**
	 *  获取所有商品详细信息, 返回结果为IOSS_ProductGold 对象数组
	 * @param int $category_id
	 * @param string $type_id
	 * @return multitype:IOSS_ProductGold
	 */
	public static function getProducts($category_id, $type_id=null){
		$cache = IOSS_GameCache::getInstance();
		$cacheKey = __CLASS__.'::'.__FUNCTION__.'('.$category_id.','.$type_id.')';
		$rtn = $cache->get($cacheKey);
		if(($rtn = $cache->get($cacheKey)) === false){
			$db = IOSS_DB::getInstance();
			$db->select(parent::table.'.*, product_gold.*');
			$db->from(parent::table);
			$db->join('product_gold', parent::table.'.id=product_gold.product_id');
			$db->join('category', parent::table.'.category_id=category.id');
			$db->join('types', parent::table.'.type_id=types.id');
			$db->join('game', 'types.game_id=game.id');
			if($type_id !== null){
				$db->where(parent::table.'.type_id', $type_id);
			}
			$db->where(parent::table.'.category_id', $category_id);
			$db->where(parent::table.'.state <>', self::DISABLED);
			$db->where('types.state =', IOSS_Type::ENABLED);
			$db->where('game.state =', IOSS_Game::ENABLED);
			$db->order_by(parent::table.'.sort');
			$rows = $db->get()->result();
			$rtn = array();
			foreach ($rows as $row){
				$rtn[$row->id] = new IOSS_ProductGold($row);
			}
			$cache->set($cacheKey, $rtn);
		}
		return $rtn;
	}
	
}
