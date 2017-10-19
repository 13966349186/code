<?php
class Finance extends AdminController{
	function __construct(){
		parent::__construct();
		$this->load->library('Currency');
		$this->load->library('Sites');
		$this->load->library('Games');
		$this->load->model('MOrder');
		$this->load->model('RFinance');
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
	}
	
	public function index($type='site'){
		$this->RFinance->type = $type;
		if(!$this->formfilter->getFilterValue('create_begin') || !$this->formfilter->getFilterValue('create_end')){
			$this->formfilter->setFilterValue('create_begin',date('Y-m-d'));
			$this->formfilter->setFilterValue('create_end',date('Y-m-d'));
		}
		if($begintime = filterValue('create_begin')){
			$this->formfilter->addFilter('create_begin', 'where',array('order_payment.create_time >= ',strtotime($begintime.' 00:00:00')));
		}
		if($endtime = filterValue('create_end')){
			$this->formfilter->addFilter('create_end', 'where',array('order_payment.create_time <= ',strtotime($endtime.' 23:59:59')));
		}
		$this->formfilter->addFilter('order_payment.currency','where');
		$lst = $this->RFinance->getList();
		$this->assign('params', $this->input->get()?'?'.http_build_query($this->input->get()):'');
		$this->assign('type', $type);
		$this->assign('lst', $lst);
		$this->layout();
	}
}