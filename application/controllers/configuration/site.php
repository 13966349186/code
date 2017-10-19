<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 核心模块-网站平台
 */
class site extends AdminController {

	public $_validation = null;

	function __construct(){
		parent::__construct();
	//	$this->_required['editParam'] = VIEWPOWER;
		//$this->_required['editSitePaymethod'] = VIEWPOWER;
		$this->load->model('MSite');
		$this->_validation =  array(
			array('field' => 'domain', 'label' => l('site_domain'), 'rules' => 'required|max_length[128]|valid_domain|trim')
			,array('field' => 'name', 'label' => l('site_name'), 'rules' => 'required|max_length[128]')
			,array('field' => 'update_time', 'label' => l('update_time'), 'rules' => '')
			,array('field' => 'state', 'label' => l('site_state'), 'rules' => 'required|integer')
		);  
	}
	
	/** 添加新的网站平台 */
	function add(){
		$this->load->library('form_validation');
		$this->_validation[] = array('field' => 'domain', 'label' => l('site_domain'), 'rules' => 'required|max_length[128]|valid_domain|is_unique[core_site.domain]');
		$this->_validation[] = array('field' => 'name', 'label' => l('site_name'), 'rules' => 'required|max_length[128]|is_unique[core_site.name]');
		$this->_validation[] = array('field' => 'code', 'label' => l('site_code'), 'rules' => 'required|max_length[64]|is_unique[core_site.code]');

		$this->form_validation->set_rules($this->_validation);
		$this->load->helper('populate');
				$newSite=$this->MSite->createVo();
		
		if($this->form_validation->run()==TRUE){
			$newSite = populate($newSite,$this->form_validation->post());
			
			$newSite->domain = trim($newSite->domain);
			$newSite->update_time = time();
			
		if($this->MSite->save($newSite)){
				successAndRedirect(l('add_success'));
			}else{
			errorAndRedirect(l('data_fail'));
			}
			
		}
		$this->assign('site', $newSite);
		$this->layout();
	}
	
	/**
	 * 编辑网站平台
	 * @param  int $id 网站平台ID
	 */
	function edit($id){
		if(!is_numeric($id)){
			show_error(l('id_not_null'));
		}
		if(!($site = $this->MSite->getById($id))){
			show_error('指定网站不存在！');
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->_validation);
		if($this->form_validation->run()==TRUE){
			$this->load->helper('populate');
			$site = populate($site, $this->form_validation->post());
	
			if($this->MSite->update($site)){
				successAndRedirect(l('edit_success'));
			}else{
				error(l('data_fail'));
			}
		}
		
		$this->assign('site', $site);
		$this->layout();
	}

	function index(){
		$this->load->model('MSite');
		$this->load->library('FormFilter');
	$this->load->helper('formfilter');
		if($domain = $this->formfilter->getFilterValue('domain')){
			var_dump($domain);exit;
			$domain = $this->db->escape_str($domain);
			$this->formfilter->addFilter('domain', 'where', array( "(domain like '%$domain%' or name like '%$domain%')" ) );
		} 
	$this->formfilter->addFilter('state', 'where');
		$limit = $this->pagination($this->MSite->getCount());
		
		$sites = $this->MSite->getList($limit);	
		$this->assign('sites',$sites);
		$this->layout();
	}

	function editParam($id){
		$this->load->model('MSite');
		$this->load->model('MSiteConfig');
		$this->load->model('MSiteConfigDef');
		$defs = $this->MSiteConfigDef->getConfigDef($id);
		
		$params = $this->MSiteConfig->getConfig($id);
		$confs = Array();
		foreach ($params as $val){
			$confs[$val->config_key] = $val->value;
		}
		$postInfo = $this->input->post();
		if(!$postInfo){
			$postInfo = array();
		}
	
		foreach ($defs as $def){
			if($def->data_type == '2'){
				$arr = Array();
				//下拉框
				if(isset($def->data_extra) && strlen($def->data_extra) > 0){
					$tmpData = explode(';', $def->data_extra);
					foreach ($tmpData as $item){
						$itemSub = explode('=', $item);
						if(count($itemSub) > 1){
							$arr[$itemSub[0]] = $itemSub[1];
						}else if(count($itemSub) == 1){
							$arr[$itemSub[0]] = $itemSub[0];
						}
					}
				}
				$def->selectList = $arr;
			}
			$def->config_value = '';

			if(array_key_exists($def->config_key, $confs)){ 
				
				$def->config_value = $confs[$def->config_key];
			}
			$def->value = $def->config_value;
			
			if(array_key_exists($def->config_key, $postInfo)){
				
				$def->value = $postInfo[$def->config_key];
			}
		}
		
		if($this->p->edit && $postInfo){
				if($this->MSiteConfig->saveConfig($id, $defs)){
					successAndRedirect(l('edit_success'));
				}else{
					errorAndRedirect(l('data_fail'));
				}
		}
		$site = $this->MSite->getById($id);
		
		//$this->assign('site',$site);
		$this->assign('defs',$defs);
		$this->layout('param');
	}
	
	/**
	 * 编辑网站支付方式
	 * @param  int $id 网站平台ID
	 */
/*	function editSitePaymethod($id){
		if(!is_numeric($id)){
			show_error(l('id_not_null'));
		}
		$this->load->library('PaymentMethod');
		$this->load->model('MSite');
		$this->load->model('MSitePayment');
		$this->load->model('MPaymentAccount');
		$this->load->library('form_validation');
		foreach ($this->paymentmethod as $k=>$v){
			$this->form_validation->set_rules('account_id'.$k, '支付账号', 'integer|callback__checkAcct');
			$this->form_validation->set_rules('sort'.$k, '排序', '');
			$this->form_validation->set_rules('add'.$k, '添加', 'integer');
		}
		$accountDropList = Array();
		$defaultVals = Array();
		if($result = $this->MSitePayment->getSitePayMents($id)){
			foreach ($result as $v){
				$accountDropList[$v->method_id][] = $v;
				if($v->site_payment_id){
					$defaultVals[$v->method_id] = $v;
				}
			}
		}
		if($this->p->edit && $this->form_validation->run()==TRUE){
			$defs = Array();
			foreach ($this->paymentmethod as $k=>$v){
				if($this->form_validation->post('add'.$k)){
					if(!$this->form_validation->post('account_id'.$k)){
						$this->form_validation->set_error('account_id'.$k, '支付账号 必须选择');
					}else if(((int)$this->form_validation->post('sort'.$k)).'' !== $this->form_validation->post('sort'.$k).''){
						$this->form_validation->set_error('sort'.$k, '排序 必须是整数');
					}else{
						$defs[] = array('account_id'=>$this->form_validation->post('account_id'.$k), 'site_id'=>$id, 'sort'=>$this->form_validation->post('sort'.$k));
					}
				}
			}
			if(!validation_errors()){
				if($this->MSitePayment->saveConfig($id, $defs)){
					successAndRedirect(l('edit_success'));
				}else{
					errorAndRedirect(l('data_fail'));
				}
			}
		}
		$this->assign('site', $this->MSite->getById($id));
		$this->assign('accountDropList',$accountDropList);
		$this->assign('defaultVals',$defaultVals);
		$this->assign('paymentmethod',$this->paymentmethod);
		$this->layout('sitepaymethod');
	}
}
*/
}