<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('populate')){
	/**
	 * 输入读取，将对象data_info的所有属性，以属性名作为key从数组inputs中取值
	 * @param Object $data_info
	 * @param Array $inputs
	 */	
	function populate($data_info,$inputs){
		foreach($inputs as $key=>$value){
			if(property_exists($data_info, $key)){
				$data_info->$key = $value;
			}
		}
		return $data_info;
	}
}
/* End of file populate.php */
/* Location: SOMEFOLDERS/populate.php */
