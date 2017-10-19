<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** CMS-目录管理 */
class Category extends AdminController {
	public $_validation = null;
	function __construct(){
		parent::__construct();
		$this->load->model('MCmsCategory');
		$this->load->model('MCmsModel');
		$this->_validation = Array(
			array('field'=>'site_id', 'label'=>'所属网站', 'rules'=>'required|integer')
			,array('field'=>'code', 'label'=>'目录代码', 'rules'=>'required|max_length[32]|callback__checkCode')
			,array('field'=>'name', 'label'=>'名称', 'rules'=>'required|max_length[64]|callback__checkName')
			,array('field'=>'model_id', 'label'=>'文章类型', 'rules'=>'required|integer')
			,array('field'=>'parent_id', 'label'=>'父级目录', 'rules'=>'integer|required|callback__checkParent')
			,array('field'=>'disp_sort', 'label'=>'显示顺序', 'rules'=>'required|integer')
			,array('field'=>'record_limit', 'label'=>'限制数量', 'rules'=>'required|integer')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
			,array('field'=>'note', 'label'=>'目录说明', 'rules'=>'trim|max_length[256]')
		);
		$this->load->library('Sites');
	}
	function add(){
		$obj = $this->MCmsCategory->createVo();
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$newObj = populate((Object)((Array)$obj), $this->form_validation->post());
			if($this->MCmsCategory->add($newObj)){
				successAndRedirect(l('edit_success'));
			}else{
				errorAndRedirect(l('data_fail'));
			}
		}
		$this->assign('models', $this->MCmsModel->getAll());
		$this->assign('obj', $obj);
		$this->layout();
	}
	/**
	 * 编辑文章类型
	 * @param integer $id 文章类型ID
	 */
	function edit($id){
		if(!is_numeric($id)){
			show_error(l('id_not_null'));
		}
		$obj = $this->MCmsCategory->getOne(Array('id'=>$id));
		if(!$obj){
			errorAndRedirect('未找到定义！');
		}
		$this->editObj = $obj;
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$newObj = populate((Object)((Array)$obj), $this->form_validation->post());
			if($this->MCmsCategory->update($newObj)){
				successAndRedirect(l('edit_success'));
			}else{
				errorAndRedirect(l('data_fail'));
			}
		}
		$this->assign('models', $this->MCmsModel->getAll());
		$this->assign('obj', $obj);
		$this->layout();
	}
	function delete($id, $update_time){
		if(!is_numeric($id) || !is_numeric($update_time)){
			show_error(l('id_not_null'));
		}
		$this->load->model('MCmsNode');
		if($this->MCmsNode->getOne(Array('category_id'=>$id))){
			errorAndRedirect('该目录下有数据，不能删除！');
		}
		if(!$this->MCmsCategory->delete($id, $update_time)){
			errorAndRedirect(l('data_fail'));
		}
		successAndRedirect(l('delete_success'));
	}
	function index($game_tag=null){
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('cms_category.site_id', 'where');
		$this->formfilter->addFilter('cms_category.model_id', 'where');
		$limit = $this->pagination($this->MCmsCategory->getCount());
		$list = $this->MCmsCategory->getList($limit);
		$this->assign('list', $list);
		$this->assign('models', $this->MCmsModel->getAll());
		$this->layout();
	}
	/**
	 * 检查名称是否有重复
	 * @param string $name 名称
	 */
	function _checkName($name){
		$site_id = $this->input->post('site_id');
		if($site_id.'' !== ((int)$site_id).''){
			//网站没找到，不作检查
			return true;
		}
		$parent_id = (int)$this->input->post('parent_id');
		$where = Array('site_id'=>$site_id, 'parent_id'=>$parent_id, 'name'=>$name);
		if(isset($this->editObj) && $this->editObj){
			$where['id <>'] = $this->editObj->id;
		}
		if($this->MCmsCategory->getAll($where)){
			$this->form_validation->set_message('_checkName', '同级目录中，已经存在相同名称的目录！');
			return false;
		}
		return true;
	}
	/**
	 * 检查代码是否有重复
	 * @param string $code 目录代码
	 */
	function _checkCode($code){
		$site_id = $this->input->post('site_id');
		if($site_id.'' !== ((int)$site_id).''){
			//网站没找到，不作检查
			return true;
		}
		$where = Array('code'=>$code, 'site_id'=>$site_id);
		if(isset($this->editObj) && $this->editObj){
			$where['id <>'] = $this->editObj->id;
		}
		if($this->MCmsCategory->getAll($where)){
			$this->form_validation->set_message('_checkCode', '所选网站下，已经存在相同代码的目录！');
			return false;
		}
		return true;
	}
	/**
	 * 检查循环
	 * @param integer $pid 父目录标识
	 */
	function _checkParent($pid){
		if($pid < 1){
			return true;
		}
		$site_id = $this->input->post('site_id');
		if(isset($this->editObj) && $this->editObj){
			$site_id = $this->editObj->site_id;
		}
		if($site_id.'' !== ((int)$site_id).''){
			//网站没找到，不作检查
			return true;
		}
		$parentObj = $this->MCmsCategory->getOne(Array('id'=>$pid));
		if(!$parentObj){
			$this->form_validation->set_message('_checkParent', '父目录不存在！');
			return false;
		}
		if($parentObj->site_id != $site_id){
			$this->form_validation->set_message('_checkParent', '父目录必须和当前目录属于同一个网站！');
			return false;
		}
		if(isset($this->editObj) && $this->editObj){
			$where = Array('site_id'=>$site_id);
			$tmp = $this->MCmsCategory->getAll($where);
			$arr = Array();
			foreach ($tmp as $v){
				$arr[$v->id] = $v;
			}
			$tmpObj = $arr[$pid];
			$tmp = Array($tmpObj->id=>$tmpObj);
			while(array_key_exists($tmpObj->parent_id, $arr) && !array_key_exists($tmpObj->parent_id, $tmp)){
				$tmpObj = $arr[$tmpObj->parent_id];
				$tmp[$tmpObj->id] = $tmpObj;
			}
			if(array_key_exists($this->editObj->id, $tmp)){
				$this->form_validation->set_message('_checkParent', '目录分级出现循环！');
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 读取指定网站下的目录树
	 * @param int $site_id
	 */
	function ajaxGetTree($site_id){
		$categories = $this->MCmsCategory->getListBySiteId($site_id);
		$t['0'] = array('text'=>'/','id'=>'0','pid'=>NULL);
		foreach ($categories as $c){
			$t[$c->id] = array('text'=>$c->name,'id'=>$c->id,'pid'=>$c->parent_id);
		}
		foreach ($t as $k => $node){
			if( !is_null($node['pid']) ) {
				$t[$node['pid']]['children'][] =& $t[$k];
			}
		}
		echo json_encode($t['0']);
	}
}
