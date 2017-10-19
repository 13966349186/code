<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * IOSS_Product 测试
 */
class IOSS_Order_Test extends CI_Controller {

	function __construct(){
		parent::__construct();
		if(ENVIRONMENT == 'production' ){
			exit('enviroment is production');
		}
		$this->load->library('unit_test');
	}
	
	function index(){
		$order = IOSS_Order::getById(33177);
		var_dump($order);
		var_dump($order->getAttributes());
		var_dump($order->getProducts());
		$order->setAttributes(array('Player' =>  'zhangyk' ,'Rating' =>  '2','Quality' =>  'Gold' ,'Position' =>  'RB', 'test'=>'ttttttttttttttttttttttttttttt'));
		$order->update();
		
	}
	
}
