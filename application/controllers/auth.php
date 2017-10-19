<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auth extends CI_Controller{
	private $_validation = null;
	private $_user = null;
	function __construct(){
		parent::__construct();
		$this->load->model('MAdmin');
		$this->load->library('UserIdentity',array('checkLogin'=>FALSE));
		$this->_validation =  array(
			array(
				'field'   => 'account',
				'label'   => l('account'),
				'rules'   => 'required|max_length[100]'
			),array(
				'field'   => 'password',
				'label'   => l('password'),
				'rules'   => 'required|max_length[100]|callback__validatePassword'
			),array(
				'field'   => 'remember',
				'label'   => '',
				'rules'   => ''
			)
		);
	$this->output->enable_profiler(false);
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation);
	}
	
	//默认方法
	function index(){
		$this->login();
		
	}
	
	//登录方法
	function login() {
		$account = $this->input->post('account', TRUE);
		
$this->_user = $this->MAdmin->getByName($account);
	
		var_dump($this->form_validation->run());
		if($this->form_validation->run()===true){
			$this->useridentity->processLogin($account, $this->input->post('password', TRUE));
			
			$this->load->helper('cookie');
			if($this->input->post('remember')){
				set_cookie('remember', '1', time()+365*24*60*60);
				set_cookie('account', $account, time()+365*24*60*60);
			}else{
				set_cookie('remember', '', time()+365*24*60*60);
				set_cookie('account', '', time()+365*24*60*60);
			}
			$defaultCtrl = $this->session->userdata('login_redirect_url');
			if(!$defaultCtrl){
				$menuArr = $this->config->item('menu_page');
				foreach ($menuArr as $ctrl => $val) {
					if($val['group'] === GROUP_EVERYONE){
						$defaultCtrl = $ctrl;
						break;
					}
				}
			}else{
				$this->session->set_userdata('login_redirect_url', '');
			}
			$this->load->model("MAdminLogin");
			$adminLogin = new stdClass();
			$adminLogin->admin_id =$this->_user->id;
			$adminLogin->account =$this->_user->account;
			$adminLogin->name =$this->_user->name;
			$adminLogin->login_ip = $this->input->ip_address();
			$adminLogin->login_time = time();
			$ret = $this->MAdminLogin->save($adminLogin);
			
			if($ret){			
				redirect($defaultCtrl);
			}else{
				errorAndRedirect("记录用户登录日志失败!");
			}
		}
		$this->load->view('login.tpl');
	}
	
	function _validatePassword(){
		$password = $this->input->post('password', TRUE);
		if($this->_user && !$this->useridentity->isForbidden($this->_user) && $this->useridentity->validatePassword($this->_user, $password)){
			return true;
		}else{
			$this->form_validation->set_message('_validatePassword',l('user_login_faild'));
			sleep(1);  //防暴力猜密码
			return false;
		}
	}
	
	
	function logout(){
		$this->useridentity->logout();
		redirect('auth');
	}

}
