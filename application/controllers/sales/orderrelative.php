<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 订单模块-关联订单
 */
class OrderRelative extends AdminController {
	function __construct(){
		$this->disp_menu_item_ctrl = 'sales/order';
		parent::__construct();
		$this->load->model('MOrder');
		$this->load->model('MOrderPayment');
		$this->assign('orderStates', $this->MOrder->getState());
		$this->assign('paymentStates', $this->MOrder->getPayState());
		$this->assign('deliveryStates', $this->MOrder->getDeliveryState());
		$this->assign('riskStates', $this->MOrder->getRiskState());
		$this->load->library('Currency');
		$this->load->library('Games');
	}
	/**
	 * 订单详细
	 * @param integer $order_id 订单标识
	 */
	function index($order_id, $stype=''){
		if(((int)$order_id) . '' !== $order_id){
			show_error(l('id_or_updated_not_null'));
		}
		if(!($order = $this->MOrder->getById($order_id))){
			show_error('订单标识不存在!');
		}
		$this->load->library('Types');
		$this->load->library('FormFilter', Array('method'=>'get'));
		$this->load->helper('formfilter');
		$this->load->model('MPaymentMethod');
		
		//订单关联索引
		$stypes = Array(
				'paypal_txn.payer_email'=>'Paypal帐号',
				'orders.user_email'=>'邮箱',
				'orders.user_phone'=>'电话',
				'orders.user_ip'=>'IP地址',
				'order_attributes'=>'游戏账号');
		$this->assign('stypes', $stypes);
		$this->load->model('MOrderIndex');

		//$this->formfilter->addFilter('orders.id', 'where', array('orders.id !=', $order->id));
		$relative_index= array();
		$indexs = $this->MOrderIndex->getAll(Array('order_id'=>$order->id));
		foreach ($indexs as $vo){
			$key = $vo->table_name.'.'.$vo->col_name;
			if($stype &&  strstr($key,$stype) === FALSE ){
				continue;
			}
			$relative_index[$key] = $vo->col_value;
		}
		if($relative_index){
			$limit = $this->pagination($this->MOrder->getCount($relative_index));
			$lst = $this->MOrder->getList($limit, null, $relative_index);
		}else{
			$limit = $this->pagination(0);
			$lst = Array();
		}
		$paymentMethods = parse_options($this->MPaymentMethod->getAll());
		
		$this->assign('order', $order);
		$this->assign('lst', $lst);
		$this->assign('paymentMethods', $paymentMethods);
		$this->assign('stype', $stype);
		$this->layout();
	}
 }

  