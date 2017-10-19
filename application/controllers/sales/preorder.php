<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 订单模块-预订单管理
 */
class PreOrder extends AdminController {
	function __construct(){
		parent::__construct();
		$this->load->model('MOrder');
		$this->MOrder->exclude_unsubmited = false;
		$this->load->library('Currency');
		$this->load->library('Games');
		$this->load->library('Sites');
		$this->load->library('Types');
		$this->load->library('PaymentMethod');
	}
	/** 未生成订单查询 */
	function index(){
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->load->model('MPaymentMethod');
		$this->load->library('Types');
		$paymentMethods = parse_options($this->MPaymentMethod->getAll(array('state'=>MPaymentMethod::STATE_ENABLE)));
		$this->formfilter->addFilter('orders.site_id', 'where');
		$this->formfilter->addFilter('orders.state', 'where',array('orders.state = ', MOrder::STATE_UNSUBMITTED));
		$this->formfilter->addFilter('orders.no', 'where');
		if($begintime = filterValue('create_begin')){
			$this->formfilter->addFilter('create_begin', 'where',array('orders.create_time >= ',strtotime($begintime.' 00:00:00')));
		}
		if($endtime = filterValue('create_end')){
			$this->formfilter->addFilter('create_end', 'where',array('orders.create_time <= ',strtotime($endtime.' 23:59:59')));
		}

		$limit = $this->pagination($this->MOrder->getCount());
		$lst = $this->MOrder->getList($limit, 0);
		$this->assign('lst', $lst);
		$this->layout();
	}
	
	/**
	 * 生成订单
	 * @param int $order_id
	 * @param int $update_time
	 */
	function open($order_id,$update_time){
		if(!is_numeric($order_id) || !($order = $this->MOrder->getOne(Array('id'=>$order_id)))){
			show_error('当前订单不存在!');
		}
		if($order->state != MOrder::STATE_UNSUBMITTED){
			errorAndRedirect('当前订单状态错误');
		}
		$this->load->model('MOrderLog');
		$order->update_time = $update_time;
		$order->state = MOrder::STATE_OPEN;
		$log = new stdClass();
		$log->type = MOrderLog::TYPE_EDIT;
		$log->note = "【手工生成订单】";
		$log->admin_id = $this->_user->id;
		$log->admin = "{$this->_user->name}({$this->_user->account})";
		if($this->MOrder->save($order, $log, false)){
			successAndRedirect(l('edit_success'), site_url($this->_thisModule . 'order/view/' .  $order_id));
		}else{
			errorAndRedirect(l('data_error'));
		}
	}
}
