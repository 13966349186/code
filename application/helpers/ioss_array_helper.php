<?php
if(!function_exists('array_column')){
	/**
	 * 
	 * @param array $input
	 * @param string $column_key
	 * @param string $index_key
	 */
	function array_column($input, $column_key, $index_key = NULL){
		$r = array();
		if($index_key){
			foreach ($input as $item){
				$r[$item[$index_key]] = ($column_key === NULL)?$item:$item[$column_key];
			}
		}else{
			foreach ($input as $item){
				$r[] = ($column_key === NULL)?$item:$item[$column_key];
			}
		}
		return $r;
	}
}


/**
 * 返回对象数组中指定的一列
 * @param array  $input  需要取属性值的对象数组
 * @param mixed  $column_key 需要返回值的列名（对象属性名）
 * @param mixed  $index_key  作为返回数组的索引的列名，它可以是该列的整数索引，或者字符串键值。
 * @return array 
 */
function object_column($input, $column_key, $index_key = NULL){
	return array_column(array_map('get_object_vars', $input), $column_key, $index_key);
}


/**
 * 如果数组中不包含当前元素，则将单元压入数组的末尾
 * @param array $array
 * @param mixed $var
 */
function array_unique_push(&$array, $var){
	if(!in_array($var, $array)){
		return array_push($array, $var);
	}
	return 0;
}
