<?php
/**
 * 货币信息操作类
 * @author HEYI
 */
Class IOSS_Currency {
	protected $id;
	protected $code;
	protected $name;
	protected $format;
	protected $exchange_rate;

	private static $list = null;

	public function __get($prop){ return $this->{$prop}; }

	protected function __construct($arr){
		foreach ($arr as $k=>$v){
			if(property_exists($this, $k)){
				$this->$k = $v;
			}
		}
	}
	
	/**
	 * @param string $code	
	 * @return Currency/array
	 */
	public static function getCurrency($code=NULL){
		if(self::$list === null){
			$cache = IOSS_GameCache::getInstance();
			$cacheKey = __CLASS__.'::'.__FUNCTION__;
			if(!(self::$list = $cache->get($cacheKey))){
				self::$list = Array();
				if($lst = IOSS_DB::getInstance()->get('core_currency')->result()){
					foreach ($lst as $row){
						self::$list[$row->code] = new self($row);
					}
					$cache->set($cacheKey, self::$list);
				}
			}
		}
		if($code === null){
			return self::$list;
		}else{
			return array_key_exists($code, self::$list)?self::$list[$code] : null;
		}
	}
	
	/**
	 * 格式化输出
	 * @param float $amount 金额	
	 * @return	 string 用于显示金额（带货币符号）
	 */
	public function format($amount){
		return sprintf($this->format, $amount);
	}
	
	/**
	 * 汇率转化
	 * @param float $num 美元金额	
	 * @return	 float 当前币种金额
	 */
	public function exchage($num){
		return round($num * $this->exchange_rate, 2);
	}
	
	/**
	 * 汇率转化
	 * @param float $num 当前币种金额
	 * @return	 float 美元金额
	 */
	public function exchageToUSD($num){
		return round($num / $this->exchange_rate, 2);
	}
}
