<?php
class IOSS_PaymentMethod_Test extends CI_Controller {

	function __construct(){
		parent::__construct();
		if(ENVIRONMENT == 'production' ){
			exit('enviroment is production');
		}
		$this->load->library('unit_test');
	}
	
	function index(){
		$methods = IOSS_PaymentMethod::getAll(4);
		var_dump($methods);
		
		$m = IOSS_PaymentMethod::getById(4,1);
		var_dump($m);
		
		$order = IOSS_Order::getByNo('1701161154286442');
		$paypal = IOSS_Paypal::create($order);
		var_dump($paypal);
		//var_dump($paypal->form('test-form'));
	}
}