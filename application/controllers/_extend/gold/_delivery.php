<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 订单模块-发货
 */
include_once  APPPATH . '/controllers/sales/delivery.php';

class _Delivery extends Delivery {
	function __construct(){
		parent::__construct();
		$this->p = UserPower::getPermisionInfo('sales/delivery');
		$this->load->model('MOrderProductGold');
	}
	
	function view($order_product_id){
		if(!is_numeric($order_product_id) || !($obj = $this->MOrderProductGold->getDtlById($order_product_id))){
			model_error('当前订单商品不存在!');
		}
		$order = $this->MOrder->getOne(Array('id'=>$obj->order_id));
		$this->load->library('Currency');
		$this->load->library('Types');
		$this->load->library('Games');
		$this->load->library('Categories',array($order->game_id));
		$attrs = $this->MOrderAttribute->getAll(Array('order_id'=>$obj->order_id));
		$this->assign('thisControllerName', '订单发货（金币）');
		$this->assign('order', $order);
		$this->assign('attrs', $attrs);
		$this->assign('read_only', $this->_isReadOnyl($order));
		$this->assign('obj', $obj);
		$this->layout_modal();
	}
}
