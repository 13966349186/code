<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站模块-货币管理
 */
class Currency extends AdminController {
	public $_validation = null;

	function __construct(){
		parent::__construct();
		$this->load->model('MCurrency');
		$this->_validation =  array(
			array('field'=>'exchange_rate', 'label'=>l('exchange_rate'), 'rules'=>'required|numeric|greater_than[0.0001]|less_than[99999.9999]|is_decimal[4]')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
		);
	}
	
	function edit($id){
		if(!$id || !($currency = $this->MCurrency->getById($id))){
			echo json_encode(Array('code'=>'0', 'msg'=>'指定的货币类型不存在 !'));
			return;
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$currency = populate($currency, $this->form_validation->post());
			$currency->exchange_rate = number_format($currency->exchange_rate, 4,'.','');
			if($this->MCurrency->update($currency) !== true){
				//操作冲突
				echo json_encode(Array('code'=>'0', 'msg'=>l('data_fail')));
				return ;
			}
			echo json_encode(Array('code'=>'1', 'msg'=>l('edit_success'), 'value'=>$currency->exchange_rate, 'update_time'=>$currency->update_time));
			return;
		}
		echo json_encode(Array('code'=>'0', 'msg'=>validation_errors()));
	}
	
	function index(){
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('name', 'like');

		$limit = $this->pagination($this->MCurrency->getCount());
		$currencys = $this->MCurrency->getList($limit);
		
		$this->assign('currencys',$currencys);
		$this->layout();
	}
}
