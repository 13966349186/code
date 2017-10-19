<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站模块-支付账号
 */
class PaymentAccount extends AdminController {
	public $_validation = null;

	function __construct(){
		parent::__construct();
		$this->load->model('MPaymentMethod');
		$this->load->model('MPaymentAccount');
		
		$this->_validation =  array(
			array('field'=>'account', 'label'=>l('payment_account'), 'rules'=>'required|max_length[128]')
			,array('field'=>'config', 'label'=>l('payment_account_config'), 'rules'=>'required|max_length[255]')
			,array('field'=>'state', 'label'=>l('payment_account_state'), 'rules'=>'required|integer')
			,array('field'=>'update_time', 'label'=>l('update_time'), 'rules'=>'')
		);
	}

	function add(){
		$this->load->library('form_validation');
		$this->_validation[] = array('field'=>'method_id', 'label'=>l('payment_method'), 'rules'=>'required|integer');
		$this->form_validation->set_rules($this->_validation);
		$this->load->helper('populate');
		$newPaymentAccount = $this->MPaymentAccount->createVo();

		if($this->form_validation->run()==TRUE){
			$newPaymentAccount = populate($newPaymentAccount, $this->form_validation->post());
			$newPaymentAccount->update_time = time();
			$this->MPaymentAccount->add($newPaymentAccount);
			successAndRedirect(l('add_success'));
		}
		
		$paymentMethods = $this->MPaymentMethod->getAll(array('state'=>MPaymentMethod::STATE_ENABLE));	
		$this->assign('paymentMethods',$paymentMethods);
		$this->assign('paymentAccount', $newPaymentAccount);
		$this->layout();
	}
	
	function edit($id){
		if(((int)$id) . '' !== $id){
			show_error(l('id_or_updated_not_null'));
		}
		if(!($paymentAccount = $this->MPaymentAccount->getById($id))){
			show_error("当前支付账号不存在!");
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$paymentAccount = populate($paymentAccount, $this->form_validation->post());
			if($this->MPaymentAccount->update($paymentAccount) !== true){
				//操作冲突
				errorAndRedirect(l('data_fail'));
			}
			successAndRedirect(l('edit_success'));
		}
		$paymentMethods = $this->MPaymentMethod->getAll();	
		$this->assign('paymentMethods',$paymentMethods);
		$this->assign('paymentAccount', $paymentAccount);
		$this->layout();
	}
	
	function index(){
		$paymentMethods = $this->MPaymentMethod->getAll();
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('method_id','where');
		$this->formfilter->addFilter('state','where');
		$limit = $this->pagination($this->MPaymentAccount->getCount());
		$paymentAccounts = $this->MPaymentAccount->getList($limit);
		$this->assign('paymentAccounts',$paymentAccounts);
		$this->assign('paymentMethodNames',object_column($paymentMethods, 'name','id'));
		$this->assign('paymentMethodCodes',object_column($paymentMethods, 'code','id'));
		$this->layout();
	}
	
	/**
	 * 删除支付账号
	 */
	function delete($id,$update_time = NULL){
		if(((int)$id) . '' !== $id){
			show_error(l('id_or_updated_not_null'));
		}
		if(!($paymentAccount = $this->MPaymentAccount->getById($id))){
			show_error("当前支付账号不存在！");
		}
		$this->load->model('MSitePayment');
		$lst = $this->MSitePayment->getAll(array('account_id'=>$id));
		if(count($lst) > 0){
			errorAndRedirect('当前支付账号已被引用，不能删除！', site_url($this->_thisModule . $this->_thisController));
		}
		if(!$this->MPaymentAccount->delete($id,$update_time)){
			errorAndRedirect('数据库操作失败！', site_url($this->_thisModule . $this->_thisController));
		}else{
			successAndRedirect('删除成功！', site_url($this->_thisModule . $this->_thisController));
		}
	}
}
