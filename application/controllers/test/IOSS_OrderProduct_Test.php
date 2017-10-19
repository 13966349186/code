<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * IOSS_Product 测试
 */
class IOSS_OrderProduct_Test extends CI_Controller {

	function __construct(){
		parent::__construct();
		if(ENVIRONMENT == 'production' ){
			exit('enviroment is production');
		}
		$this->load->library('unit_test');
		//$this->output->enable_profiler(true);
	}
	
	function index(){
		//$p = IOSS_OrderProduct::getById(33163); //异常数据
		$p = IOSS_OrderProduct::getById(33149); 
		var_dump($p);
		var_dump($p->save());		
		$product = IOSS_Product::getProduct(968);
		$p2 = IOSS_OrderProduct::create($product, 10);
		var_dump($p2);
		//$p2->save(2);
		
		$list = IOSS_OrderProduct::getAll(33128);
		var_dump($list);
	}
	
}