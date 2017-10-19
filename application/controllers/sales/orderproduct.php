<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 订单模块-订单商品
 */
class OrderProduct extends AdminController {
	private $_validation =  array(
		array('field'=>'del_jsons[]', 'label'=>'', 'rules'=>'')
		,array('field'=>'del_flgs[]', 'label'=>'', 'rules'=>'integer')
		,array('field'=>'add_ids[]', 'label'=>'', 'rules'=>'integer')
		,array('field'=>'add_jsons[]', 'label'=>'', 'rules'=>'')
		
		,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
	);
	function __construct(){
		parent::__construct();
		$this->_required['addpre'] = ADDPOWER;
		$this->load->model('MOrder');
		$this->load->model('MOrderProduct');
		$this->load->library('Currency');
		$this->load->helper('ioss_extend');
	}
	function index($order_id){
		if(((int)$order_id) . '' !== $order_id){
			model_error(l('id_or_updated_not_null'));
		}
		$order_p = UserPower::getPermisionInfo('sales/order');
		if(!$order_p->read){
			modal_error(l('user_has_nopower'));
		}
		if(!($order = $this->MOrder->getById($order_id))){
			modal_error('订单标识不存在!');
		}
		$this->assign('no_modal_head', '1');
		$this->assign('order', $order);
		$this->layout_modal();
	}
	function add_pre($order_id){
		$this->assign('thisControllerName', '选择商品');
		$this->load->library('Games');
		$this->load->library('Types');
		$this->load->model('MProduct');
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('product.category_id', 'where');
		$this->formfilter->addFilter('product.type_id', 'where');
		$this->formfilter->addFilter('category.game_id', 'where');
		$limit = $this->pagination($this->MProduct->getCount());
		$lst = $this->MProduct->getList($limit);
		$this->assign('lst', $lst);
		$this->assign('order_id', $order_id);
		$this->layout_modal();
	}
	/**
	 * 编辑购物车
	 * @param integer $order_id 订单标识
	 */
	function edit($order_id){
		if(((int)$order_id) . '' !== $order_id){
			model_error(l('id_or_updated_not_null'));
		}
		if(!($order = $this->MOrder->getOne(Array('id'=>$order_id)))){
			model_error('订单标识不存在!');
		}
		$this->assign('order', $order);
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$json_arr = Array();
			if(array_key_exists('add_jsons', $_POST) && ($add_jsons=$_POST['add_jsons'])){
				foreach ($add_jsons as $v){
					$json_arr[] = json_decode($v);
				}
			}
			$remove_arr = Array();
			if(array_key_exists('del_flgs', $_POST) && ($del_flgs=$_POST['del_flgs'])){
				for($i=0;$i<count($del_flgs);$i++){
					if($del_flgs[$i]){
						$remove_arr[] = json_decode($_POST['del_jsons'][$i]);
					}
				}
			}
			$order->update_time = $this->input->get_post('update_time');
			if(!$remove_arr && !$json_arr){
				redirect($this->_thisModule.$this->_thisController.'/index/'.$order_id);
				return;
			}
			$this->load->model('MOrderLog');
			$log = Array('type'=>MOrderLog::TYPE_EDIT, 'note'=>'【修改订单商品】', 'admin_id'=>$this->_user->id, 'admin'=>$this->_user->name.'('.$this->_user->account.')');
			if($this->MOrderProduct->updateCart($order, $remove_arr, $json_arr, $log)){
				model_success(l('edit_success'));
				return;
			}
			error(l('data_fail'));
		}else{
			error(validation_errors());
		}

		$this->assign('no_modal_head', '1');
		$this->layout_modal();
	}
}
