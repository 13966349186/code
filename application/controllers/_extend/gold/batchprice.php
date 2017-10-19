<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 商品模块-金币批量定价 */
class BatchPrice extends AdminController {
	public $_validation = null;
	function __construct(){
		parent::__construct();
		$this->_required['index'] = EDITPOWER; 
		$this->load->model('MProductGold');
	}
	
	function index($game_id = '', $category_id='', $type_id=''){
		$this->load->library('Games');
		$this->assign('game_id',$game_id);
		$this->assign('category_id',$category_id);
		$this->assign('type_id',$type_id);
		$this->layout();
	}

	function edit($game_id,$category_id,$type_id){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('price', '价格', 'required|callback__checkPrice');
		if($this->form_validation->run()==TRUE){
			$prices = $this->input->post('price');
			$times = $this->input->post('update_time');
			foreach ($prices as $id=>$price){
				$vos[] = (Object)Array('id'=>$id, 'price'=>$price, 'update_time'=>(int)$times[$id]);
			}
			if($this->MProductGold->GoldPriceBatch($vos)){
				successAndRedirect(l('edit_success'),current_url());
			}else{
				errorAndRedirect(l('data_fail'),current_url());
			}
		}
		$this->load->model('MGame');
		$this->load->model('MCategory');
		$this->load->model('MType');
		$game = $this->MGame->getById($game_id);
		if(!$game){
			show_error('指定游戏不存在！');
		}
		$type = $this->MType->getById($type_id);
		if(!$type){
			show_error('指定商品类型不存在！');
		}
		$this->load->library('Categories',array($game_id));
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('product.category_id', 'where',array('product.category_id =', $category_id));
		$this->formfilter->addFilter('product.type_id', 'where',array('product.type_id =', $type_id));
		$lst = $this->MProductGold->getList();
		$this->assign('back_url',site_url($this->_thisModule . $this->_thisController .'/index') . '/' . $game_id . '/' . $category_id . '/' . $type_id);
		$this->assign('game',$game);
		$this->assign('type',$type);
		$this->assign('path',$this->categories->getPath($category_id));
		$this->assign('lst', $lst);
		$this->layout('view.tpl');
	}
	
	/**
	 * 检查表单提交的价格数据
	 * @param array $data
	 * @return boolean
	 */
	function _checkPrice($data){
		$msg = '';
		foreach ($data as $v){
			if(is_null($v)){
				$msg = '价格不能为空';
			}elseif(!is_numeric($v)){
				$msg = '价格必须是数字';
			}elseif($v < 0.01){
				$msg = '价格必须大于 0.01';
			}elseif($v > 99999999){
				$msg = '价格必须小于 99999999';
			}
			if($msg){
				$this->form_validation->set_message('_checkPrice', $msg);
				return false;
			}
		}
		return true;
	}
}
