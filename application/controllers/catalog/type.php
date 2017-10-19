<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 商品模块-类型管理
 */
class Type extends AdminController {
	/** 添加和编辑共用检查规则 */
	public $_validation = null;
	public $_uniqueCondition = array();
	public $format_type_list = Array('0'=>'文本','11'=>'多行文字','2'=>'时间','3'=>'日期','4'=>'整数','5'=>'浮点数','6'=>'下拉框','7'=>'复选框','8'=>'单选框');
	function __construct(){
		parent::__construct();
		$this->_required['product_attr'] = EDITPOWER;
		$this->_required['order_attr'] = EDITPOWER;
		$this->load->model('MType');
		$this->_validation =  array(
			array('field'=>'state', 'label'=>'状态', 'rules'=>'required|integer')
			,array('field'=>'note', 'label'=>'备注', 'rules'=>'trim|max_length[128]')
			,array('field'=>'name', 'label'=>'名称', 'rules'=>'trim|required|max_length[128]|callback__checkName')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
		);
		$this->_add_validation = Array(
			array('field'=>'code', 'label'=>'类型标识', 'rules'=>'trim|required|max_length[64]|callback__checkCode')
			,array('field'=>'game_id', 'label'=>'所属游戏', 'rules'=>'required|integer')
			,array('field'=>'model', 'label'=>'数据类型', 'rules'=>'trim|required|max_length[64]')
		);
		$this->load->library('Games');
	}
	
	/** 类型列表 */
	function index(){
		//读取model配置信息
		$models = $this->config->item('model');
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('game_id', 'where');
		$this->formfilter->addFilter('model', 'where');
		$this->formfilter->addFilter('state', 'where');
		$limit = $this->pagination($this->MType->getCount());
		$lst = $this->MType->getList($limit);
		$this->assign('lst', $lst);
		$this->assign('models', $models);
		$this->layout();
	}
	
