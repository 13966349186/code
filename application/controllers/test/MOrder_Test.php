<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 测试
 */
class MOrder_Test extends CI_Controller {
	/**
	 * @var MOrder
	 */
	public  $MOrder;
	function __construct(){
		parent::__construct();
		if(ENVIRONMENT == 'production' ){
			exit('enviroment is production');
		}
		$this->load->library('unit_test');
		$this->unit->use_strict(true);
	}
	function index(){
		$this->load->model ( 'MOrder');
		$success = $this->MOrder->updateState(498, MOrder::STATE_CLOSED);
		$filed = $this->MOrder->updateState(-1, MOrder::STATE_HOLDING);
		$this->unit->run($success, TRUE, ' MOrder',  "");
		$this->unit->run($filed, FALSE, ' MOrder',  "");
		echo $this->unit->report();
	}
}