<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Types 测试
 */
class TypesTest extends AdminController {
	
	function __construct(){
		parent::__construct();
		$this->_required = array('index'=>0);
		$this->load->library('unit_test');
		$this->load->library('Types');
	}
	
	function index(){

		$this->unit->run($this->types[34], 'coins', 'types::getModel',  "");
		$this->unit->run($this->types[1], '金币(K)', 'types::getName',  "");
		
		var_dump(array_key_exists(1, $this->types) );
		foreach ($this->types as $k=>$v){
			//echo   $k . ' -- ' .$v;
		}
		
		$this->types->getNameList(1,false);
		echo $this->unit->report();
	}
}