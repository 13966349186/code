<?php
class Types  implements ArrayAccess, IteratorAggregate{
	private $names = array();
	private $data = array();
	private $isLoadAttribute = FALSE;
	
	public function __construct(){
		$CI =& get_instance();
		$CI->load->model('MType');
		foreach ($CI->MType->getAll() as $v){
			$this->names[$v->id] = $v->name;
			$this->data[$v->id] = $v;
		}
	}
	
	private function loadAttribute(){
		$CI =& get_instance();
		$CI->load->model('MTypeAttribute');
		foreach ($CI->MTypeAttribute->getAll() as $v){
			if(array_key_exists($v->type_id, $this->data)){
				$this->data[$v->type_id]->{$v->code} = $v->value;
			}
		}
		$this->isLoadAttribute = true;
	}
	
	public function offsetGet($offset) {
		return  (isset($this->names[$offset]))?$this->names[$offset]:NULL;
	}
	
	public function offsetSet($offset, $value){
	}
	
	public function offsetExists($offset) {
		return isset($this->names[$offset]);
	}
	
	public function offsetUnset($offset) {
	}
	
	public function getIterator(){
		return new ArrayIterator($this->names);
	}
	
	/**
	 * 返回type信息
	 * @param int $id
	 * @return object 
	 */
	public function  getById($id){
		if(!$this->isLoadAttribute){
			$this->loadAttribute();
		}
		return array_key_exists($id, $this->data)?$this->data[$id]:null;
	}

	/**
	 * 获取商品类型对应的Model
	 * @param int $id
	 * @return string
	 */
	public function getModel($id){
		if(array_key_exists($id, $this->data)){
			$vo = $this->data[$id];
			return $vo->model;
		}else{
			return '';
		}
	}

	
	/**
	 * 转为键值对数组
	 * @param string $index_key   作为返回数组的索引/键的列
	 * @param string $column_key 需要返回值的列
	 * @return array
	 */
	public function toArray($index_key ='id', $column_key = 'name'){
		return object_column($this->data, $column_key, $index_key);
	}
	
}