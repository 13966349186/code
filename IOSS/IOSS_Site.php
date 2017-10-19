<?php
/**
 * 网站信息操作类
 * @author HEYI
 */
Class IOSS_Site {

	const ENABLED = 1;
    protected $id;
    protected $code;
    protected $domain;
    protected $name;
    protected $state;

    private $cfg = null;

    private static $idArr = null;
    private static $codeArr = Array();
    private static $domainArr = Array();
	public function __get($prop){ return $this->{$prop}; }

    private function __construct($arr){
    	foreach ($arr as $k=>$v){
    		if(property_exists($this, $k)){
    			$this->{$k} = $v;
    		}
    	}
    }
	private static function _load(){
		if(self::$idArr === null){
			self::$idArr = Array();
			$cache = IOSS_GameCache::getInstance();
			$cacheKey = __CLASS__ . '::'.__FUNCTION__ .'::AllSites';
			if(!($siteInfo = $cache->get($cacheKey))){
				$siteInfo = Array();
				$db = IOSS_DB::getInstance();
				if($lst = $db->get_where('site', Array('state'=>self::ENABLED))->result()){
					foreach ($lst as $v){
						$siteInfo[$v->id] = new self($v);
					}
					$cache->set($cacheKey, $siteInfo);
				}
			}
			self::$idArr = $siteInfo;
			foreach (self::$idArr as $id=>$v){
				self::$codeArr[$v->code] = $v;
				self::$domainArr[$v->domain] = $v;
			}
		}
	}
    /**
     * 根据code获取网站信息
     * @param string $code
     * @return IOSS_Site
     */
    public static function getSiteByCode($code){
    	self::_load();
		return element($code, self::$codeArr, null);
    }
	/**
	 * 根据域名获取网站信息
	 * @param string $domain
	 * @return IOSS_Site
	 */
    public static function getSiteByDomain($domain){
    	self::_load();
		return element($domain, self::$domainArr, null);
    }
	/**
	 * 根据网站ID获取网站信息
	 * @param string $id
	 * @return IOSS_Site
	 */
    public static function getSite($id){
    	self::_load();
		return element($id, self::$idArr, null);
    }
    
    /**
     * 获取网站配置信息
     * @param    string $code    
     * @return   array
     */
    public function getConfig($code){
    	if($this->cfg === null){
	        $cache = IOSS_GameCache::getInstance();
			$cacheKey = __CLASS__.'::'.__FUNCTION__ .'__' . $this->id;
			if(!($this->cfg = $cache->get($cacheKey))){
				$this->cfg = Array();
				$db = IOSS_DB::getInstance();
				if($arr = $db->get_where('core_site_config', Array('site_id'=>$this->id))->result()){
					foreach ($arr as $row){
						$this->cfg[$row->config_key] = $row->value;
					}
					$cache->set($cacheKey, $this->cfg);
				}
			}
    	}
    	return element($code, $this->cfg, '');
    }
    /**
     * 返回AdWords转换跟踪代码
     * @return string
     */
    public function getAdWordsJs($amount = 1, $currency = 'USD'){
    	$str = $this->getConfig('google_adwords');
    	$search = array('#totalValue#', '#currency#');
    	$replace = array($amount,$currency);
    	return str_replace($search, $replace, $str);
    }
}
