<?php
/**
 * 商品类型类
 * @author lifw
 */
class IOSS_Type {
	const ENABLED = 1;
	const table = 'types';
	const tabble_attribute_def = 'order_attribute_def';
	protected $id;
	protected $game_id;
	protected $code;
	protected $name;
	protected $model;
	protected $state;
	protected $note;
	protected $create_time;
	protected $update_time;
	protected $_attributes_def;
	protected $_order_attributes_def;
	
	private static $idArr = array ();
	
	private function __construct($vo){
		$this->id = (int)$vo->id;
		$this->game_id = (int)$vo->game_id;
		$this->code = $vo->code;
		$this->name = $vo->name;
		$this->model = $vo->model;
		$this->state = (int)$vo->state;
		$this->note = $vo->note;
		$this->create_time = (int)$vo->create_time;
		$this->update_time = (int)$vo->update_time;
	}
	
	public function __get($prop) {
		return $this->{$prop};
	}
	
	/**
	 * 根据类型ID获取商品类型信息
	 * @param int $type_id        	
	 * @return IOSS_Type
	 */
	public static function getType($type_id) {
		if(!is_numeric($type_id)){
			return null;
		}
		if (array_key_exists ( $type_id, self::$idArr )) {
			return self::$idArr [$type_id];
		}
		$cache = IOSS_GameCache::getInstance ();
		$cacheKey = __CLASS__ . '::' . __FUNCTION__ . '::types-' . $type_id;
		if(($type = $cache->get($cacheKey))  === false){
			$db = IOSS_DB::getInstance ();
			$row = $db->get_where ( self::table, Array ('state' => self::ENABLED,'id' => $type_id))->row();
			$type = $row?new self($row):null;
			$cache->set ( $cacheKey, $type );
		}
		self::$idArr [$type_id] = $type;
		return $type;
	}
	
	/**
	 * 获取指定游戏的所有商品类型
	 * @param int $game_id
	 * @param string $mode
	 */
	public static function getTypes($game_id,$model=''){
		$cache = IOSS_GameCache::getInstance ();
		$cacheKey = __CLASS__ . '::' . __FUNCTION__ . '::game_id-' . $game_id . ',model-' . $model;
		if(($list = $cache->get($cacheKey)) === false){
			$list = array();
			$db = IOSS_DB::getInstance ();
			$db->from(self::table);
			$db->order_by('name');
			$db->where('game_id',$game_id);
			$db->where('state',self::ENABLED);
			if($model){
				$db->where('model',$model);
			}
			$rows = $db->get()->result();
			foreach ($rows as $vo){
				$list[$vo->id] = new self($vo);
			}
			$cache->set($cacheKey,$list);
		}
		return $list;
	}
	
	/**
	 * @deprecated 已作废，旧版本代码中使用
	 * 取得商品发货信息配置
	 * @return array
	 */
	public function getAttributesDef() {
		if($this->_attributes_def !== null){
			return $this->_attributes_def;
		}
		$cache = IOSS_GameCache::getInstance ();
		$cacheKey = __CLASS__ . '::' . __FUNCTION__ . '--type_id-' . $this->id;
		if(($this->_attributes_def = $cache->get($cacheKey)) === false ){
			$this->_attributes_def = array();
			$db = IOSS_DB::getInstance ();
			$rows = $db->order_by ('sort')->get_where (self::tabble_attribute_def, Array ('type_id'=>$this->id))->result();
			foreach ($rows as $v) {
				$v->data_format = IOSS_OrderAttributesDef::_parse_config ( $v->data_config, $v->data_format );
				$this->_attributes_def[$v->code] = $v;
			}
			$cache->set ( $cacheKey, $this->_attributes_def);
		}
		return $this->_attributes_def;
	}
	

