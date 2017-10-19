<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 订单模块-支付详细
 */
class OrderPayment extends AdminController {
	function __construct(){
		$this->disp_menu_item_ctrl = 'sales/order';
		parent::__construct();
		$this->_required['confirm'] = EDITPOWER;
		$this->_required['pay'] = EDITPOWER;
		$this->_required['refund'] = EDITPOWER;
		$this->_required['dtl'] = VIEWPOWER;
		$this->load->model('MOrder');
		$this->load->model('MOrderPayment');
		$this->load->library('Currency');
		$this->load->helper('ioss_order');
	}
	/**
	 * 订单详细
	 * @param integer $order_id 订单标识
	 */
	function index($order_id){
		if(!is_numeric($order_id) || !($order = $this->MOrder->getOne(Array('id'=>$order_id)))){
			show_error('当前订单不存在!');
		}
		$lst = $this->MOrderPayment->getAll(array('order_id'=>$order_id));
		$this->assign('order', $order);
		$this->assign('lst', $lst);
		$this->layout();
	}
	
	/**
	 * 对进行中的订单付款记录进行后续[完成/取消]操作
	 * @param integer $order_payment_id 订单付款信息ID
	 */
	function confirm($order_payment_id){
		if(!is_numeric($order_payment_id) || !($obj = $this->MOrderPayment->getOne(Array('id'=>$order_payment_id)))){
			model_error('指定的付款记录不存在!');
		}
		if($obj->state != MOrderPayment::STATE_PENDING){
			model_error('状态已经变化!');
		}
		if(!($order = $this->MOrder->getOne(Array('id'=>$obj->order_id)))){
			model_error('订单不存在!');
		}
		$title = $this->MOrderPayment->getType($obj->type);

		$validation =array(
			array('field'=>'note', 'label'=>'备注', 'rules'=>'required|max_length[120]')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
		);
		$this->load->library('Form_validation');
		$this->form_validation->set_rules($validation);
		if($this->form_validation->run()){
			$this->load->helper('populate');
			$this->load->model('MOrderLog');
			$info = populate((Object)((Array)$obj), $this->form_validation->post());
			if($this->input->get_post('op_flg')){
				$info->state = MOrderPayment::STATE_COMPLETED;
			}else{
				$info->state = MOrderPayment::STATE_CANCELLED;
			}
			$log = new stdClass();
			$log->type = MOrderLog::TYPE_PAY;
			$log->note = ($info->state == MOrderPayment::STATE_COMPLETED ?  "【确认{$title}】" : "【取消{$title}】" ) . $info->note;
			$log->admin_id = $this->_user->id;
			$log->admin = $this->_user->name.'('.$this->_user->account.')';
			if($this->MOrderPayment->save($info, NULL, $log)){
				model_success(l('edit_success'));
			}else{
				model_error(l('data_error'));
			}
		}
		$this->assign('thisControllerName', $title);
		$this->assign('title', $title);
		$this->assign('order', $order);
		$this->assign('obj', $obj);
		$this->layout_modal();
	}
	
	/**
	 * 订单付款操作
	 * @param integer $order_id 订单标识
	 */
	function pay($order_id){
		if(!is_numeric($order_id) || !($order = $this->MOrder->getOne(Array('id'=>$order_id)))){
			show_error('当前订单不存在!');
		}
		$flg = ($order->state == MOrder::STATE_OPEN || $order->state == MOrder::STATE_HOLDING) && ($order->payment_state == MOrder::PAY_STATE_UNPAIED || $order->payment_state == MOrder::PAY_STATE_PEDING || $order->payment_state == MOrder::PAY_STATE_PART);
		if(!$flg){
			model_error('状态已经变化!');
		}
		//保存数据
		$validation =array(
			array('field'=>'fee', 'label'=>'手续费', 'rules'=>'required|numeric|greater_than[-0.00001]')
			,array('field'=>'note', 'label'=>'备注', 'rules'=>'max_length[128]')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
			,array('field'=>'transcation_id', 'label'=>'流水号', 'rules'=>'required|max_length[128]|is_unique[order_payment.transcation_id]')
		);
		$payAmount = $this->MOrderPayment->getTotalAmount($order_id);
		$this->load->library('Form_validation');
		$this->form_validation->set_rules($validation);
		if($flg && $this->form_validation->run()){
			$this->load->helper('populate');
			$info = populate($this->MOrderPayment->createVo(), $this->form_validation->post());
			$info->order_id = $order->id;
			$info->order_no = $order->no;
			$info->currency = $order->currency;
			$info->type = MOrderPayment::TYPE_PAY;
			$info->state = MOrderPayment::STATE_COMPLETED;
			$info->amount = $order->amount - $payAmount;
			$info->payment_method = $order->payment_method;
			$info->admin_id = $this->_user->id;
			$info->admin = $this->_user->name.'('.$this->_user->account.')';
			$this->load->model('MOrderLog');
			$log = new stdClass();
			$log->type = MOrderLog::TYPE_PAY;
			$log->note = '【付款】'.$this->input->get_post('note');
			$log->admin_id = $this->_user->id;
			$log->admin = $this->_user->name.'('.$this->_user->account.')';
			if($this->MOrderPayment->save($info, $order, $log)){
				model_success(l('edit_success'));
			}else{
				model_error(l('data_error'));
			}
		}
		$this->load->model('MPaymentMethod');
		$this->assign('payment_method', $this->MPaymentMethod->getOne(Array('id'=>$order->payment_method)));
		$this->assign('payAmount', $payAmount);
		$this->assign('flg', $flg);
		$this->assign('order', $order);
		$this->layout_modal();
	}
	
