<?php
class IOSS_PaymentMethod {
	const PAYPAL = 1;
	const WESTERN_UNION = 3;
	const STATE_ENABLE = 1;
	protected $id;
	protected $code;
	protected $name;
	protected $des;
	protected $state;
	protected $default_risk;
	protected $account;
	protected $config;
	private $cfg = null;
	
	/**
	 * 获取网站收款方式
	 * @param int $site_id
	 * @param int $payment_method_id
	 * @return IOSS_PaymentMethod
	 */
	public static function getById($site_id, $payment_method_id){
		$methods = self::getAll($site_id);
		return isset($methods[$payment_method_id])?$methods[$payment_method_id]:null;
	}
	/**
	 * 使用code获取网站收款方式
	 * @param int $site_id
	 * @param int $code
	 * @return IOSS_PaymentMethod
	 */
	public static function getByCode($site_id, $code){
		$cache = IOSS_GameCache::getInstance();
		$cacheKey = __CLASS__.'::'.__FUNCTION__ . "( $site_id , $code )";
		if($rtn = $cache->get($cacheKey)){
			return $rtn;
		}
		$methods = self::getAll($site_id);
		foreach ($methods as $m){
			if($m->code ===$code ) {
				$cache->set($cacheKey, $m);
				return $m;	
			}
		}
		return null;
	}
	
	/**
	 * 获取网站所有可用的收款方式
	 * @param int $site_id
	 * @return array
	 */
	public static function getAll($site_id){
		$cache = IOSS_GameCache::getInstance();
		$cacheKey = __CLASS__.'::'.__FUNCTION__.'( ' . $site_id. ' )';
		if($rtn = $cache->get($cacheKey)){
			return $rtn;
		}
		$db = IOSS_DB::getInstance();
		$db->select('method.*, a.account, a.config');
		$db->from('payment_account a');
		$db->join('site_payment site', 'a.id = site.account_id ');
		$db->join('payment_method method', 'a.method_id = method.id ');
		$db->where('a.state', self::STATE_ENABLE);
		$db->where('site.site_id', $site_id);
		$db->order_by('site.sort');
		if($rows = $db->get()->result()){
			foreach ($rows as $vo){
				$rtn[$vo->id] = new self($vo);	
			}
			$cache->set($cacheKey, $rtn);
			return $rtn;
		}
		return array();
	}
	
	protected function __construct($vo){
		$this->id = $vo->id;
		$this->code = $vo->code;
		$this->name = $vo->name;
		$this->des = $vo->des;
		$this->state = $vo->state;
		$this->default_risk = $vo->default_risk;
		$this->account = $vo->account;
		$this->config = $vo->config;
	}
	
	public function __get($prop){ return $this->{$prop}; }
	
	/**
	 * 获取配置信息
	 * @param string $key
	 * @return mixed
	 */
	public function getConfig($key=null){
		if($this->cfg === null){
			$this->cfg = Array();
			if($this->config){
				$arr = explode("\n", str_replace("\r\n", "\n", $this->config));
				foreach ($arr as $v){
					if($list = explode('=', $v, 2)){
						$this->cfg[trim($list[0])] = isset($list[1])?trim($list[1]):'';
					}
				}
			}
		}
		if($key === null){
			return $this->cfg;
		}else{
			return isset($this->cfg[$key]) ? $this->cfg[$key] : null;
		}
	}
}