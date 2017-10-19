<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 订单模块-发货
 */
class Delivery extends AdminController {
	function __construct(){
		parent::__construct();
		$this->load->model('MOrder');
		$this->load->model('MOrderLog');
		$this->load->model('MOrderProduct');
		$this->load->model('MOrderAttribute');
		$this->_required['view'] = VIEWPOWER;
	}
	
	function view($order_product_id){
		$this->load->helper('ioss_extend');
		if(!is_numeric($order_product_id) || !($obj = $this->MOrderProduct->getDtlById($order_product_id))){
			model_error('当前订单商品不存在!');
		}
		if($url = ioss_extend_route($this->_thisController . '/' . $this->_thisMethod, $obj->model, $obj->game_code, $obj->type_code)){
			//跳转到Model对应的控制器
			redirect($url . '/' . $order_product_id); 
		}
		$order = $this->MOrder->getOne(Array('id'=>$obj->order_id));
		$read_only = ($order->state != MOrder::STATE_OPEN) || (!$this->p->edit);
		$attrs = $this->MOrderAttribute->getAll(Array('order_id'=>$obj->order_id));
		$this->assign('order', $order);
		$this->assign('read_only', $read_only);
		$this->assign('attrs', $attrs);
		$this->assign('obj', $obj);
		$this->layout_modal();
	}
	
	/**
	 * 更新订单商品的发货状态
	 * @param int $order_product_id
	 */
	function edit($order_product_id){
		$_validation =array(
				array('field'=>'delivery_state', 'label'=>'发货状态', 'rules'=>'required|integer')
				,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'required')
		);
		$this->load->library('Form_validation');
		$this->load->helper('populate');
		$vo = $this->MOrderProduct->getOne(Array('id'=>$order_product_id));
		$order = $this->MOrder->getOne(Array('id'=>$vo->order_id));
		if(!$vo || !$order){
			model_error('当前订单商品不存在!');
		}
		if($this->_isReadOnyl($order)){
			$msg = '订单状态为"' . element($order->state, $this->MOrder->states) . '"，不能修改发货状态！';
			model_error($msg);
		}
	
		$this->form_validation->set_rules($_validation);
		if($this->form_validation->run()){
			$vo = populate($vo, $this->form_validation->post());
			$log = (Object)Array('type'=>MOrderLog::TYPE_DELEVERY, 'note'=>'', 'admin_id'=>$this->_user->id, 'admin'=>$this->_user->name.'('.$this->_user->account.')');
			if($this->MOrderProduct->delivery($vo, $order, $log)){
				if($vo->delivery_state == MOrder::DELEVERY_STATE_DELEVERED){
					$this->_autoClose($order);
				}
				model_success(l('edit_success'));
			}else{
				model_error(l('data_error'));
			}
		}
	}
	
	/**
	 * 根据订单当前状态判断是否自动关闭订单
	 * @param int $oder_id
	 * @return boolean
	 */
	protected function _autoClose($order){
		if($order->state == MOrder::STATE_OPEN
				&& ($order->payment_state == MOrder::PAY_STATE_PAID || $order->payment_state == MOrder::PAY_STATE_PART)
				&& $order->delivery_state == MOrder::DELEVERY_STATE_DELEVERED){
			//修改订单状态
			$order->state = MOrder::STATE_CLOSED;
			return $this->MOrder->update($order);
		}
		return true;
	}
	
	/**
	 * 判断订单是否为只读状态
	 * @param object $order
	 */
	protected function _isReadOnyl($order){
		return ($order->state != MOrder::STATE_OPEN) || (!$this->p->edit);
	}
}
