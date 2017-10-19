<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 订单模块-订单商品添加(金币)
 */
class _OrderProduct extends AdminController {
	public $_validation =  array(
		array('field'=>'category_id', 'label'=>'目录', 'rules'=>'integer|required')
		,array('field'=>'product_id', 'label'=>'商品', 'rules'=>'integer|required')
		,array('field'=>'price', 'label'=>'价格', 'rules'=>'numeric|required')
		,array('field'=>'num', 'label'=>'商品数量', 'rules'=>'is_natural_no_zero|required')
	);
	protected $disp_menu_item_ctrl = 'catalog/product';
	function __construct(){
		parent::__construct();
		$this->p = UserPower::getPermisionInfo($this->disp_menu_item_ctrl);
		$this->assign('product_ctrl', $this->disp_menu_item_ctrl);
		$this->assign('p', $this->p);
		$this->load->model('MOrder');
		$this->load->model('MProduct');
		$this->load->model('MGame');
		$this->load->model('MCategory');
		$this->load->model('MType');
		$this->load->library('form_validation');
		$this->load->library('Currency');
	}
	function add($product_id){
		$this->assign('thisControllerName', '<i class="fa fa-shopping-cart"></i> 订单添加商品');
		$this->load->library('Form_validation');
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()){
			$this->load->helper('populate');
			$sel_product_id = $this->input->post('product_id');
			if(!$sel_product = $this->MProduct->getOne(Array('id'=>$sel_product_id))){
				error('商品不存在!');
			}
			if(!$sel_type = $this->MType->getOne(Array('id'=>$sel_product->type_id))){
				error('类型不存在!');
			}
			$this->load->model('MProductGold');
			$sel_product = $this->MProductGold->getDtlByProductId($sel_product_id);
			$sel_product->num = $this->input->post('num');
			$sel_product->price = $this->input->post('price');
			$sel_product->price_disp = $this->currency->format($sel_product->price, DEFAULT_CURRENCY);
			$sel_product->total_price_disp = $this->currency->format($sel_product->price*$sel_product->num, DEFAULT_CURRENCY);
			$this->assign('sel_product', $sel_product);
			$this->assign('sel_json', parse_js(json_encode($sel_product)));
		}
		if(!$product = $this->MProduct->getOne(Array('id'=>$product_id))){
			modal_error('商品不存在!');
		}
		if(!$type = $this->MType->getOne(Array('id'=>$product->type_id))){
			modal_error('类型不存在!');
		}

		$this->load->model('MProductGold');
		$product = $this->MProductGold->getDtlByProductId($product_id);

		$game = $this->MGame->getOne(Array('id'=>$type->game_id));
		$categorys = $this->MCategory->getAll(Array('game_id'=>$game->id));

		$this->assign('product', $product);
		$this->assign('type', $type);
		$this->assign('game', $game);
		$this->assign('categorys', $categorys);

		$this->layout_modal();
	}
	function AJAX_readProducts($category_id){
		$this->load->model('MProductGold');
		$lst = $this->MProductGold->getFullAll(Array('product.category_id'=>$category_id));
		$rtn = Array();
		foreach($lst as $v){
			$rtn[] = Array('id'=>$v->id, 'name'=>$v->name, 'price'=>$v->price, 'gold_num'=>$v->gold_num);
		}
		echo json_encode($rtn);
		die();
	}
}
