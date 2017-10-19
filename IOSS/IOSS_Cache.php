<?php
/**
 * 缓存基类 
 * @author Niap
 **/

class IOSS_Cache {
	protected $cache = null;
	protected $flag = 0;
	/**
	 * 读取数据标志
	 * 调试验证数据库读取时，设置为false，则缓存取不到数据
	 * @var boolean
	 */
	public static $READ_CACHE_FLG = true;

	public function get($name){
		if(!self::$READ_CACHE_FLG){
			return false;
		}
		if($this->cache === null) {
			return false;
		}else{
			return $this->cache->get($name);
		}
	}

	public function set($name,$value,$time=86400){
		if($this->cache === null) return false;
		return $this->cache->set($name,$value,$this->flag,$time);
	}

	public function replace($name,$value,$time=86400){
		if($this->cache === null) return false;
		return $this->cache->replace($name,$value,$this->flag,$time);
	}

	public function delete($name){
		if($this->cache === null) return false;
		return $this->cache->delete($name);
	}

	public function flush(){
		if($this->cache === null) return false;
		return $this->cache->flush();
	}
    
     public function getVersion(){
		if($this->cache === null) return false;
     	return $this->cache->getVersion();
     }
     
     public function getStates(){
     	if($this->cache === null) return false;
     	return $this->cache->getStats();
     }

     public function increment($key, $offset=1){
     	return $this->cache->increment($key, $offset);
     }
}
