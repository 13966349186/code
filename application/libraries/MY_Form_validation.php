<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation{
	
	protected $_error_prefix = '';
	protected $_error_suffix = '';
	
	function __construct($rules=array()){
		parent::__construct($rules);
	}
	
	/**
	 * POST数据项过滤
	 */
	public function post($key=null){
		if($key){
			$value =  $this->CI->input->post($key);
			return $value && (array_key_exists($key, $this->_field_data) || array_key_exists($key . '[]', $this->_field_data))?$value : null ;
		}
		if($date = $this->CI->input->post()){
			foreach ($date as $k=>$v){
				if(!array_key_exists($k, $this->_field_data) && !array_key_exists($k . '[]', $this->_field_data)){
					unset($date[$k]);
				}
			}
			//$date = array_filter($date, function ($k)  {
			//	return array_key_exists($k, $this->_field_data) || array_key_exists($k . '[]', $this->_field_data);
			//}, ARRAY_FILTER_USE_KEY);
			return $date;
		}
		return null;
	}

	/**
	 * 域名格式检查
	 * @param string $str
	 */
	public function valid_domain($str){
		return ( ! preg_match("/^[0-9a-zA-Z]+[0-9a-zA-Z\.-]*\.[a-zA-Z]{2,4}$/", $str)) ? FALSE : TRUE;
	}
	
	/**
	 * 检查小数点后位数最大值
	 * @param string $str
	 * @param int $val 小数点后最多位数
	 */
	public function is_decimal($str,$val){
		if (preg_match("/[^0-9]/", $val)){
			return FALSE;
		}
		return (bool) preg_match('/^[\-+]?[0-9]+(\.[0-9]{0,' .$val . '})?$/', $str);
	}

	
	//************************************************
	//  文件上传相关函数，cms模块依赖以下函数
	//  作者：张永慷
	// **********************************************
	public function is_file($str){
		return  is_file($str) !== false;
	}
	
	public function is_image($str){
		return exif_imagetype($str) !== false;
	}
	
	public function max_size($str, $val){
		$size = @filesize($str);
		return  is_numeric($size) && ($size/1024 <= $val);
	}
	
	public function max_width($str, $val){
		$info = @getimagesize($str);
		return $info && ($info[0] <= $val);
	}
	
	public function min_width($str, $val){
		$info = @getimagesize($str);
		return $info && ($info[0] >= $val);
	}
	
	public function max_height($str, $val){
		$info = @getimagesize($str);
		return $info && ($info[1] <= $val);
	}
	
	public function min_height($str, $val){
		$info = @getimagesize($str);
		return $info && ($info[1] >= $val);
	}
	
	public function allowed_types($str, $val){
		if($val == '*'){
			return true;
		}
		$allowed_types = explode(',', strtolower($val));
		$type = strtolower(pathinfo($str, PATHINFO_EXTENSION));
		return in_array($type, $allowed_types);
	}
	
	/**
	 * 文件保存函数
	 * @param 文件路径 $str
	 * @param 保存参数 $val：使用逗号分隔， file_name= 指定文件名，upload_path= 保存文件路径，encrypt_name=true/false 使用随机的字符串做文件名，overwrite=true/false 允许同名文件覆盖
	 * @return boolean|string
	 */
	public function upload($str, $val){
		$str = trim($str, '/ ');
		if(!file_exists($str)){
			return false;
		}
		$CI = get_instance();
		$base_path = $CI->config->item('upload_path')? trim($CI->config->item('upload_path'), '/ '):'upload';
		$temp_path =  $CI->config->item('tmp_path')? trim($CI->config->item('tmp_path'), '/ ') : 'upload/tmp';
		if(strncmp($str, $temp_path, strlen($temp_path)) !== 0){
			return $str; //当前文件不在临时目录，直接返回原文件路径
		}
		foreach (explode(',', $val) as $v){
			$config_array = explode('=', $v, 2);
			$configs[$config_array[0]] = (count($config_array)==2)? trim($config_array[1]) : NULL;
		}
		$encrypt_name = (isset($configs['encrypt_name']) && $configs['encrypt_name'] === 'false') ? false : true;
		$overwrite = (isset($configs['overwrite']) && $configs['overwrite'] === 'false') ? false : true;
		$upload_path = isset($configs['upload_path'])? trim($configs['upload_path'], '/') : date("Y-m", time());
		$ext = pathinfo($str, PATHINFO_EXTENSION);
		if(isset($configs['file_name']) && $configs['file_name']){
			$file_name = $configs['file_name'];
		}else if($encrypt_name){
			do{
				mt_srand();
				$file_name = md5(uniqid(mt_rand()));
				if($ext){
					$file_name .= '.' . $ext;
				}
			}
			while(file_exists($base_path . '/' . $upload_path . '/' . $file_name ));
		}else {
			$file_name  = pathinfo($str, PATHINFO_BASENAME);
		}
		if($overwrite === false && file_exists($base_path . '/' . $upload_path . '/' . $file_name)){
			return false;
		}
		//拷贝文件
		if(!file_exists($base_path . '/' . $upload_path)){
			@mkdir($base_path . '/' . $upload_path, DIR_READ_MODE, true);
		}
		if (!copy($str, $base_path . '/' . $upload_path . '/' . $file_name)){
			return FALSE;
		}
		return $base_path . '/' . $upload_path . '/' . $file_name;
	}
}