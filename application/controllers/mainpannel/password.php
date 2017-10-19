<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 权限模块-修改密码
 */
class Password extends AdminController {
	public $_validation = null;

	function __construct(){
		parent::__construct();
	}
	
	function index(){
		$validation =  array(
				array('field'   => 'oldpwd','label'   => '原密码',	'rules'   => 'required|callback__validatePassword'),
				array('field'   => 'newpwd','label'   =>'新密码','rules'   => 'trim|required|min_length[6]'),
				array('field'   => 'newpwdrpt','label'   => '确认新密码','rules'   => 'trim|required|matches[newpwd]')
		);
		$this->load->helper('populate');
		$this->load->library('form_validation');
		$this->form_validation->set_rules($validation);
		if($this->form_validation->run()===true){
			$this->load->model('MAdmin');
			$err = $this->MAdmin->changePwd($this->useridentity->getUser(), $this->input->get_post("oldpwd"), $this->input->get_post("newpwd"));
			if($err === true){
				successAndRedirect('修改完成');
			}
			$this->form_validation->set_error('oldpwd','原密码不正确！');
		}
		$this->layout();
	}
}
