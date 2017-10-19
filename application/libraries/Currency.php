<?php
class Currency implements ArrayAccess,IteratorAggregate{
	private $data = array();
	private $names = array();
	public function __construct(){
		$CI =& get_instance();
		$CI->load->model('MCurrency');
		foreach ($CI->MCurrency->getAll() as $v){
			$this->data[$v->code] = $v;
			$this->names[$v->code] = $v->name;
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
	public function toArray($index_key ='code', $column_key = 'name'){
		return object_column($this->data, $column_key, $index_key);
	}
	
	
	/**
	 * 格式化金额显示格式
	 * @param number $num 金额
	 * @param string $code   货币代码
	 */
	public function format($num, $code = NULL){
		if($code && array_key_exists($code, $this->data)){
			return sprintf($this->data[$code]->format, $num);
		}
		return number_format($num,2,'.','');
	}
}