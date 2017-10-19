<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站模块-邮件模板
 */
class MailTemplate extends AdminController {
	function __construct(){
		parent::__construct();
		$this->load->model('MMailTemplate');
		$this->_required['edit'] = VIEWPOWER;
	}
	function index(){
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('state', 'where');
		$limit = $this->pagination($this->MMailTemplate->getCount());
		$lst = $this->MMailTemplate->getList($limit);		
		$this->assign('lst',$lst);
		$this->layout();
	}
	/**
	 * 编辑邮件模板
	 * @param  int $id 邮件模板ID
	 */
	function edit($id){
		if(!is_numeric($id)){
			show_error(l('id_not_null'));
		}
		if(!($obj = $this->MMailTemplate->getOne(Array('id'=>$id)))){
			show_error('指定模板不存在！');
		}
		$validation =  array(
			array('field' => 'name', 'label' => '名称', 'rules' => 'required|max_length[128]')
			,array('field' => 'subject', 'label' => '标题', 'rules' => 'required|max_length[255]')
			,array('field' => 'message', 'label' => '内容', 'rules' => 'required')
			,array('field' => 'state', 'label' => l('site_state'), 'rules' => 'required|integer')
			,array('field' => 'update_time', 'label' => l('update_time'), 'rules' => '')
		);
		$this->load->library('form_validation');
		$this->form_validation->set_rules($validation);
		if($this->p->edit && $this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$newObj = populate($obj, $this->form_validation->post());
			if($this->MMailTemplate->update($newObj)){
				successAndRedirect(l('edit_success'));
			}else{
				error(l('data_fail'));
			}
		}
		$this->assign('obj', $obj);
		$this->layout();
	}
}
