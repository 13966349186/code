<?php
class PaymentMethod implements ArrayAccess,IteratorAggregate {
	private $names = Array();
	private $data = Array();
	public function __construct(){
		$CI =& get_instance();
		$CI->load->model('MPaymentMethod');
		foreach ($CI->MPaymentMethod->getAll() as $v){
			$this->data[$v->id] = $v;
			$this->names[$v->id] = $v->name;
		}
	}
	public function offsetGet($offset) {
		return isset($this->data[$offset])?$this->data[$offset]->name:NULL;
	}
	public function offsetSet($offset, $value) {
	}
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->data);
	}
	public function offsetUnset($offset) {
	}
	public function getIterator(){
		return new ArrayIterator($this->names);
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