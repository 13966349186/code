<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 商品模块-游戏管理
 */
class Game extends AdminController {
	public $_validation = null;
	/** 
	 * @var MGame $MGame;
	 */
	public $MGame;
	private $_edit = null;

	function __construct(){
		parent::__construct();
		$this->load->model('MGame');
		$this->_validation =  array(
			array('field'=>'name', 'label'=>l('game_name'), 'rules'=>'trim|required|max_length[64]|callback__isUniqueName')
			,array('field'=>'sort', 'label'=>l('game_sort'), 'rules'=>'required|integer')
			,array('field'=>'description', 'label'=>l('game_description'), 'rules'=>'max_length[512]')
			,array('field'=>'state', 'label'=>l('game_state'), 'rules'=>'required|integer')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
		);
	}

	function add(){
		$this->load->library('form_validation');
		$this->load->helper('populate');
		$this->_validation[] = array('field'=>'code', 'label'=>'标识', 'rules'=>'trim|required|max_length[32]|is_unique[core_game.code]');
		$this->form_validation->set_rules($this->_validation);
		$game=$this->MGame->createVo();
		if($this->form_validation->run()==TRUE){
	//	var_dump($this->form_validation->post());exit;
		//	$game = populate($game, $this->form_validation->post());
			$game = $this->form_validation->post();
			$this->MGame->add($game);
			successAndRedirect(l('add_success'));
		}
		$this->assign('game', $game);
		$this->layout();
	}
	
	
	function edit($id){
		if(((int)$id) . '' !== $id){
			show_error(l('id_or_updated_not_null'));
		}
		if(!($game = $this->MGame->getById($id))){
			show_error('指定游戏不存在！');
		}
		$this->load->helper('populate');
		$this->load->library('form_validation');
		$this->_edit = $id;
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$game = populate($game, $this->form_validation->post());
			if($this->MGame->update($game) !== true){
				//操作冲突
				errorAndRedirect(l('data_fail'));
			}
			successAndRedirect(l('edit_success'));
		}
		$this->assign('game', $game);
		$this->layout();
	}
	
	function index(){
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('name', 'like');
		$this->formfilter->addFilter('state', 'where');
		$limit = $this->pagination($this->MGame->getCount());
		$games = $this->MGame->getList($limit, filterValue('sort') === '1');
		$this->assign('games',$games);
		$this->layout();
	}
	
	function _isUniqueName($name){
		$where = array('name' => $name);
		if(is_numeric($this->_edit)){
			$where['id != '] = $this->_edit;
		}
		$vo = $this->MGame->getOne($where);
		if($vo){
			$this->form_validation->set_message('_isUniqueName', '%s 已存在！');
			return false;
		}
		return true;
	}
}
