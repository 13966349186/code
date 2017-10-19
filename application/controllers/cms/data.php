<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** CMS-文章 管理 */
class data extends AdminController {
	private $category = null;
	private $_checkCodeWhere;
	private $_validation =array(
		'title' => array('field' => 'title', 'label' => '标题', 'rules' => 'required|max_length[128]')
		,'code' => array('field' => 'code', 'label' => '标识', 'rules' => 'trim|max_length[64]|callback__checkCode')
		,'status' => array('field' => 'status', 'label' => '状态', 'rules' => 'required|integer')
		,'publish_time' => array('field' => 'publish_time', 'label' => '发布时间', 'rules' => 'required|strtotime|integer')
		,'update_time' => array('field' => 'update_time', 'label' => '更新时间', 'rules' => '')
	);
	function __construct(){
		parent::__construct();
		$this->load->helper('cms');	
		$this->load->library('Form_validation');
		$this->load->model('MCmsNode');
		$this->load->model('MCmsCategory');
		$this->load->model('MSite');
	}
	
	function _checkCode($str){
		if(empty($str)){
			return true;
		}
		$this->_checkCodeWhere['code'] = $str;
		$row = $this->MCmsNode->getOne($this->_checkCodeWhere);
		if($row){
			$this->form_validation->set_message('_checkCode', '标识不能重复！');
			return false;
		}
		return true;
	}
	
	private function _createMenu($site_id, $category_id=null){
		$site = $this->MSite->getOne(Array('id'=>$site_id));
		if(!$site){
			show_404();
		}
		if($category_id){
			$this->category = $this->MCmsCategory->getOne(Array('id'=>$category_id));
		}
		if($this->category && $this->category->site_id != $site_id){
			redirect(site_url($this->_thisModule.$this->_thisController.'/index/'.$site_id));
		}
		$this->assign('parents', Array());
		$menuLeft = Array();
		if($site && $lst = $this->MCmsCategory->getListBySiteId($site_id)){
			$idArr = Array();
			foreach ($lst as $v){
				//模拟系统菜单信息
				$v->sel = false;
				$v->pages = Array();
				$v->icon = '';
				$v->class = '';
				$v->parent = null;
				$v->ctrl = $this->_thisModule.$this->_thisController.'/index/'.$site_id.'/'.$v->id;
				$idArr[$v->id] = $v;
			}
			//结构化关联
			foreach ($idArr as $id=>$v){
				if(array_key_exists($v->parent_id, $idArr)){
					$idArr[$v->parent_id]->pages[$v->id] = $v;
					$v->parent = $idArr[$v->parent_id];
				}else{
					$menuLeft[$id] = $v;
				}
			}
			if($this->category && array_key_exists($this->category->id, $idArr)){
				//父级目录的操作
				$tmpItem = $this->category = $idArr[$this->category->id];
				$tmpArr = Array();
				while ($tmpItem && !array_key_exists($tmpItem->id, $tmpArr)){
					$tmpArr[$tmpItem->id] = $tmpItem;
					$tmpItem->sel = true;//标记菜单选中状态
					$tmpItem = $tmpItem->parent;
				}
				//生成面包霄数组
				unset($tmpArr[$this->category->id]);
				$this->assign('parents', array_reverse($tmpArr));
			}
			//填充图标（只区分目录和节点）
			foreach ($idArr as $id=>$v){
				$v->icon = $v->pages?'fa fa-folder-o':'fa fa-file-word-o';
			}
		}
		$this->assign('site', $site);
		$this->assign('category', $this->category);
		$this->assign('leftMenu', $menuLeft);
	}
	/**
	 * 列表页
	 * @param int $site_id 网站ID
	 * @param int $cat_id 目录ID
	 */
	function index($site_id, $cat_id = null){
		$this->_createMenu($site_id, $cat_id);
		if($this->category){
			$this->assign('breadName', $this->category->name.'列表');
			$this->load->model('MCmsModel');
			$model = $this->MCmsModel->getOne(Array('id'=>$this->category->model_id));
			if(!$model){
				errorAndRedirect('目录所属的类型ID['.$this->category->model_id.']不存在!', site_url('/'.$this->_thisModule.$this->_thisController.'/'.$this->_thisMethod.'/'.$site_id));
			}
			$model_list = $this->MCmsModel->getDtlById($model->id);
			$lst = $this->MCmsNode->getAll(Array('category_id'=>$this->category->id));
			$this->load->library('FormFilter');
			$this->load->helper('formfilter');
			$this->formfilter->addFilter('title','like');
			$this->formfilter->addFilter('status','where');
			$this->formfilter->addFilter('category_id', 'where', Array('category_id', $cat_id));
			$limit = $this->pagination($this->MCmsNode->getCount(), '/'.$this->_thisModule.$this->_thisController.'/'.$this->_thisMethod.'/'.$site_id.($cat_id?'/'.$cat_id:''));
			$nodeList = $this->MCmsNode->getListWithModel($limit, $model->code);
			$this->assign('nodeList', $nodeList);
			$this->assign('model', $model);
			$this->assign('model_detail', $model_list);
		}
		$this->layout();
	}
	/** 
	 * 删除内容
	 * @param int $site_id 网站ID
	 * @param int $id 内容ID
	 * @param int $update_time 更新时间戳 
	 */
	function delete($site_id, $id, $update_time){
		$vo = $this->MCmsNode->getOne(Array('id'=>$id));
		if(!$vo){
			errorAndRedirect(l('id_not_null'));
		}
		$this->category = $this->MCmsCategory->getOne(array('id'=>$vo->category_id));
		if(!$this->category){
			$url = site_url($this->_thisModule.$this->_thisController.'/index/'.$site_id);
		}
		$this->load->model('MCmsModel');
		$model = $this->MCmsModel->getOne(Array('id'=>$this->category->model_id));
		if(!$model){
			errorAndRedirect('目录所属的类型ID['.$this->category->model_id.']不存在!');
		}
		$url = site_url($this->_thisModule.$this->_thisController.'/index/'.$site_id.'/'.$vo->category_id);
		if(!$this->MCmsNode->deleteNode($id, $update_time, $model->code)){
			errorAndRedirect(l('data_fail'), $url);
		}
		successAndRedirect(l('delete_success'), $url);
	}

