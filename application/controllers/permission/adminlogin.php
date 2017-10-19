<?php
class AdminLogin extends AdminController{
	
	function __construct(){
		parent::__construct();
		$this->load->model('MAdminLogin');
	}
	
	/**
	 * 获取登录日志列表
	 */
	function index(){
		if(!$this->p->read){
			//权限不足
			show_error(l('user_has_nopower'));
		}
		
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('login_ip', 'where');
		$this->formfilter->addFilter('account', 'where');
		
		if($begintime = filterValue('login_begin')){
			$this->formfilter->addFilter('login_begin', 'where',array('login_time >= ',strtotime($begintime.' 00:00:00')));
		}
		if($endtime = filterValue('login_end')){
			$this->formfilter->addFilter('login_end', 'where',array('login_time <= ',strtotime($endtime.' 23:59:59')));
		}

		$limit = $this->pagination($this->MAdminLogin->getCount());
		$adminLogin = $this->MAdminLogin->getList($limit);
		$this->assign('adminLogin',$adminLogin);
		$this->layout();
	}
	
}