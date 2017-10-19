<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 商品模块-商品添加和编辑(金币)
 */
class _Product extends AdminController {
	public $_validation = null;
	protected $disp_menu_item_ctrl = 'catalog/product';
	protected $_model = 'gold';
	
	function __construct(){
		parent::__construct();
		$this->load->model('MProduct');
		$this->load->model('MProductGold');
		$this->load->model('MGame');
		$this->load->model('MCategory');
		$this->load->model('MType');
		$this->load->library('form_validation');
		$this->_validation =  array(
			array('field'=>'name', 'label'=>'名称', 'rules'=>'required|max_length[64]|callback__checkName')
			,array('field'=>'description', 'label'=>'描述', 'rules'=>'max_length[128]')
			,array('field'=>'category_id', 'label'=>'所属目录', 'rules'=>'required|is_natural_no_zero')
			,array('field'=>'sort', 'label'=>'排序', 'rules'=>'required|integer')
			,array('field'=>'price', 'label'=>'价格', 'rules'=>'required|numeric|max_length[64]')
			,array('field'=>'state', 'label'=>'状态', 'rules'=>'required|integer')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
			,array('field'=>'gold_num', 'label'=>'数量', 'rules'=>'required|integer')
			,array('field'=>'discount', 'label'=>'折扣率', 'rules'=>'required|is_natural_no_zero')
		);
		$this->assign('thisControllerName', '商品管理[金币]');
		$this->assign('thisControllerPath', $this->_thisModule.'product');
		$this->assign('product_ctrl', $this->disp_menu_item_ctrl);
		$this->assign('p', $this->p = UserPower::getPermisionInfo($this->disp_menu_item_ctrl));
	}
	/**
	 * 添加商品步骤2
	 * @param integer $game_id 游戏标识
	 * @param integer $category_id 目录标识
	 * @param string $type 类型Code
	 */
	function add($type_id){
		//参数的合法性检查
		if(!$type_id || !($type = $this->MType->getOne(Array('id'=>$type_id))) || $type->model != $this->_model ){
			errorAndRedirect('指定的商品类型['.$type_id.']不存在!', site_url($this->disp_menu_item_ctrl.'/addpre'));
		}
		if(!($game = $this->MGame->getOne(Array('id'=>$type->game_id)))){
			errorAndRedirect('类型['.$type_id.']所属的游戏['.$type->game_id.']不存在!', site_url($this->disp_menu_item_ctrl.'/addpre'));
		}
		//加载商品信息
		if(($id = $this->input->get('copy'))
			&& ($product = $this->MProduct->getOne(Array('id'=>$id)))
			&& ($gold = $this->MProductGold->getOne(Array('product_id'=>$id)))){
			$product->id = '';
			$gold->product_id='';
		}else{
			$product = $this->MProduct->createVo();
			$gold = $this->MProductGold->createVo();
		}

		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			//合并商品和附表属性成为一个对象，以此取画面数据
			$gold = populate($gold, $this->form_validation->post());
			$product = populate($product, $this->form_validation->post());
			$product->type_id = $type_id;
			if($this->MProductGold->add($gold, $product)){
				successAndRedirect(l('add_success'), site_url($this->disp_menu_item_ctrl));
			}
			errorAndRedirect(l('data_fail'), site_url($this->disp_menu_item_ctrl));
		}
		$this->assign('breadName', '添加商品 - 步骤2');
		$this->assign('type', $type);
		$this->assign('game', $game);
		$this->assign('gold', $gold);
		$this->assign('product', $product);
		$this->layout();
	}
	
	/**
	 * 编辑商品
	 * @param integer $id 商品标识
	 */
	function edit($id){
		//加载商品信息
		if(!is_numeric($id) 
		|| !($product = $this->MProduct->getOne(Array('id'=>$id)))
		|| !($gold = $this->MProductGold->getOne(Array('product_id'=>$id)))){
			show_error(l('当前商品不存在'));
		}
		$this->editObj = $product;
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			//合并商品和附表属性成为一个对象，以此取画面数据
			$gold = populate($gold, $this->form_validation->post());
			$product = populate($product, $this->form_validation->post());
			if($this->MProductGold->update($gold, $product)){
				successAndRedirect(l('edit_success'), site_url($this->disp_menu_item_ctrl));
			}
			//操作冲突
			error(l('data_fail'));
		}
		
		//找到该新商品的类型信息
		$type = $this->MType->getOne(Array('id'=>$product->type_id));
		$game = $this->MGame->getOne(Array('id'=>$type->game_id));
		$this->assign('type', $type);
		$this->assign('game', $game);
		$this->assign('product', $product);
		$this->assign('gold', $gold);
		$this->layout();
	}
	
	/**
	 * 删除商品
	 * @param int $id
	 */
	function delete($id,$update_time){
		if(! is_numeric($id) || ! is_numeric($update_time)){
			show_error(l('id_or_updated_not_null'));
		}
		if($this->MProductGold->delete($id,$update_time)){
			successAndRedirect(l('delete_success'), site_url($this->disp_menu_item_ctrl));
		}
		errorAndRedirect(l('data_fail'), site_url($this->_thisModule.$this->_thisController . '/edit/' .$id));
	} 
	
	/**
	 * 检查名称是否有重复
	 * @param string $name 名称
	 */
	function _checkName($name){
		$category_id = $this->input->post('category_id');
		if($category_id.'' !== ((int)$category_id).''){
			//游戏没找到，不作检查
			return true;
		}
		$where = Array('name'=>$name, 'category_id'=>$category_id);
		if(isset($this->editObj) && $this->editObj){
			$where['id <>'] = $this->editObj->id;
		}
		if($this->MProduct->getAll($where)){
			$this->form_validation->set_message('_checkName', '所选目录下，已经存在相同名称的商品！');
			return false;
		}
		return true;
	}
 }
