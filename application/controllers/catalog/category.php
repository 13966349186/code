<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 * 商品模块-目录管理
 */
class Category extends AdminController {
	protected $disp_menu_item_ctrl = 'catalog/game';
	public $_validation = null;
	public $game_id = 0;
	/**
	 * @var MCategory $MCategory
	 */
	public $MCategory;
	
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'MCategory' );
		$this->load->library ( 'Games' );
		$this->_validation = array (
				array ('field' => 'name',	'label' => '名称',	'rules' => 'required|max_length[64]' ),
				array ('field' => 'description',	'label' => '描述',	'rules' => 'max_length[255]' ),
				array ('field' => 'state','label' => '状态','rules' => 'integer|required' ),
				array ('field' => 'update_time','label' => '更新时间','rules' => '' ) 
		);
	}
	
	/**
	 * 新建目录
	 * @param string $game_id
	 * @param string $parent_id
	 */
	function add($game_id='',$parent_id='') {
		$obj = $this->MCategory->createVo ();
		$this->game_id = $this->input->post ( 'game_id' );
		$validation =  array (
				array ('field' => 'game_id','label' => '游戏',	'rules' => 'required|integer|callback__checkGameId' ),
				array ('field' => 'parent_id',	'label' => '父目录','rules' => 'required|integer|callback__checkParent' 	),
				array ('field' => 'code',	'label' => '标识',	'rules' => 'trim|max_length[64]|callback__checkCode' )
		);
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( array_merge ($validation, $this->_validation ) );
		if ($this->form_validation->run ()) {
			$this->load->helper ( 'populate' );
			$newObj = populate ( $obj, $this->form_validation->post () );
			$newObj->code = $newObj->code?:null;
			if ($this->MCategory->add ( $newObj )) {
				successAndRedirect ( l ( 'add_success' ) );
			}
			error ( l ( 'data_fail' ) );
		}else{
			$obj->game_id = $game_id;
			$obj->parent_id = $parent_id;
		}
		$this->assign ( 'obj', $obj );
		$this->assign ( 'parents', $this->MCategory->getAll () );
		$this->layout ();
	}
	
	/**
	 * 编辑目录
	 * @param int $id
	 */
	function edit($id) {
		if (! $id || ! ($obj = $this->MCategory->getById ( $id ))) {
			show_error ( l ( 'id_or_updated_not_null' ) );
		}
		$this->game_id = $obj->game_id;
		$validation =  array (
				array ('field' => 'parent_id',	'label' => '父目录','rules' => 'required|integer|callback__checkParent|callback__checkTree[' . $obj->id . ']' 	)
		);
		$this->load->library ( 'form_validation' );
		$this->form_validation->set_rules ( array_merge ($validation, $this->_validation ) );
		if ($this->form_validation->run () == TRUE) {
			$post = $this->input->post ();
			$this->load->helper ( 'populate' );
			$newObj = populate ( $obj, $this->form_validation->post () );
			$newObj->id = $id;
			if ($this->MCategory->update ( $newObj )) {
				successAndRedirect ( l ( 'edit_success' ) , site_url($this->_thisModule.$this->_thisController . '/' . $this->_thisMethod . '/' . $id));
			}
			// 操作冲突
			error ( l ( 'data_fail' ) );
		}
		$this->assign ( 'obj', $obj );
		$this->assign ( 'parents', $this->MCategory->getAll ( Array ('id <>' => $obj->id ) ) );
		$this->layout ();
	}
	
	
	/**
	 * 列表页
	 */
	function index() {
		// 根据权限显示可用的操作
		$actions= array();
		if ($this->p->delete) {
			$actions ['forbid'] = '禁用';
			$actions ['unforbid'] = '启用';
		}
		if ($this->p->delete) {
			$actions ['delete'] = '删除';
		}
		$this->load->library ( 'FormFilter' );
		$this->load->helper ( 'formfilter' );
		if($game_id = $this->input->get('game_id')){
			$this->formfilter->setFilterValue('game_id', $game_id);
			$this->formfilter->setFilterValue('parent_id', $this->input->get('category_id')?:'0');
			$this->formfilter->setFilterValue('page', $this->input->get('page'));
		}

		$this->formfilter->addFilter ( 'parent_id', 'where' );
		$this->formfilter->addFilter ( 'game_id', 'where' );
		$limit = $this->pagination ( $this->MCategory->getCount () );
		$lst = $this->MCategory->getList ( $limit );
		$this->load->library('Categories',array(filterValue('game_id')));
		$path = filterValue('parent_id')?$this->categories->getPath( filterValue('parent_id')):array();
		$this->assign ( 'lst', $lst );
		$this->assign ( 'game_id',  filterValue('game_id') );
		$this->assign ( 'category_id', filterValue('parent_id') );
		$this->assign ( 'actions', $actions );
		$this->assign ( 'path', $path );
		$this->layout ();
	}
	
	/**
	 * 批量处理
	 */
	function batch() {
		$user_op = $this->input->post ( 'user_op' );
		$idTimeList = $this->input->post ( 'id_time' );
		if (! is_array ( $idTimeList ) || ! $idTimeList) {
			errorAndRedirect ( '请选择要操作的目录！' );
		}
		$success = true;
		switch ($user_op) {
			case 'forbid' :
			case 'unforbid' :
				if (! $this->p->edit) {
					show_error ( l ( 'user_has_nopower' ) ); // 权限不足
				}
				$error_msg = '数据冲突，部分目录更新状态失败';
				$state = ("unforbid" == $user_op) ? MCategory::STATE_ENABLE : MCategory::STATE_DISABLE;
				foreach ( $idTimeList as $id => $update_time ) {
					$success = $this->MCategory->setState ( $id, $state, $update_time ) ? $success : FALSE;
				}
				break;
			case 'delete' :
				if (! $this->p->delete) {
					show_error ( l ( 'user_has_nopower' ) ); // 权限不足
				}
				$error_msg = '部分目录非空，不能删除';
				foreach ( $idTimeList as $id => $update_time ) {
					$success = $this->MCategory->delete ( $id, NULL ) ? $success : FALSE;
				}
				break;
			default :
				errorAndRedirect ( '未知操作！' );
		}
		if ($success) {
			successAndRedirect ( '操作成功！' );
		} else {
			errorAndRedirect ( $error_msg );
		}
	}
	
	/**
	 * 父目录检查
	 * @param integer $pid  	父目录id
	 */
	function _checkParent($pid, $id = NULL) {
		if ($pid == 0) {
			return true;
		}
		$p = $this->MCategory->getOne ( Array ('id' => $pid));
		if (! $p) {
			$this->form_validation->set_message ( '_checkParent', '父目录不存在！' );
			return false;
		}
		if ($p->game_id != $this->game_id) {
			$this->form_validation->set_message ( '_checkParent', '父目录必须和当前目录属于同一个游戏！' );
			return false;
		}
		return true;
	}
	
	/**
	 * 检查目录树中是否有死循环
	 * @param int $pid 父目录ID
	 * @param int $id   当前目录ID
	 */
	function _checkTree($pid, $id){
		if ($pid == 0) {
			return true;
		}
		$rows = $this->MCategory->getAll ( array('game_id' => $this->game_id) );
		foreach ($rows as $row){
			$arr[$row->id] = $row->parent_id;
		}
		do{
			if($pid == $id){
				$this->form_validation->set_message ( '_checkTree', '目录树出现循环！' );
				return false;
			}
		}while(isset($arr[$pid]) && ($pid = $arr[$pid]) );
		return true;
	}
	
	
	/**
	 * 检查code是否有重复
	 * @param string $code 标识
	 */
	function _checkCode($code){
		if($code == ''){
			return true;
		}
		if($this->MCategory->getOne(Array('code'=>$code, 'game_id'=>$this->game_id))){
			$this->form_validation->set_message('_checkCode', '目录 code 重复！');
			return false;
		}
		return true;
	}
	
	/**
	 * 检查game id 存在
	 * @param int $id
	 */
	function _checkGameId($id){
		if(!$this->games[$id]){
			$this->form_validation->set_message('_ckeckGameId', '指定的游戏不存在！');
			return false;
		}
		return true;
	}
	
}
