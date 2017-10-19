<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 权限模块-用户管理
 */
class Admin extends AdminController {
	public $_validation = null;
	function __construct(){
		parent::__construct();
		$this->load->model('MAdmin');
		$this->_validation =  array(
			array('field'=>'password', 'label'=>l('password'), 'rules'=>'min_length[3]|max_length[20]')
			,array('field'=>'role_id', 'label'=>l('role'), 'rules'=>'required|integer')
			,array('field'=>'name', 'label'=>l('name'), 'rules'=>'required|max_length[20]')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
		);
	}
	
	
	function add(){
		if(!$this->p->add){
			//权限不足
			show_error(l('user_has_nopower'));
		}
		$this->load->library('form_validation');
		$this->_validation[] =array('field'=>'account', 'label'=>l('account'), 'rules'=>'required|max_length[20]|is_unique[core_admins.account]');
		$this->_validation[0]['rules'] .= "|required";
		$this->form_validation->set_rules($this->_validation);
		$this->load->helper('populate');
		$obj = $this->MAdmin->createVo();
		if($this->form_validation->run()==TRUE){
			$newAdmin = populate($obj, $this->form_validation->post());
			if($this->MAdmin->addAdmin($newAdmin)){
				successAndRedirect(l('add_success'));
			}
			error(l('data_fail'));
		}
		$this->load->model('MRole');
		$roles = $this->MRole->getAll();
		$this->assign('admin', $obj);
		$this->assign('roles', object_column($roles,'name','id'));
		$this->layout();
	}
	
	function delete(){
		if(!$this->p->delete){
			//权限不足
			show_error(l('user_has_nopower'));
		}
		$idTimeList = $this->input->post('id_time');
		if(!is_array($idTimeList) || !$idTimeList){
			errorAndRedirect('请选择要操作的用户！');
		}
		$user_op = $this->input->post('user_op');
		if("delete" == $user_op){
			//批量删除
			$ret = $this->MAdmin->deleteUser($idTimeList);
		}else if("forbid" == $user_op){
			//批量禁用
			$ret = $this->MAdmin->changeForbidden($idTimeList, MAdmin::IS_FORBIDDEN);
		}else if("unforbid" == $user_op){
			//批量启用
			$ret = $this->MAdmin->changeForbidden($idTimeList, MAdmin::NOT_FORBIDDEN);
		}else{
			errorAndRedirect('未知操作！');
		}
		if($ret){
			successAndRedirect('操作成功！');
		}else{
			successAndRedirect(l('data_fail'));
		}
	}
	
	function edit($id){
		if(!$this->p->edit){
			//权限不足
			show_error(l('user_has_nopower'));
		}
		if(((int)$id) . '' !== $id){
			show_error(l('id_or_updated_not_null'));
		}
		$admin = $this->MAdmin->getById($id);
		if(!$admin/* || $admin->type != GROUP_ADMIN*/){
			show_error('此管理员账号不存在！');
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$newAdmin = populate($admin, $this->form_validation->post());
			if($this->MAdmin->updateAdmin($newAdmin)){
				successAndRedirect(l('edit_success'));
			}
			//操作冲突
			error(l('data_fail'));
		}
		$this->load->model('MRole');
		$roles = $this->MRole->getAll();
		$this->assign('admin', $admin);
		$this->assign('roles', object_column($roles,'name','id'));
		$this->layout();
	}
	
	function index(){
		if(!$this->p->read){
			//权限不足
			show_error(l('user_has_nopower'));
		}
		$this->load->model('MRole');
		$roles = $this->MRole->getAll();
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('name', 'like');
		$this->formfilter->addFilter('role_id', 'where');
		$this->formfilter->addFilter('forbidden', 'where');

		$limit = $this->pagination($this->MAdmin->getCount());
		$admins = $this->MAdmin->getList($limit);
		$this->assign('admins',$admins);
		$this->assign('roles',object_column($roles,'name','id'));
		$this->layout();
	}
}
