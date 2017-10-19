<?php
class Categories implements ArrayAccess,IteratorAggregate {
	private $names = Array();
	private $data = Array();
	private $tree = Array();
	

	public function __construct($game_id){
		$CI =& get_instance();
		$CI->load->model('MCategory');
		foreach ($CI->MCategory->getAll(array('game_id'=>$game_id[0])) as $v){
			$this->names[$v->id] = $v->name;
			$this->data[$v->id] = $v;
		}
	}
	public function offsetGet($offset) {
		return isset($this->names[$offset])?$this->names[$offset]:NULL;
	}
	public function offsetSet($offset, $value) {
	}
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->names);
	}
	public function offsetUnset($offset) {
	}
	public function getIterator(){
		return new ArrayIterator($this->names);
	}
	
	public function  getById($id){
		return array_key_exists($id, $this->data)?$this->data[$id]:null;
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
	
	public function getPath($id){
		$path = array();
		while(array_key_exists($id, $this->data) && !array_key_exists($id, $path)){
			$path[$id] = $this->data[$id];
			$id = $this->data[$id]->parent_id;
		}
		return array_reverse($path);
	}
	
	public function displayPath($id, $game_name = null , $delimiter = ' <i class="fa fa-angle-double-right"></i> '){
		$path = object_column( $this->getPath($id), 'name');
		if($game_name){
			array_unshift($path, $game_name);
		}
		return  implode($path, $delimiter);
	}
	
	/**
	 * 返回当前节点和所有子节点id
	 * @param int $id
	 * @return array;
	 */
	public function  getChildIds($id){
		if(!$this->tree){
			foreach ($this->data as $v){
				$this->tree[$v->id] = array();
			}
			foreach ($this->data as $v){
				$this->tree[$v->parent_id][$v->id] = &$this->tree[$v->id];
			}
		}
		$arr = array($id);
		if(array_key_exists($id, $this->tree)){
			treeWalk($this->tree[$id], $arr);
		}
		return $arr;
	}
	
	/**
	 * 递归遍历目录树
	 * @param array $tree 目录树
	 * @param string $str 目录id
	 */
	private function treeWalk($tree, &$arr){
		foreach ($tree as $k=>$v){
			if($v && is_array($v)) {
				$this->treeWalk($v, $arr);
			}
			array_push($arr, $k);
		}
		return;
	}
}