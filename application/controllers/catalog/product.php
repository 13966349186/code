<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 商品模块-商品管理
 */
class Product extends AdminController {
	/** 步骤1需要的参数规则 */
	public $_validation_pre = null;
	/** 步骤2需要的参数规则 */
	public $_validation = null;
	function __construct(){
		parent::__construct();
		$this->_required['addpre'] = ADDPOWER;
		$this->_required['batch'] = EDITPOWER;
		$this->_required['edit'] = 0;
		$this->load->model('MProduct');
		$this->load->library('Currency');
		//分开定义两个步骤页面的检查参数规则
		$this->_validation_pre =  array(
			array('field'=>'game_id', 'label'=>'游戏', 'rules'=>'required|integer')
			,array('field'=>'type_id', 'label'=>'类型', 'rules'=>'required|integer')
		);
		$this->load->library('Games');
		$this->load->helper('ioss_extend');
	}
	/** 商品列表 */
	function index(){
		$actions= array();
		if ($this->p->edit) {
			$actions ['recomend'] = '推荐';
			$actions ['unforbid'] = '启用';
			$actions ['forbid'] = '禁用';
		}
		$this->load->library('Types');
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('parent_id', 'where');
		$this->formfilter->addFilter('category_id', 'where');
		$this->formfilter->addFilter('product.name', 'like');
		$this->formfilter->addFilter('product.state', 'where');
		$this->formfilter->addFilter('type_id', 'where');
		$this->formfilter->addFilter('category.game_id', 'where');
		$limit = $this->pagination($this->MProduct->getCount());
		$lst = $this->MProduct->getList($limit, filterValue('sort'));
		$this->assign('types', $this->types);
		$this->assign('actions', $actions);
		$this->assign('lst', $lst);
		$this->layout();
	}
	
	/** 添加商品步骤1 */
	function addpre(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation_pre);
		if($this->form_validation->run()==TRUE){
			$this->load->library('Types');
			$type_id = $this->input->get_post('type_id');
			$model = $this->types->getModel($type_id);
			if($model && $url = ioss_extend_route('product/add', $model)){
				redirect(site_url($url . '/' . $type_id));
			}
		}
		//展示步骤1的页面
		$this->assign('breadName', '添加商品');	
		$this->layout('formpre');
	}
	
	function edit($id){
		if(!$id || !($product = $this->MProduct->getOne(Array('id'=>$id)))){
			show_error('当前商品不存在！');
		}
		$this->load->library('Types');
		$model = $this->types->getModel($product->type_id);
		if($model && $url = ioss_extend_route('product/edit', $model)){
			redirect(site_url($url . '/' . $product->id ));
		}
		show_error('配置数据异常！');
	}
	
	/** 批量处理 */
	function batch(){
		$idTimeArr = $this->input->post('id_time');
		$success = false;
		if(!is_array($idTimeArr) || !$idTimeArr){
			errorAndRedirect('请选择要操作的商品！');
		}
		$user_op = $this->input->post('user_op');
		if("recomend" == $user_op){
			//批量推荐
			$success = $this->MProduct->changeForbidden($idTimeArr, MProduct::STATE_RECOMEND);
		}else if("forbid" == $user_op){
			//批量禁用
			$success = $this->MProduct->changeForbidden($idTimeArr, MProduct::STATE_DISABLE);
		}else if("unforbid" == $user_op){
			//批量启用
			$success = $this->MProduct->changeForbidden($idTimeArr, MProduct::STATE_ENABLE);
		}else{
			errorAndRedirect('未知操作！');
		}
		if($success){
			successAndRedirect(l('edit_success'));
		}else{
			errorAndRedirect(l('data_fail'));
		}
	}
	
	/**
	 * 删除记录
	 * @param integer $id 标识
	 * @param integer $update_time 更新时间
	 */
	function delete($id, $update_time = 0){
		/**
		 * @todo 待测试
		 */
		if(!$id || !$obj = $this->MProduct->getOne(Array('id'=>$id))){
			show_error('当前商品不存在');
		}
		$this->load->library('Types');
		$model = $this->types->getModel($obj->type_id);
		if($model){
			show_error('扩展类型商品不能删除');
		}
		$this->load->model('MProduct');
		if($this->$modelName->delete($id, $update_time)){
			successAndRedirect(l('delete_success'));
		}else{
			errorAndRedirect(l('data_fail'));
		}
	}
}
