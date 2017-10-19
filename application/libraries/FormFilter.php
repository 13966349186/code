<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FormFilter{

	private $CI= null;
	private $filter= null;
	private $stack = null;
	private $page_name = null;
	private $post = true;
 	

	/**
	 * 初始化stack数据
	 * @param array $params   method=> post 或 get, 默认值为 post
	 * <br/>post - 表单提交方式必须为post，表单值会保存到session
	 * <br/>get - 表单提交方式必须为get
	 */
 	public function __construct($params = array()) {
		$this->CI = &get_instance();
		if(array_key_exists('method', $params) && $params['method'] == 'get'){
			$this->post = false;
			$this->stack = $this->CI->input->get(NULL, TRUE)?:array();
		}else{
			$this->page_name = str_replace('/', '.', strtolower($this->CI->_thisModule.$this->CI->_thisController.'/'.$this->CI->_thisMethod));
			$this->stack = $this->CI->input->post(NULL, TRUE);
			if(!$this->stack) $this->stack = $this->CI->session->userdata($this->page_name);
			if(!$this->stack) $this->stack = array();
			if($this->CI->input->get('page')){
				$this->stack['page'] = $this->CI->input->get('page');
			}
		}
	}

	public function addFilter($key,$condition,$param = null){
		if($param == null ){
			$val = $this->getFilterValue($key);
			if(strlen($val) < 1 or $val == '*')
				return;
			$param = array($key, trim($val));
		}
		$array = array(
			'condition' => $condition,
			'param' => $param,
		);
		$this->filter[$key] = $array;
	}
//('state', 'where');
	public function getFilterValue($key){
		if($pos = strpos($key,'.')){
			$key = substr($key , $pos+1);
		}
		if(array_key_exists($key,$this->stack)){
			return $this->stack[$key];
		}
		return '';
	}

	public function isInFilter($key){
		if($pos = strpos($key,'.')){
			$key = substr($key , $pos+1);
		}
		if(array_key_exists($key,$this->stack)){
			return true;
		}
		return false;
	}

	public function setFilterValue($key,$value){
		$this->stack[$key] = $value;
	}

	public function doFilter($db= null){
		if($this->post){
			$this->CI->session->set_userdata($this->page_name,$this->stack);
		}
		if(!$db){
			$db = $this->CI->db;
		}
		if(!empty($this->filter)){
			foreach($this->filter as $key=>$array){
				if(!empty($array['param'])){
					call_user_func_array(array($db, $array['condition']), $array['param']);
				}
			}
		}
	}
 }