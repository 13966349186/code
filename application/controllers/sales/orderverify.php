<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 订单模块-订单验证
 */
class OrderVerify extends AdminController {

	function __construct(){
		parent::__construct();
		$this->load->model('MOrder');
		$this->assign('orderStates', $this->MOrder->getState());
		$this->assign('paymentStates', $this->MOrder->getPayState());
		$this->assign('deliveryStates', $this->MOrder->getDeliveryState());
		$this->assign('riskStates', $this->MOrder->getRiskState());
		$this->load->library('Form_validation');
		$this->load->model('MOrderVerificationInfo');
		$this->_required['view'] = VIEWPOWER;
		$this->_required['verify_tel'] = ADDPOWER;
		$this->_required['verify_idcard'] = ADDPOWER;
		$this->_required['list_tel'] = VIEWPOWER;
		$this->_required['list_idcard'] = VIEWPOWER;
		$this->_required['delete'] = DELETEPOWER;
	}
	
	/**
	 * 待验证订单列表
	 */
	function index(){
		$this->load->library('Currency');
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->load->library('PaymentMethod');
		$this->load->library('Games');
		$this->load->library('Types');
		
		$this->formfilter->addFilter('orders.state', 'where', array('orders.state',MOrder::STATE_OPEN));
		$this->formfilter->addFilter('orders.risk', 'where', array('orders.risk',MOrder::RISK_MEDIUM));
		$lst =  $this->MOrder->getList(array('limit'=>300, 'offset'=>0));
		$this->assign('lst', $lst);
		$this->assign('paymentMethods', $this->paymentmethod->toArray());
		
		$this->layout();
	}
	/**
	 * 定时取得新订单数量(ajax使用)
	 * @param int $time
	 */
	function getCount($time = 0){
		$this->load->library('FormFilter');
		$this->formfilter->addFilter('orders.state', 'where', array('orders.state',MOrder::STATE_OPEN));
		$this->formfilter->addFilter('orders.risk', 'where', array('orders.risk',MOrder::RISK_MEDIUM));
		$this->formfilter->addFilter('orders.update_time', 'where', array('orders.update_time >', (int)$time));
		echo  $this->MOrder->getCount();
	}
	
	/**
	 * 订单验证信息
	 * @param integer $order_id 订单标识
	 */
	function view($order_id){
		if(!is_numeric($order_id) || !($order = $this->MOrder->getById($order_id))){
			show_error('订单标识不存在!');
		}
		$validation = array(
			array('field'=>'risk', 'label'=>'风险等级', 'rules'=>'required|integer')
			,array('field'=>'note', 'label'=>'备注', 'rules'=>'required|max_length[128]')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'required')
		);
		$this->load->model('MOrderLog');
		$this->load->library('form_validation');
		$this->load->library('Currency');
		$this->load->model('MOrderIndex');
		$this->load->model('MPaypalTxn');
		
		//保存数据
		$this->form_validation->set_rules($validation);
		if($this->p->edit && $this->form_validation->run()){
			$orderInfo = new stdClass();
			$orderInfo->id = $order->id;
			$orderInfo->payment_state = $order->payment_state;
			$orderInfo->delivery_state = $order->delivery_state;
			$orderInfo->state = $order->state;
			$orderInfo->risk = $this->input->get_post('risk');
			$orderInfo->update_time = $this->input->get_post('update_time');
			$log = Array('type'=>MOrderLog::TYPE_VERIFY, 'note'=>$this->input->get_post('note'), 'admin_id'=>$this->_user->id, 'admin'=>$this->_user->name.'('.$this->_user->account.')');
			if($this->MOrder->save($orderInfo, (Object)$log)){
				successAndRedirect(l('edit_success'), site_url($this->_thisModule.$this->_thisController. '/'. $this->_thisMethod . '/' . $order->id));
			}else{
				error(l('data_error'));
			}
		}
		$paypal = $this->MPaypalTxn->getOne(Array('custom'=>$order->id))? : $this->MPaypalTxn->createVo();  //查Paypal信息
		$lst = $this->MOrderVerificationInfo->getAll(Array('order_id'=>$order->id));  //查验证信息
		
		//查Paypal验证记录
		$tel_flg = strlen($paypal->payer_id) > 0 && $this->MOrderVerificationInfo->getOne(Array('verify_key'=>MOrderVerificationInfo::KEY_PHONE, 'payer_id'=>$paypal->payer_id));
		$idcard_flg = strlen($paypal->payer_id) > 0 && $this->MOrderVerificationInfo->getOne(Array('verify_key'=>MOrderVerificationInfo::KEY_IDCARD, 'payer_id'=>$paypal->payer_id));

		//查日志
		$logs = $this->MOrderLog->getAll('order_id ='.$order->id.' and type ='.MOrderLog::TYPE_VERIFY.' order by id desc');

