<?php
class OrderVerifyReport extends AdminController{
	private $types = array('admin'=>'账号统计','m'=>'月报', 'w'=>'周报', 'd'=>'日报');
	function __construct(){
		parent::__construct();
		$this->load->model('ROrderLog');
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		
	}

	function index($type = ''){
		$type = array_key_exists($type, $this->types)?$type:'admin';  //设置默认值	
		if(!$this->formfilter->getFilterValue('create_begin') || !$this->formfilter->getFilterValue('create_end')){
			$this->formfilter->setFilterValue('create_begin',date('Y-m-d'));
			$this->formfilter->setFilterValue('create_end',date('Y-m-d'));
		}
		$this->formfilter->addFilter('create_begin', 'where',array('create_time >= ',strtotime( filterValue('create_begin').' 00:00:00')));
		$this->formfilter->addFilter('create_end', 'where',array('create_time <= ',strtotime( filterValue('create_end').' 23:59:59')));
		
		$this->assign('lst', $this->ROrderLog->getVerifyList($type));
		$this->assign('types', $this->types);
		$this->assign('type', $type);
		$this->layout();
	}
}