	/** 添加类型 */
	function add(){
		$vo = $this->MType->createVo();
		$this->_uniqueCondition['game_id'] = (int)$this->input->post('game_id');
		$this->load->library('form_validation');
		$this->form_validation->set_rules(array_merge($this->_add_validation, $this->_validation));
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$vo = populate($vo, $this->form_validation->post());
			if($this->MType->add($vo)){
				successAndRedirect('基本信息添加成功', site_url($this->_thisModule.$this->_thisController.'/product_attr/'.$vo->id));
			}
			error(l('data_fail'));
		}
		//读取model配置信息
		$models = $this->config->item('model');
		$this->assign('states', $this->MType->states);
		$this->assign('models', $models);
		$this->assign('vo', $vo);
		$this->layout();
	}
	
	/** 编辑类型 */
	function edit($id){
		if(!is_numeric($id) || !($vo = $this->MType->getById($id))) {
			show_error('指定的类型不存在！');
		}
		$this->_uniqueCondition['game_id'] = $vo->game_id;
		$this->_uniqueCondition['id !='] = $vo->id;
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$vo = populate($vo, $this->form_validation->post());
			if($this->MType->update($vo)){
				successAndRedirect('基本信息保存成功', site_url($this->_thisModule.$this->_thisController.'/product_attr/'.$vo->id));
			}
			error(l('data_fail'));
		}
		//读取model配置信息
		$models = $this->config->item('model');
		$this->assign('states', $this->MType->states);
		$this->assign('models', $models);
		$this->assign('vo', $vo);
		$this->layout();
	}

	/**
	 * 编辑商品属性
	 * @param integer $id 类型标识
	 */
	function product_attr($id){
		$this->load->helper('cms');
		$this->load->model('MTypeAttribute');
		$this->load->model('MTypeAttributeDef');
		$this->load->library('form_validation');
		
		$type = $this->MType->getById($id);
		if(!$type){
			show_error('参数错误！');
		}
		//读取model配置信息
		$models = $this->config->item('model');
		if(!array_key_exists($type->model, $models)){
			show_error('数据模型['.$type->model.']不存在！');
		}
		//读取配置定义
		$defs = $this->MTypeAttributeDef->getListByModel($type->model);
		//读取数据
		$data_list = $this->MTypeAttribute->getAll(Array('type_id'=>$type->id));
		if($this->input->post() && !$defs){
			successAndRedirect(l('add_success'), site_url($this->_thisModule.$this->_thisController.'/order_attr/'.$id));
		}
		//将数据列表，根据配置定义，转化为单个对象
		$obj = new stdClass();
		foreach ($defs as $v){
			//配置定义中取ci规则
			$v->data_format = cms_parse_config($v->data_config, $v->data_format);
			$this->form_validation->set_rules($v->code, $v->name, implode('|', $v->data_format['ci']));
			//数据列表中取值，以code为名保存到单个对象中
			$obj->{$v->code} = '';
			foreach ($data_list as $d){
				if($d->code == $v->code){
					$obj->{$v->code} = $d->value;
					break;
				}
			}
		}
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$obj = populate($obj, $this->form_validation->post());
			if($this->MTypeAttribute->update($type, $obj)){
				successAndRedirect('商品属性保存成功', site_url($this->_thisModule.$this->_thisController.'/order_attr/'.$id));
			}
			error(l('data_fail'));
		}
		$this->assign('models', $models);
		$this->assign('defs', $defs);
		$this->assign('obj', $obj);
		$this->assign('type', $type);
		$this->layout('product_attr');
	}
	/**
	 * 编辑订单属性
	 * @param integer $id 类型标识
	 */
	function order_attr($id){
		$this->load->helper('cms');
		$this->load->model('MOrderAttributeDef');
		$this->load->library('form_validation');
		
		$type = $this->MType->getById($id);
		if(!$type){
			show_error('参数错误！');
		}
		//读取model配置信息
		$models = $this->config->item('model');
		if(!array_key_exists($type->model, $models)){
			show_error('数据模型['.$type->model.']不存在！');
		}
		$lst = $this->_chk();
		if($this->input->post() && !isset($this->chkErr)){
			if($this->MOrderAttributeDef->update($type, $lst)){
				successAndRedirect('配置完成', site_url($this->_thisModule.$this->_thisController));
			}
			error(l('data_fail'));
		}
		if(!$this->input->post()){
			$lst = $this->MOrderAttributeDef->getListByTypeId($type->id);
		}
		$this->assign('format_type_list', $this->format_type_list);
		$this->assign('models', $models);
		$this->assign('lst', $lst);
		$this->assign('type', $type);
		$this->layout('order_attr');
	}
	
	private function _chk(){
		$cols = Array('id'=>'', 'name'=>'', 'code'=>'', 'data_config'=>'', 'data_format'=>'', 'sort'=>'', 'index_flg'=>'', 'update_time'=>'');
		$rtn = Array();
		$postData = $this->input->post();
		$end = (int)$this->input->get_post('end');
		for($i=0;$i<$end;$i++){
			if(!array_key_exists('id'.$i, $postData)){
				continue;
			}
			$item = new stdClass();
			foreach ($cols as $k=>$v){
				$item->{$k} = trim($this->input->get_post($k.$i));
			}
			$rtn[] = $item;
			$pos = count($rtn)-1;
			if($item->sort.'' !== ((int)$item->sort).''){
				$this->assign('sort'.$pos.'_err', '填整数');
				$this->chkErr = true;
			}
			if($item->index_flg.'' !== '1' && $item->index_flg.'' !== '0'){
				$this->assign('index_flg'.$pos.'_err', '填0或1');
				$this->chkErr = true;
			}
			if(strlen($item->code) < 1){
				$this->assign('code'.$pos.'_err', '不可以为空');
				$this->chkErr = true;
			}else if(strlen($item->code) > 64){
				$this->assign('code'.$pos.'_err', '长度不可超过64');
				$this->chkErr = true;
			}else if((ord($item->code) <= 57 && ord($item->code) >= 48) || $item->code[0] == '_'){
				$this->assign('code'.$pos.'_err', '只能以字母开头');
				$this->chkErr = true;
			}else if(!preg_match("/^[A-Za-z0-9_]+$/",$item->code)){
				$this->assign('code'.$pos.'_err', '只能是字母数字下划线');
				$this->chkErr = true;
			}
			if(strlen($item->data_format) > 1024){
				$this->assign('data_format'.$pos.'_err', '长度不可超过1024');
				$this->chkErr = true;
			}
			if(!array_key_exists($item->data_config, $this->format_type_list)){
				$this->assign('data_config'.$pos.'_err', '不可以为空');
				$this->chkErr = true;
			}
			if(strlen($item->name) < 1){
				$this->assign('name'.$pos.'_err', '不可以为空');
				$this->chkErr = true;
			}else if(strlen($item->name) > 64){
				$this->assign('name'.$pos.'_err', '长度不可超过64');
				$this->chkErr = true;
			}
		}
		$countNum = count($rtn);
		for($i=0;$i<$countNum;$i++){
			$curr = $rtn[$i];
			for($j=$i+1;$j<$countNum;$j++){
				$tmp = $rtn[$j];
				
				if(strlen(trim($curr->code)) > 0 && trim($curr->code) == trim($tmp->code)){
					$this->assign('code'.$i.'_err', '代码不能重复');
					$this->assign('code'.$j.'_err', '代码不能重复');
					$this->chkErr = true;
				}
				if(strlen(trim($curr->name)) > 0 && trim($curr->name) == trim($tmp->name)){
					$this->assign('name'.$i.'_err', '名称不能重复');
					$this->assign('name'.$j.'_err', '名称不能重复');
					$this->chkErr = true;
				}
			}
		}
		return $rtn;
	}
	
	/**
	 * 检查名称是否有重复
	 * @param string $name 名称
	 */
	function _checkName($name){
		if($this->MType->getOne(Array('name'=>$name) + $this->_uniqueCondition)){
			$this->form_validation->set_message('_checkName', '当前游戏下，已经存在相同名称！');
			return false;
		}
		return true;
	}
	
	/**
	 * 检查code是否有重复值
	 * @param string $code
	 */
	function _checkCode($code){
		if($this->MType->getOne(Array('code'=>$code) + $this->_uniqueCondition)){
			$this->form_validation->set_message('_checkCode', '当前游戏下，已经存在相同标识！');
			return false;
		}
		return true;
	}
}
