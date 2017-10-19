<?php
class IOSS_Product {
	const RECOMMEND = 2;
    const ENABLED = 1;
    const DISABLED = 0;
    const table = 'product';
    
	public  $id;
	public $category_id;
	public $type_id;
	public $name;
	public $description;
	public $price;
	public $sort;
	public $state;
	
	protected function __construct($vo){
		$this->id = $vo->id;
		$this->category_id = $vo->category_id;
		$this->type_id = $vo->type_id;
		$this->name = $vo->name;
		$this->description = $vo->description;
		$this->price = $vo->price;
		$this->sort = $vo->sort;
		$this->state = $vo->state;
	}
	
	/**
	 * 从数据库加载数据
	 * @param int $category_id
	 * @param string $type_id
	 * @return array
	 */
	private static function load($category_id, $type_id=null){
		$db = IOSS_DB::getInstance();
		$db->select(self::table.'.*, types.model');
		$db->from(self::table);
		$db->join('category', self::table.'.category_id=category.id');
		$db->join('types', self::table.'.type_id=types.id');
		$db->join('game', 'types.game_id=game.id');
		if($type_id !== null){
			$db->where(self::table.'.type_id', $type_id);
		}
		$db->where(self::table.'.category_id', $category_id);
		$db->where(self::table.'.state <>', self::DISABLED);
		$db->where('types.state =', IOSS_Type::ENABLED);
		$db->where('game.state =', IOSS_Game::ENABLED);
		$db->order_by(self::table.'.sort');
		return  $db->get()->result();
	}
	
	/**
	 * @param	int $id	
	 * @return   Product
	 */
	public static function getProduct($id){
        $cache = IOSS_GameCache::getInstance();
        $cacheKey = __CLASS__.'::'.__FUNCTION__.'-'.$id.'';
        if($rtn = $cache->get($cacheKey)){
        	return $rtn;
        }
		$db = IOSS_DB::getInstance();
		$db->select(self::table.'.*, types.model');
		$db->from(self::table)->join('types', self::table.'.type_id=types.id');
		$db->where(self::table.'.id', $id)->where(self::table.'.state <>', self::DISABLED)->where('types.state =', IOSS_Type::ENABLED);
		if(($row = $db->get()->row()) && $row->model){
			$className = __CLASS__ . ucfirst($row->model);
			$rtn = new $className($row);
			$cache->set($cacheKey, $rtn);
			return $rtn;
		}
        return null;
	}
	
	
	/**
	 * 获取商品基础信息列表, 返回结果为IOSS_Product对象数组
	 * @param int $category_id
	 * @param int $type_id
	 * @return array
	 */
	public static function getList($category_id, $type_id=null){
		$cache = IOSS_GameCache::getInstance();
		$cacheKey = __CLASS__.'::'.__FUNCTION__.'('.$category_id.','.$type_id.')';
		if(!($rtn = $cache->get($cacheKey))){
			$rtn = array();
			$rows = self::load($category_id, $type_id);
			foreach ($rows as $row){
				$rtn[] = new self($row);
			}
			$cache->set($cacheKey, $rtn);
		}
		return $rtn;
	}

	
	/**
	 * 获取所有商品详细信息, 返回结果为IOSS_Product对应子类的对象
	 * @param int $category_id
	 * @param int $type_id
	 * @return array
	 */
	public static function getProducts($category_id, $type_id=null){
        $cache = IOSS_GameCache::getInstance();
        $cacheKey = __CLASS__.'::'.__FUNCTION__.'('.$category_id.','.$type_id.')';
        if(!($rtn = $cache->get($cacheKey))){
        	$rtn = array();
        	$rows = self::load($category_id, $type_id);
        	foreach ($rows as $row){
        		$className = __CLASS__.ucfirst($row->model);
        		$item = new $className($row);
        		$rtn[$row->id] = $item;
        	}
        	$cache->set($cacheKey, $rtn);
        }
        return $rtn;
	}
}
