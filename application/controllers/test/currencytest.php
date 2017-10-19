<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Currency 测试
 */
class CurrencyTest extends AdminController {
	
	function __construct(){
		parent::__construct();
		$this->_required = array('index'=>0);
		$this->load->library('unit_test');
		$this->load->library('Currency');
	}
	
	function index(){

		$this->unit->run($this->currency['USD'], '美元', "Currency::getName('USD')",  "根据code返货货币名称");
		$this->unit->run($this->currency->format(12.12345,'USD'), '$ 12.12', 'Currency::format',  "格式化货币显示格式");
		
		$list = $this->MCurrency->getAll();
		$this->unit->run( count($this->currency->toArray()), count($list),  "Currency::getNameList", '测试返回货币列表数量');
		
		echo $this->unit->report();
		
		foreach ($this->currency as $v){
			var_dump($v);
		}
	}
}