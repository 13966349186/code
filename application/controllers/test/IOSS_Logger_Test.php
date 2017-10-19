<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Types 测试
 */
class IOSS_Logger_Test extends CI_Controller {
	function __construct(){
		parent::__construct();
		if(ENVIRONMENT == 'production' ){
			exit('enviroment is production');
		}
		$this->load->library('unit_test');
	}
	
	function index(){
		$log = IOSS_Logger::getIntance();
		$log->message('this is a message', 'test');
	}
}