	/**
	 * 订单退款操作
	 * @param int $order_id
	 */
	function refund($order_id){
		if(!is_numeric($order_id) || !($order = $this->MOrder->getOne(Array('id'=>$order_id)))){
			show_error('当前订单不存在!');
		}
		//保存数据
		$validation =array(
			array('field'=>'amount', 'label'=>'退款金额', 'rules'=>'required|numeric|greater_than[0]')
			,array('field'=>'fee', 'label'=>'手续费', 'rules'=>'required|numeric|greater_than[-0.00001]')
			,array('field'=>'note', 'label'=>'备注', 'rules'=>'max_length[120]')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'required')
			,array('field'=>'transcation_id', 'label'=>'流水号', 'rules'=>'required|max_length[128]|is_unique[order_payment.transcation_id]')
		);
		$flg = ($order->state == MOrder::STATE_OPEN || $order->state == MOrder::STATE_HOLDING) && ($order->payment_state == MOrder::PAY_STATE_PAID || $order->payment_state == MOrder::PAY_STATE_PART || $order->payment_state == MOrder::PAY_STATE_REVERSED);
		if(!$flg){
			model_error('订单状态已发生变化！');
		}
		$amount = $this->MOrderPayment->getTotalAmount($order_id);
		$this->load->library('Form_validation');
		$this->form_validation->set_rules($validation);
		if($this->form_validation->run()){
			if(float_compare($amount, 0) <= 0 && $order->payment_state == MOrder::PAY_STATE_REVERSED){
				model_error('订单已冻结，且款已退完！');
			}
			if(float_compare($this->input->get_post('amount'),$amount) > 0 && !$this->input->get_post('confirm_flg')){
				$this->assign('cfm_flg', '1');
			}else{
				$this->load->helper('populate');
				$info = populate($this->MOrderPayment->createVo(), $this->form_validation->post());
				$info->amount = -$info->amount;
				$info->fee = -$info->fee;
				$info->order_id = $order->id;
				$info->order_no = $order->no;
				$info->currency = $order->currency;
				$info->type = MOrderPayment::TYPE_REFUND;
				$info->state = MOrderPayment::STATE_COMPLETED;
				$info->payment_method = $order->payment_method;
				$info->admin_id = $this->_user->id;
				$info->admin = $this->_user->name.'('.$this->_user->account.')';
				$this->load->model('MOrderLog');
				$log = new stdClass();
				$log->type = MOrderLog::TYPE_PAY;
				$log->note = '【退款】'.$this->input->get_post('note');
				$log->admin_id = $this->_user->id;
				$log->admin = $this->_user->name.'('.$this->_user->account.')';
				
				$order_info = $this->MOrder->getOne(Array('id'=>$order->id));
				if(!$this->MOrderPayment->save($info, $order_info, $log)){
					model_error(l('data_error'));
				}
				//关闭订单
				$state = calc_order_state($order_info);
				if($order_info->state != $state){
					$order_info->state = $state;
					if(!$this->MOrder->update($order_info)){
						model_error( '更新订单状态失败！');
					}
				}
				model_success(l('edit_success'));
			}
		}
		if(float_compare($amount, 0) <= 0){
			$amount = '';
		}
		
		$this->load->model('MPaymentMethod');
		$this->assign('payment_method', $this->MPaymentMethod->getOne(Array('id'=>$order->payment_method)));
		$this->assign('amount', $amount);
		$this->assign('flg', $flg);
		$this->assign('order', $order);
		$this->layout_modal();
	}

	/**
	 * 修改已冻结的支付记录
	 * @param int $order_payment_id
	 */
	function unfreeze($order_payment_id){
		$this->assign('thisControllerName', '订单解冻');
		if(!is_numeric($order_payment_id) || !($orderPayment = $this->MOrderPayment->getOne(Array('id'=>$order_payment_id)))){
			show_error('参数错误!');
		}
		if($orderPayment->state == MOrderPayment::STATE_CANCELLED){
			model_error('冻结已经取消!');
		}
		if(!($order = $this->MOrder->getOne(Array('id'=>$orderPayment->order_id)))){
			show_error('未关联到订单!');
		}
		$this->assign('order', $order);
		$this->assign('orderPayment', $orderPayment);
		//保存数据
		$validation =array(
			array('field'=>'note', 'label'=>'备注', 'rules'=>'max_length[120]')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
			,array('field'=>'order_update_time', 'label'=>'更新时间', 'rules'=>'')
		);
		$this->load->library('Form_validation');
		$this->form_validation->set_rules($validation);
		if($this->form_validation->run()){
			$this->load->helper('populate');
			$this->load->model('MOrderLog');
			$orderPayment->state = MOrderPayment::STATE_CANCELLED;
			$orderPayment->update_time = $this->form_validation->post('update_time');
			$log = new stdClass();
			$log->type = MOrderLog::TYPE_PAY;
			$log->note = '【取消冻结】'.$this->input->get_post('note');
			$log->admin_id = $this->_user->id;
			$log->admin = $this->_user->name.'('.$this->_user->account.')';
			$order_info = $this->MOrder->getOne(Array('id'=>$order->id));
			if($this->MOrderPayment->save($orderPayment, $order_info, $log)){
				$state = calc_order_state($order_info);
				if($order_info->state != $state){
					$order_info->state = $state;
					if(!$this->MOrder->update($order_info)){
						model_error( '更新订单状态失败！');
					}
				}
				model_success(l('edit_success'));
			}else{
				model_error(l('data_error'));
			}
		}
		$this->layout_modal();
	}
}
