<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** CMS-文章类型管理 */
class Model extends AdminController {
	public $_validation = null;
	private $dtlArr = Array();
	private $err_flg = false;
	private $format_type_list = Array('0'=>'文本','1'=>'富文本','11'=>'多行文字','2'=>'时间','3'=>'日期','4'=>'整数','5'=>'浮点数','6'=>'下拉框','7'=>'复选框','8'=>'单选框','9'=>'文件(供下载)','10'=>'文件(图片)');
	private $err_code = Array('id', 'category_id', 'title', 'author', 'code', 'status', 'publish_time', 'create_time', 'update_time');
	function __construct(){
		parent::__construct();
		$this->load->model('MCmsModel');
		$this->_validation = Array(
			array('field'=>'code', 'label'=>'表名', 'rules'=>'required|max_length[32]|callback__checkCode')
			,array('field'=>'name', 'label'=>'名称', 'rules'=>'required|max_length[64]|callback__checkName')
			,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
		);
	}
	function add(){
		$obj = $this->MCmsModel->createVo();
		$this->load->library('form_validation');
		$lst = $this->_chk();
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE && !isset($this->chkErr)){
			$this->load->helper('populate');
			$newObj = (Array)$obj;
			$newObj = populate((Object)$newObj, $this->form_validation->post());
			if($this->MCmsModel->dataExists($newObj->code)){
				errorAndRedirect('该表已经存在，且有数据在其中，无法修改表结构！');
				return;
			}
			if($this->MCmsModel->add($newObj, $lst)){
				successAndRedirect(l('edit_success'));
			}else{
				errorAndRedirect(l('data_fail'));
			}
		}
		$this->assign('data_exists', false);
		$this->assign('obj', $obj);
		$this->assign('lst', $lst);
		$this->assign('format_type_list', $this->format_type_list);
		$this->layout();
	}
	function _checkCode($code){
		if((ord($code) <= 57 && ord($code) >= 48) || $code[0] == '_'){
			$this->form_validation->set_message('_checkCode','%s只能以字母开头');
			return false;
		}else if(!preg_match("/^[A-Za-z0-9_]+$/",$code)){
			$this->form_validation->set_message('_checkCode','%s只能由字母数字下划线组成');
			return false;
		}
		if(!property_exists($this, 'editObj') || $this->editObj->code <> $code){
			if($this->MCmsModel->dtlTableExists($code)){
				$this->form_validation->set_message('_checkCode','相同的%s已经存在');
				return false;
			}
		}
		return true;
	}
	function _checkName($name){
		$where = Array('name'=>$name);
		if(property_exists($this, 'editObj')){
			$where['id <>'] = $this->editObj->id;
		}
		if($this->MCmsModel->getOne($where)){
			$this->form_validation->set_message('_checkName','相同的%s已经存在');
			return false;
		}
		return true;
	}
	/**
	 * 编辑文章类型
	 * @param integer $id 文章类型ID
	 */
	function edit($id){
		if(!is_numeric($id)){
			show_error(l('id_not_null'));
		}
		$obj = $this->MCmsModel->getOne(Array('id'=>$id));
		if(!$obj){
			errorAndRedirect('没找到类型定义！');
		}
		$this->editObj = $obj;
		$data_exists = $this->MCmsModel->dataExists($obj->code, $id);
		$this->load->library('form_validation');
		$lst = $this->_chk();
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE && !isset($this->chkErr)){
			$this->load->helper('populate');
			$newObj = (Array)$obj;
			$newObj = populate((Object)$newObj, $this->form_validation->post());
			if($this->MCmsModel->update($newObj, $lst)){
				successAndRedirect(l('edit_success'));
			}else{
				errorAndRedirect(l('data_fail'));
			}
		}
		if(!$this->input->post()){
			$lst = $this->MCmsModel->getDtlById($id);
		}
		$this->assign('data_exists', $data_exists);
		$this->assign('obj', $obj);
		$this->assign('lst', $lst);
		$this->assign('format_type_list', $this->format_type_list);
		$this->layout();
	}
	private function _chk(){
		if(!$this->input->post()){
			return Array();
		}
		$cols = Array('id'=>'', 'col_name'=>'', 'disp_name'=>'', 'data_type'=>'', 'data_format'=>'', 'disp_on_list'=>'');
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
			$item->disp_on_list = $item->disp_on_list?'1':'0';
			$rtn[] = $item;
			$currIdx = count($rtn)-1;
			if(in_array($item->col_name, $this->err_code)){
				$this->assign('col_name'.$currIdx.'_err', '不可以用关键字');
				$this->chkErr = true;
			}else if(strlen($item->col_name) < 1){
				$this->assign('col_name'.$currIdx.'_err', '不可以为空');
				$this->chkErr = true;
			}else if(strlen($item->col_name) > 64){
				$this->assign('col_name'.$currIdx.'_err', '长度不可超过64');
				$this->chkErr = true;
			}else if((ord($item->col_name) <= 57 && ord($item->col_name) >= 48) || $item->col_name[0] == '_'){
				$this->assign('col_name'.$currIdx.'_err', '只能以字母开头');
				$this->chkErr = true;
			}else if(!preg_match("/^[A-Za-z0-9_]+$/",$item->col_name)){
				$this->assign('col_name'.$currIdx.'_err', '只能由字母数字下划线组成');
				$this->chkErr = true;
			}
			if(!array_key_exists($item->data_type, $this->format_type_list)){
				$this->assign('data_type'.$currIdx.'_err', '不可以为空');
				$this->chkErr = true;
			}
			if(strlen($item->disp_name) < 1){
				$this->assign('disp_name'.$currIdx.'_err', '不可以为空');
				$this->chkErr = true;
			}else if(strlen($item->disp_name) > 64){
				$this->assign('disp_name'.$currIdx.'_err', '长度不可超过64');
				$this->chkErr = true;
			}
			if(strlen($item->data_format) > 1024){
				$this->assign('data_format'.$currIdx.'_err', '长度不可超过1024');
				$this->chkErr = true;
			}
			$tmpArr = explode("\n", str_replace("\r", "", $item->data_format));
			$item->config_arr = Array();
			$configs = &$item->config_arr;
			foreach($tmpArr as $line){
				$c = explode(':',$line,2);
				if($c && count($c)==2 ){
					if($c[0] == 'options' || $c[0] =='upload'){
						$option = explode('=', $c[1],2);
						$configs[$c[0]][trim($option[0])] = trim($option[1]);
					}elseif($c[0] == 'default'){
						$configs[$c[0]] = $c[0];
					}else{
						$configs[$c[0]][] = trim($c[1]);
					}
				}
			}
		}
		$countNum = count($rtn);
		for($i=0;$i<$countNum;$i++){
			$curr = $rtn[$i];
			for($j=$i+1;$j<$countNum;$j++){
				$tmp = $rtn[$j];
				if(strlen(trim($curr->col_name)) > 0 && trim($curr->col_name) == trim($tmp->col_name)){
					$this->assign('col_name'.$i.'_err', '代码不能重复');
					$this->assign('col_name'.$j.'_err', '代码不能重复');
					$this->chkErr = true;
				}
			}
		}
		if($countNum < 1){
			error('请添加字段信息！');
			$this->chkErr = true;
		}
		return $rtn;
	}
	function delete($id, $update_time){
		if(!is_numeric($id) || !is_numeric($update_time)){
			show_error(l('id_not_null'));
		}
		$obj = $this->MCmsModel->getOne(Array('id'=>$id));
		if(!$obj){
			errorAndRedirect('没找到类型定义！');
		}
		if($this->MCmsModel->dataExists($obj->code, $id)){
			errorAndRedirect('表中已经有数据，不允许删除！');
		}
		$this->load->model('MCmsCategory');
		if($this->MCmsCategory->getAll(Array('model_id'=>$id))){
			errorAndRedirect('类型被目录引用，不允许删除！');
		}
		if(!$this->MCmsModel->delete($obj, $update_time)){
			errorAndRedirect(l('data_fail'));
		}
		successAndRedirect(l('delete_success'));
	}
	function index($game_tag=null){
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$limit = $this->pagination($this->MCmsModel->getCount());
		$list = $this->MCmsModel->getList($limit);
		$this->assign('list', $list);
		$this->layout();
	}
}
