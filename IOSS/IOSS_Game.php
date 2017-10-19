<?php
/**
 * 游戏信息操作类
 * @author lifw
 */
Class IOSS_Game {
	const ENABLED = 1;
    private $id;
    private $code;
    private $name;
    private $description;
    private $sort;
    private $state;

    private static $idArr = array();
    private static $codeArr = array();
    private static $isLoaded = FALSE;

	public function __get($prop){ return $this->{$prop}; }

	private function __construct($vo){
		$this->id = (int)$vo->id;
		$this->code = $vo->code;
		$this->name = $vo->name;
		$this->description = $vo->description;
		$this->sort = (int)$vo->sort;
		$this->state = (int)$vo->state;
	}
	
	/**
	 * 返回所有启用的游戏
	 * @return array
	 */
	public static function getGames(){
		if(self::$isLoaded){
			return self::$codeArr;
		}
		$cache = IOSS_GameCache::getInstance();
		$key = __CLASS__.'::'.__FUNCTION__.'::()';
		$data = $cache->get($key);
		if($data === false){
			$db = IOSS_DB::getInstance();
			$data = array();
			$db->from("core_game");
			$db->where(Array('state'=>self::ENABLED));
			$db->order_by('sort');
			if($rows = $db->get()->result()){
				foreach ($rows as $row){
					$data[] = new self($row);
				}
			}
			$cache->set($key,$data);
		}
		foreach ($data as $v) {
			self::$idArr[$v->id] = $v;
			self::$codeArr[$v->code] = $v;
		}
		self::$isLoaded = TRUE;
		return self::$codeArr;
	}
	
	/**
	 * 根据游戏ID获取游戏信息
	 * @param int $id
	 * @return IOSS_Game
	 */
    public static function getGame($id){
    	if(array_key_exists($id, self::$idArr)){
    		return self::$idArr[$id];
    	}
    	$cache = IOSS_GameCache::getInstance();
    	$key = __CLASS__.'::'.__FUNCTION__ . "( $id )";
    	$game = $cache->get($key);
    	if($game === false){
    		$db = IOSS_DB::getInstance();
    		$vo = $db->get_where("core_game", Array('id'=>$id,'state'=>self::ENABLED ))->row();
    		if($vo){
    			$game = new self($vo);
    		}else{
    			$game = null;
    		}
    		$cache->set($key,$game);
    	}
   		return $game;
    }
	
	/**
	 * 根据游戏代码获取游戏信息
	 * @param string $code 游戏代码
	 * @return IOSS_Game
	 */
	public static function getGameByCode($code){
		if(array_key_exists($code, self::$codeArr)){
			return self::$codeArr[$code];
		}
		$cache = IOSS_GameCache::getInstance();
		$key = __CLASS__.'::'.__FUNCTION__ . "( $code )";
		$game = $cache->get($key);
		if($game === false){
			$db = IOSS_DB::getInstance();
			$vo = $db->get_where("core_game", Array('code'=>$code, 'state'=>self::ENABLED ))->row();
			if($vo){
				$game = new self($vo);
			}else{
				$game = null;
			}
			$cache->set($key,$game);
		}
		return $game;
	}
}