	/** 
	 * 添加
	 * @param int $site_id 网站ID
	 * @param int $cat_id 目录ID
	 **/
	function add($site_id, $cat_id){
		$this->_createMenu($site_id, $cat_id);
		if(!$this->category){
			redirect(site_url($this->_thisModule.$this->_thisController.'/index/'.$site_id));
		}
		if($this->category->record_limit == 0){
			errorAndRedirect('该目录下不允许添加文章！', site_url($this->_thisModule.$this->_thisController.'/index/'.$site_id.'/'.$cat_id));
		}
		$this->_setTitle(l($this->_thisMethod).$this->category->name);
		$this->load->model('MCmsModel');
		if($this->category->record_limit >= 0){
			if($this->category->record_limit < 1){
				show_error('该目录下限制不允许添加内容！');
			}else if(($ret = $this->MCmsNode->getAll(Array('category_id'=>$this->category->id))) && count($ret) >= $this->category->record_limit){
				show_error('该目录下的文章数量已经达到上限！');
			}
		}
		$model = $this->MCmsModel->getOne(Array('id'=>$this->category->model_id));
		if(!$model){
			errorAndRedirect('目录所属的类型ID['.$this->category->model_id.']不存在!');
		}
		$model_list = $this->MCmsModel->getDtlById($model->id);
		$vo = $this->MCmsNode->createVo();
		$vo->publish_time = time();
		foreach ($model_list as $v){
			$v->data_format = cms_parse_config($v->data_type,$v->data_format);
			if($v->data_type == 9 || $v->data_type == 10){
				array_unique_push($v->data_format['ci'], 'upload');
			}
			$this->_validation[] = array('field' => $v->col_name, 'label' => $v->disp_name, 'rules' => implode('|', $v->data_format['ci']));
			$vo->{$v->col_name} = '';
			if(array_key_exists('default', $v->data_format)){
				$vo->{$v->col_name} = $v->data_format['default'];
			}
		}
		//验证表单数据
		$this->_checkCodeWhere = array('category_id'=>$cat_id);
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$vo = populate($vo, $this->form_validation->post());
			$vo->category_id = $cat_id;
			$vo->author = $this->_user->account;
			$vo->code = empty($vo->code)?null:$vo->code;
			//复选框抓数据
			foreach ($model_list as $v){
				if($v->data_type == 7){
					$vo->{$v->col_name} = array_key_exists($v->col_name, $this->input->post())?'1':'0';
				}
			}
			$result = $this->MCmsNode->addNode($vo, $model, $model_list);
			if(!$result){
				error(l('data_fail'));
			}else{
				successAndRedirect(l('add_success'), site_url($this->_thisModule.$this->_thisController.'/index/'.$site_id.'/'.$cat_id));
			}
		}
		$this->assign('model', $model);
		$this->assign('list', $model_list);
		$this->assign('obj', $vo);
		$this->layout();
	}
	
	/**
	 * 编辑内容
	 * @param int $site_id 网站ID
	 * @param int $cat_id
	 * @param int $node_id
	 */
	function edit($site_id, $cat_id, $node_id){
		$this->_createMenu($site_id, $cat_id);
		if(!$this->category){
			redirect(site_url($this->_thisModule.$this->_thisController.'/index/'.$site_id));
		}
		if($this->category->record_limit == 0){
			errorAndRedirect('该目录下不允许添加文章！', site_url($this->_thisModule.$this->_thisController.'/index/'.$site_id.'/'.$cat_id));
		}
		$this->load->model('MCmsModel');
		$model = $this->MCmsModel->getOne(Array('id'=>$this->category->model_id));
		if(!$model){
			errorAndRedirect('目录所属的类型ID['.$this->category->model_id.']不存在!', site_url($this->_thisModule.$this->_thisController.'/index/'.$site_id.'/'.$cat_id));
		}
		$model_list = $this->MCmsModel->getDtlById($model->id);
		$vo = $this->MCmsNode->getDtlByIdAndCode($node_id, $model->code);
		foreach ($model_list as $v){
			$v->data_format = cms_parse_config($v->data_type,$v->data_format);
			if($v->data_type == 9 || $v->data_type == 10){
				array_unique_push($v->data_format['ci'], 'upload');
			}
			$this->_validation[] = array('field' => $v->col_name, 'label' => $v->disp_name, 'rules' => implode('|', $v->data_format['ci']));
		}
		//验证表单数据
		$this->_checkCodeWhere = array('category_id'=>$cat_id, 'id != '=>$node_id);
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$vo = populate($vo, $this->form_validation->post());
			$vo->author = $this->_user->account;
			$vo->code = empty($vo->code)?null:$vo->code;
			//复选框抓数据
			foreach ($model_list as $v){
				if($v->data_type == 7){
					$vo->{$v->col_name} = array_key_exists($v->col_name, $this->input->post())?'1':'0';
				}
			}
			$result = $this->MCmsNode->editNode($vo, $model, $model_list);
			if(!$result){
				error(l('data_fail'));
			}else{
				successAndRedirect(l('edit_success'), site_url($this->_thisModule.$this->_thisController.'/index/'.$site_id.'/'.$cat_id));
			}
		}
		$this->assign('model', $model);
		$this->assign('list', $model_list);
		$this->assign('obj', $vo);
		$this->layout();
	}
}
