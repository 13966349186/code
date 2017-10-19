<?php
class IOSS_Category{
	const ENABLED = 1;
    const table = 'category';
	private static  $categories = array(); 
	private static  $index = array();
	
    protected $id;
	protected $game_id;
	protected $parent_id;
	protected $code;
	protected $name;
	protected $description;
	protected $state;

	public function __get($prop){ return $this->{$prop}; }

	protected function __construct($vo){
		$this->id = (int)$vo->id;
		$this->game_id = (int)$vo->game_id;
		$this->parent_id = (int)$vo->parent_id;
		$this->code = $vo->code;
		$this->name = $vo->name;
		$this->description = $vo->description;
		$this->state = (int)$vo->state;
	}
	
	private static function _load($game_id){
		$cache = IOSS_GameCache::getInstance();
		$cacheKey = __CLASS__.'::'.__FUNCTION__ . '-' . $game_id;
		$rtn = $cache->get($cacheKey);
		if($rtn === false){   //无缓存数据时，读取数据库
			$db = IOSS_DB::getInstance();
			$query = $db->order_by('name', 'asc')->get_where(self::table, Array('game_id'=>$game_id, 'state'=>self::ENABLED))->result();
			$rtn = array();
			foreach ($query as $v){
				$rtn[$v->id] = new self($v);
			}
			$cache->set($cacheKey, $rtn);
		}
		$idx = array();
		foreach ($rtn as $v){
			$idx[$v->id] = array();
		}
		foreach ($rtn as $v){
			$idx[$v->parent_id][$v->id] = &$idx[$v->id];
		}
		self::$index[$game_id] = &$idx;
		self::$categories[$game_id] = &$rtn;
	}
	
	/**
	* @param	int $id	
	* @return	Category
	*/
	public static function getCategory($id){
		$cache = IOSS_GameCache::getInstance();
		$cacheKey = __CLASS__.'::'.__FUNCTION__.'('.$id.')';
		if(($category = $cache->get($cacheKey)) === false){
			$db = IOSS_DB::getInstance();
			$row = $db->get_where(self::table, Array('id'=>$id, 'state'=>self::ENABLED))->row();
			$category = $row? new IOSS_Category($row):null;
			$cache->set($cacheKey, $category);
		}
		return $category;
	}
	
	/**
	* 根据目录code获取目录信息
	* @param int $game_id
	* @param	string $code	
	* @return	Category
	*/
	public static function getCategoryByCode($game_id, $code){
		$cache = IOSS_GameCache::getInstance();
		$cacheKey = __CLASS__.'::'.__FUNCTION__.'(' . $game_id . ',' .$code.')';
		if(($rtn = $cache->get($cacheKey)) === false){
			$db = IOSS_DB::getInstance();
			$row = $db->get_where(self::table, Array('code'=>$code, 'game_id'=>$game_id, 'state'=>self::ENABLED))->row();
			$rtn = $row?new self($row) : null;
			$cache->set($cacheKey, $rtn);
		}
		return $rtn;
	}
	
	/**
	 * 获取指定游戏的目录
	 * @param int $game_id            游戏ID
	 * @param boolean $recursion 是否返回所有子目录
	 */
	public static function getCategories($game_id, $recursion = false){
		if(!array_key_exists($game_id, self::$categories)){
			self::_load($game_id);
		}
		if($recursion){
			return self::$categories[$game_id];
		}else{
			return isset(self::$index[$game_id]['0'])?array_intersect_key(self::$categories[$game_id], self::$index[$game_id]['0']):array();
		}
	}

	/**
	 * 判断是否有子目录
	 * @return boolean
	 */
	public function hasChildren(){
		if(!array_key_exists($this->game_id, self::$categories)){
			self::_load($this->game_id);
		}
		return isset(self::$index[$this->game_id][$this->id]) && count(self::$index[$this->game_id][$this->id])>0;
	}
	
	/** 获取子目录 */
	public function getChildren(){
		if(!array_key_exists($this->game_id, self::$categories)){
			self::_load($this->game_id);
		}
		return isset(self::$index[$this->game_id][$this->id])?array_intersect_key(self::$categories[$this->game_id], self::$index[$this->game_id][$this->id]):array();
	}
	/** 获取父级目录对象 */
	public function getParent(){
		return self::getCategory($this->parent_id);
	}
}
