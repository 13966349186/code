<?php
/**
 * 游戏配置缓存
 * @author Niap
 **/

class IOSS_GameCache extends IOSS_Cache{

	private static $_instance = null;
	public $isConnect=null;  //是否连接上
	
	private function __construct(){
		if(!class_exists('Memcache')){
			return false;
		}
		$config = IOSS_Conf::getConfig('memcache');
		$config = isset($config['game'])?$config['game']:FALSE;
		$cache = new Memcache;
		if($cache->connect($config['host'], $config['port'])){
			$this->cache = $cache;
			$this->isConnect=TRUE;
		}else{
			$this->isConnect=FALSE;
		}
	}

	public static function getInstance(){
		if(self::$_instance == null){
			self::$_instance = new self;
		}
		return self::$_instance;
	}
}