		//查询关联订单数量和风险等级
		$count = 0;
		$risk = MOrder::RISK_MEDIUM;
		$indexs = $this->MOrderIndex->getAll(Array('order_id'=>$order->id));
		if($indexs){
			foreach ($indexs as $vo){
				$relative_index[$vo->table_name.'.'.$vo->col_name] = $vo->col_value;
			}
			$rows = $this->MOrder->getCountGroupByRisk($relative_index, $order->id);
			foreach ($rows as $row){
				$count += $row->num;
				$risk = ($row->num > 0) ? $row->risk : $risk;
			}
		}
		$this->assign('order', $order);
		$this->assign('ip_info', @geoip_record_by_name($order->user_ip));
		$this->assign('paypal', $paypal);
		$this->assign('lst', $lst);
		$this->assign('tel_flg', $tel_flg);
		$this->assign('idcard_flg', $idcard_flg);
		$this->assign('logs', $logs);
		$this->assign('count', $count);
		$this->assign('risk', $risk);
		$this->layout();
	}
	
	/**
	 * 删除验证信息
	 * @param integer $verify_id order_verification_info表主键
	 * @param integer $order_id 订单标识
	 * @param integer $update_time 更新时间
	 */
	function delete($verify_id, $order_id, $update_time){
		if(((int)$verify_id) . '' !== $verify_id || ((int)$order_id) . '' !== $order_id || ((int)$update_time) . '' !== $update_time){
			show_error(l('id_or_updated_not_null'));
		}
		$this->load->model('MOrderVerificationInfo');
		if(!$this->MOrderVerificationInfo->delete($verify_id, $order_id, $update_time)){
			error(l('data_error'));
		}else{
			success(l('delete_success'));
		}
		redirect(site_url($this->_thisModule.$this->_thisController.'/view/'.$order_id));
	}
	
	function verify_tel($order_id){
		$this->assign('thisControllerName', '电话验证');
		$_validation =array(
			'verify_value' => array('field' => 'verify_value', 'label' => '电话', 'rules' => 'required|max_length[15]')
			,'note' => array('field' => 'note', 'label' => '备注', 'rules' => 'max_length[128]')
		);
		$this->_saveVerify($order_id, $_validation, MOrderVerificationInfo::KEY_PHONE);
	}
	
	function verify_idcard($order_id){
		$this->assign('thisControllerName', '上传证件');
		$this->load->helper('cms');	
		$_validation =array(
			'verify_value' => array('field' => 'verify_value', 'label' => '附件', 'rules' => 'upload|mult|allowed_types[jpg,gif,png]|max_length[255]|required')
			,'note' => array('field' => 'note', 'label' => '备注', 'rules' => 'max_length[128]')
		);
		$this->_saveVerify($order_id, $_validation, MOrderVerificationInfo::KEY_IDCARD);
	}
	
	private function _saveVerify($order_id, $validation, $type){
		if(((int)$order_id) . '' !== $order_id){
			model_error(l('id_or_updated_not_null'));
		}
		if(!($order = $this->MOrder->getById($order_id))){
			model_error('订单标识不存在!');
		}
		$this->assign('order', $order);
		//保存数据
		$this->load->library('Form_validation');
		$this->form_validation->set_rules($validation);
		if($this->form_validation->run()){
			$this->load->helper('populate');
			$newObj = $this->MOrderVerificationInfo->createVo();
			$newObj = populate($newObj, $this->form_validation->post());
			$newObj->verify_key = $type;
			$newObj->order_id = $order->id;
			$newObj->payment_method = $order->payment_method;
			$newObj->user_id = $order->user_id;
			$newObj->admin_id = $this->_user->id;
			$newObj->admin = $this->_user->name.'('.$this->_user->account.')';;
			//查Paypal信息
			$this->load->model('MPaypalTxn');
			if($paypal = $this->MPaypalTxn->getOne(Array('custom'=>$order->id))){
				$newObj->payer_id = $paypal->payer_id;
			}
			if($this->MOrderVerificationInfo->add($newObj)){
				model_success(l('add_success'));
			}else{
				model_error(l('data_error'));
			}
		}
		$this->layout_modal();
	}
	
	function list_idcard($order_id){
		$this->assign('thisControllerName', '身份证件验证记录');
		if(((int)$order_id) . '' !== $order_id){
			model_error(l('id_or_updated_not_null'));
		}
		if(!($order = $this->MOrder->getById($order_id))){
			model_error('订单标识不存在!');
		}
		//查Paypal信息
		$this->load->model('MPaypalTxn');
		if(!($paypal = $this->MPaypalTxn->getOne(Array('custom'=>$order->id)))){
			model_error('Paypal信息不存在!');
		}
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->load->model('MPaypalTxn');
		$this->formfilter->addFilter('payer_id', 'where',array('payer_id = ', $paypal->payer_id));
		$this->formfilter->addFilter('verify_key', 'where',array('verify_key = ', '2'));
		$lst = $this->MOrderVerificationInfo->getList($this->pagination($this->MOrderVerificationInfo->getCount()));
		$this->assign('paypal', $paypal);
		$this->assign('order', $order);
		$this->assign('lst', $lst);
		$this->layout_modal();
	}
	
	function list_tel($order_id){
		$this->assign('thisControllerName', '电话验证记录');
		if(((int)$order_id) . '' !== $order_id){
			model_error(l('id_or_updated_not_null'));
		}
		if(!($order = $this->MOrder->getById($order_id))){
			model_error('订单标识不存在!');
		}
		//查Paypal信息
		$this->load->model('MPaypalTxn');
		if(!($paypal = $this->MPaypalTxn->getOne(Array('custom'=>$order->id)))){
			model_error('Paypal信息不存在!');
		}
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->load->model('MPaypalTxn');
		$this->formfilter->addFilter('payer_id', 'where',array('payer_id = ', $paypal->payer_id));
		$this->formfilter->addFilter('verify_key', 'where',array('verify_key = ', '1'));
		$lst = $this->MOrderVerificationInfo->getList($this->pagination($this->MOrderVerificationInfo->getCount()));
		$this->assign('paypal', $paypal);
		$this->assign('order', $order);
		$this->assign('lst', $lst);
		$this->layout_modal();
	}
	
}