	/**
	 * 返回订单发货属性的定义 
	 * @return multitype:IOSS_OrderAttributesDef
	 */
	public function getOrderAttributesDef(){
		if($this->_order_attributes_def !== null){
			return $this->_order_attributes_def;
		}
		
		$cache = IOSS_GameCache::getInstance ();
		$cacheKey = __CLASS__ . '::' . __FUNCTION__ . '--type_id-' . $this->id;
		if(($this->_order_attributes_def = $cache->get($cacheKey)) === false ){
			$this->_order_attributes_def = array();
			$db = IOSS_DB::getInstance ();
			$rows = $db->order_by ('sort')->get_where (self::tabble_attribute_def, Array ('type_id'=>$this->id))->result();
			foreach ($rows as $v) {
				$this->_order_attributes_def[$v->code] = new IOSS_OrderAttributesDef($v);
			}
			$cache->set ( $cacheKey, $this->_order_attributes_def);
		}
		return $this->_order_attributes_def;
	}
}

class IOSS_OrderAttributesDef {
	
	public $id;
	/**
	 * 表单类型
	 * @var int
	 */
	public $type;
	
	/**
	 * 表单 name 
	 * @var string
	 */
	public $code;
	
	/**
	 * 显示名称
	 * @var string
	 */
	public $label;
	
	/**
	 * 表单默认值
	 * @var string
	 */
	public $default;
	
	/**
	 * 表单选项选项（键值对）
	 * @var array
	 */
	public $options;
	
	/**
	 * CI 表单验证规则
	 * @var string
	 */
	public $rules;
	
	public $sort;
	
	public function __construct($vo){
		$data_format = self::_parse_config ( $vo->data_config, $vo->data_format );
		$this->id = $vo->id;
		$this->type = $vo->data_config;
		$this->code = $vo->code;
		$this->label = $vo->name;
		$this->default = isset($data_format['default'])?$data_format['default']:'';
		$this->options = isset($data_format['options'])?$data_format['options']:array();
		$this->rules = implode('|', $data_format['ci']);
		$this->sort = $vo->sort;
	}

	/**
	 * 配置项解析
	 * @param integer $type 	数据类型
	 * @param string $cfg_str   配置串
	 * @return Array 解析结果
	 */
	public static function _parse_config($type, $cfg_str) {
		$lines = explode ( "\n", str_replace ( "\r", "", $cfg_str ) );
		$configs = array ();
		foreach ( $lines as $line ) {
			$c = explode ( ':', $line, 2 );
			if ($c && count ( $c ) == 2) {
				if ($c [0] == 'options' || $c [0] == 'upload') {
					$option = explode ( '=', $c [1], 2 );
					$configs [$c [0]] [trim ( $option [0] )] = trim ( $option [1] );
				} elseif ($c [0] == 'default') {
					$configs [$c [0]] = $c [1];
				} elseif ($c [0] == 'ci') {
					$configs [$c [0]] [] = trim ( $c [1] );
				}
			}
		}
		if (! isset ( $configs ['ci'] )) {
			$configs ['ci'] = array ();
		}
		$max_length = false;
		$setted_max_length = false;
		foreach ( $configs ['ci'] as &$cfgItem ) {
			$v = trim ( strtolower ( $cfgItem ) );
			if (strlen ( $v ) > 10 && substr ( $v, 0, 10 ) == 'max_length') {
				$setted_max_length = &$cfgItem;
				break;
			}
		}
		// 设置默认配置
		switch ($type) {
			case 2 : // 时间
				$max_length = 'max_length[20]';
				break;
			case 3 : // 日期
				$max_length = 'max_length[20]';
				break;
			case 6 : // 下拉框
				if ($max_length === false) {
					$max_length = 'max_length[255]';
				}
				break;
			case 7 : // 复选框
				if ($max_length === false) {
					$max_length = 'max_length[255]';
				}
				break;
			case 8 : // 单选框
				if ($max_length === false) {
					$max_length = 'max_length[255]';
				}
				break;
			case 4 : // 整数
				if (! in_array ( 'integer', $configs ['ci'] )) {
					$configs ['ci'] [] = 'integer';
				}
				break;
			case 5 : // 浮点数
				if (! in_array ( 'numeric', $configs ['ci'] )) {
					$configs ['ci'] [] = 'numeric';
				}
				break;
			case 0 : // 文本
				if ($max_length === false) {
					$max_length = 'max_length[255]';
				}
		}
		if ($setted_max_length === false && $max_length !== false) {
			$configs ['ci'] [] = $max_length;
		}
		return $configs;
	}

}