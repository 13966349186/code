<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Category 测试
 */
class IOSS_News_Test extends CI_Controller {
	function __construct(){
		parent::__construct();
		if(ENVIRONMENT == 'production' ){
			exit('enviroment is production');
		}
		$this->load->library('unit_test');
		$this->unit->use_strict(true);
	}
	
	
	function index(){
		$db = IOSS_DB::getInstance();
		
		$node = IOSS_News::getByCode(1, 'fifa-delivery-info', 'coins');
		var_dump($node);
	}
}