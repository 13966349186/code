<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 销售模块-邮件日志
 */
class MailLog extends AdminController {
	function __construct(){
		parent::__construct();
		$this->load->model('MMailLog');
		$this->load->model('MMailTemplate');
		$this->load->library('Sites');
		$this->_required['view'] = VIEWPOWER;
	}
	/** 邮件日志查询 */
	function index(){
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('mail_log.tmp_code', 'where');
		$this->formfilter->addFilter('mail_log.site_id', 'where');
		$this->formfilter->addFilter('mail_log.email_to', 'where');
		$this->formfilter->addFilter('mail_log.send_state', 'where');
		if($begintime = filterValue('create_begin')){
			$this->formfilter->addFilter('create_begin', 'where',array('mail_log.create_time >= ',strtotime($begintime.' 00:00:00')));
		}
		if($endtime = filterValue('create_end')){
			$this->formfilter->addFilter('create_end', 'where',array('mail_log.create_time <= ',strtotime($endtime.' 23:59:59')));
		}
		$limit = $this->pagination($this->MMailLog->getCount());
		$lst = $this->MMailLog->getList($limit);
		$this->assign('lst', $lst);
		$this->assign('tmplates', parse_options($this->MMailTemplate->getAll(Array('state'=>MMailTemplate::STATE_ENABLED)), 'code', 'name'));
		$this->layout();
	}
	function view($id){
		if(!($obj = $this->MMailLog->getOne(Array('id'=>$id)))){
			show_404();
		}
		$this->assign('obj', $obj);
		$this->assign('tmplates', parse_options($this->MMailTemplate->getAll(Array('state'=>MMailTemplate::STATE_ENABLED)), 'code', 'name'));
		$this->layout();
	}
}
