<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 权限模块-角色管理
 * @author lifw
 */
class Role extends AdminController {
	
	public $_validation = null;

	function __construct(){
		parent::__construct();
		$this->load->model('MRole');
		$this->_validation =  array(
			array('field'=>'description', 'label'=>'备注', 'rules'=>'')
			,array('field'=>'name', 'label'=>'角色名', 'rules'=>'required|max_length[45]')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
		);
	}
	/** 添加角色 */
	function add(){
		//读取画面上提交的权限
		$powers = $this->readPagePow();
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation);
		$obj = $this->MRole->createVo();
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$newObj = populate($obj, $this->form_validation->post());
			if($this->MRole->add($newObj, $powers)){
				successAndRedirect(l('add_success'));
			}
			error(l('add_faild'));
		}
		$this->assign('role', $obj);
		$this->assign('pows', $powers);
		$this->assign('ctrls', $this->config->item('menu_page'));
		$this->layout();
	}
	/**
	 * 删除角色
	 * @param $id		角色标识
	 * @param $updated	最后更新时间
	 */
	function delete($id,$updated){
		if((((int)$id).'' !== $id) || (((int)$updated).'' !== $updated)){
			show_error(l('id_or_updated_not_null'));
		}
		if($id == 1){
			errorAndRedirect(l('admin_delete_error'));
		}
		$this->load->model('MAdmin');
		$query = $this->MAdmin->getUserListByRoleId($id);
		if($query->num_rows() > 0){
			//用户数量检查
			errorAndRedirect(l('role_user_exists'));
		}
		$this->load->model('MRole');
		if(!$this->MRole->delete($id, $updated)){
			//操作冲突
			errorAndRedirect(l('data_fail'));
		}
		//删除成功
		successAndRedirect(l('delete_success'));
	}
	/**
	 * 修改角色及其权限信息
	 * @param $id		角色标识
	 * @param $updated	最后更新时间
	 */
	function edit($id){
		if(((int)$id).'' !== $id){
			show_error(l('id_or_updated_not_null'));
		}
		//读取角色信息
		$this->load->model('MRole');
		$role = $this->MRole->getById($id);
		//读取权限信息
		$this->load->model('MPermission');
		$tmp = $this->MPermission->getPermissionByRoleId($id)->result();
		//将权限信息组织成数组格式(以page_id为作key的数组)，以便模板显示
		$powers = Array();
		foreach ($tmp as $tmpItem){
			$powers[$tmpItem->page_id.''] = Array('page_id' => $tmpItem->page_id, 'power'=>$tmpItem->power);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->model('MRole');
			//从画面上读取权限信息
			$this->load->helper('populate');
			$inputInfo = populate($role,$this->form_validation->post());
			$inputInfo->id = $id;
			$powers = $this->readPagePow();
			$rtn = $this->MRole->update(Array('name' => $inputInfo->name, 'description' => $inputInfo->description), $id, $inputInfo->update_time, $powers);
			if($rtn === true){
				//更新成功
				successAndRedirect(l('edit_success'));
			}
			//操作冲突
			error(l('data_fail'));
		}
		$this->assign('role', $role);
		$this->assign('pows', $powers);
		$this->assign('ctrls', $this->config->item('menu_page'));
		$this->layout();
	}
	/** 角色列表查询 */
	function index(){
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->load->model('MRole');
		$limit = $this->pagination($this->MRole->getCount());
		$roles = $this->MRole->getList($limit);
		$this->assign('roles',$roles);
		$this->layout();
	}
	/**
	 * 从画面上读取权限信息
	 * @return 以page_id为作第一层key的二维数组
	 * 				第二维的数组被db直接用来插入数据(即：[字段名]=>[值] 的数组)
	 */
	function readPagePow(){
		$allCtrls = $this->config->item('menu_page');
		$ss = $this->input->post('power');
		$arr = Array();
		foreach ($allCtrls as $item){
			$ctrlId = $item['id'];
			if($ss && array_key_exists($ctrlId, $ss)){
				$obj = new stdClass();
				$obj->read = array_key_exists('read', $ss[$ctrlId]);
				$obj->delete = $obj->add = array_key_exists('add', $ss[$ctrlId]);
				$obj->edit = array_key_exists('edit', $ss[$ctrlId]);
				$obj->powNum = UserPower::encodePower($obj);
				$arr[$ctrlId] = Array('page_id' => $ctrlId, 'power' => $obj->powNum);
			}
		}
		return $arr;
	}
	
 }
