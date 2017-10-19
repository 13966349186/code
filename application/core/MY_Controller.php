<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminController extends CI_Controller {

	public $_thisModule = null;
	public $_thisController = null;
	public $_thisMethod = null;
	public $_required = array();
	protected $_data = array();
	protected $pagination_config = null;
	/** 
	 * @var UserPower $p 
	 * */
	public $p = null;
	public $_user = null;
	/** 菜单选中项的控制器，与权限无关 */
	protected $disp_menu_item_ctrl = null;
	
	function __construct() {
		parent::__construct();
		//默认的控制器访问权限
		$this->_required = array('index'=>VIEWPOWER,'add'=>ADDPOWER,'edit'=>EDITPOWER,'delete'=>DELETEPOWER);
		$this->_setEnableProfiler();
		$this->_initGlobal();
		$this->_initUser();
		$this->_initMenu();
		$this->assign('pagination','');
		$per_page = $this->config->item('per_page');
		$this->pagination_config = array(
				'num_links'=>9,
				'page_query_string'=>TRUE,
				'use_page_numbers'=>TRUE,
				'query_string_segment'=>'page',
				'full_tag_open'=>'<ul class="pagination pull-right">',
				'full_tag_close'=>'</ul>',
				'first_link'=>false,
				'last_link'=>false,
				'next_link'=>'&raquo;',
				'next_tag_open'=>'<li>',
				'next_tag_close'=>'</li>',
				'prev_link'=>'&laquo;',
				'prev_tag_open'=>'<li>',
				'prev_tag_close'=>'</li>',
				'cur_tag_open'=>'<li class="active"><a href="javascript:void(0);">',
				'cur_tag_close'=>'</a></li>',
				'num_tag_open'=>'<li>',
				'num_tag_close'=>'</li>',
				'per_page'=>$per_page?$per_page:'10',
		);
	}
	
	function _setEnableProfiler(){
		$profiler_key = $this->config->item('profiler_key');
		if(($profiler_key && $profiler_key == $this->input->get('profiler')) ){
			$this->output->enable_profiler(true);
		}else{
			$this->output->enable_profiler(false);
		}
	}

	function _initGlobal(){
		$this->_thisModule = $this->router->fetch_directory();
		$this->_thisController = $this->router->fetch_class();
		$this->_thisMethod = $this->router->fetch_method();
		$this->assign('thisModule',$this->_thisModule);
		$this->assign('thisController',$this->_thisController );
		$this->assign('thisMethod', $this->_thisMethod );
	}
	
	function _initUser(){
		$this->load->library('UserIdentity');
		$this->_user = $this->useridentity->getUser();
		$this->assign('_user', $this->_user);
		$this->p = UserPower::getPermisionInfo($this->_thisModule.$this->_thisController);
		$this->assign('p', $this->p);
	}

	function _initMenu(){
		$this->config->load('systemmenu');
		$menuLeft = Array();
		$allPageInfo = $this->config->item('menu_page');
		$groupIcon = $this->config->item('menu_group_icon');
		
		$myCtrl = $this->_thisModule.$this->_thisController;
		$ctrlName = '';
		$ctrlPath = '';
		$currCtrl = null;
		foreach($allPageInfo as $pageCtrl => $pageInfo){
			$pageCtrl = strtolower($pageCtrl);
			$currPage = (Object)$pageInfo;
			$currPage->ctrl = $pageCtrl;
			$tmpPow = UserPower::getPermisionInfo($pageCtrl);
			if(strtolower($pageCtrl) === strtolower($myCtrl)){
				$currCtrl = $currPage;
			}
			if(!$tmpPow->read){
				continue;
			}
			if(strtolower($pageCtrl) === strtolower($myCtrl)){
				$ctrlName = $currPage->name;
				$ctrlPath = $currPage->ctrl;
			}
			$currPage->sel = false;
			if($this->disp_menu_item_ctrl){
				if(strtolower($pageCtrl) === strtolower($this->disp_menu_item_ctrl)){
					$currPage->sel = true;
				}
			}else if(strtolower($pageCtrl) === strtolower($myCtrl)){
				$currPage->sel = true;
			}
			if($pageInfo['hide']){
				continue;
			}
			if(!array_key_exists($currPage->class, $menuLeft)){
				$menuLeft[$currPage->class] = new stdClass();
				$menuNode = &$menuLeft[$currPage->class];
				$menuNode->name = $currPage->class;
				$menuNode->sel = false;
				$menuNode->pages = Array();
				$menuNode->icon = array_key_exists($menuNode->name, $groupIcon)?$groupIcon[$menuNode->name]:'';
			}
			if($currPage->sel){
				$menuNode->sel = $currPage->sel;
			}
			$menuNode->pages[$currPage->id] = $currPage;
		}
		$this->assign('thisControllerName', $ctrlName);
		$this->assign('thisControllerPath', $ctrlPath);
		$this->assign('ctrlObj', $currCtrl);
		$this->assign('leftMenu', $menuLeft);
	}

	/**
	 * 表格翻页
	 * @param int $total
	 * @param string $uri_segment
	 * @param int $per_page
	 * @return multitype:number Ambigous <number, boolean, string, unknown>
	 */
	function pagination($total, $uri_segment = '', $per_page=NULL){
		$config = $this->pagination_config;
		$config['base_url'] = base_url($uri_segment ? $uri_segment : $this->uri->uri_string); 
		$config['total_rows'] = $total;
		$config['per_page'] = $per_page>0 ? $per_page:$config['per_page'];
		$offset =( $this->getPage() -1 ) * $config['per_page'];
		if($offset > 0 && $offset >= $total){
			//修正最后一页执行删除后页面显示空白的BUG
			$currPage = (int)ceil($total / $config['per_page']);
			$this->formfilter->setFilterValue('page', $currPage);
			$offset =( $this->getPage() -1 ) * $config['per_page'];
		}
		$this->load->library('Pagination');
		$this->pagination->initialize($config);
		$this->assign('pagination', $this->pagination->create_links());
		return array(
			'limit'=> $config['per_page'],
			'offset' => $offset
		);
	}

	function getPage(){
		$page = $this->formfilter->getFilterValue('page');
		if($page == null){
			$page = 1;
		}
		return $page;
	}

	function assign($key, $value){
		$this->_data[$key] = $value;
	}
	
	function getAssign(){
		return $this->_data;
	}
	function layout_modal($template=null,$data=null){
		$this->layout($template, $data, 'modal_index.tpl');
	}
	/**
	 * 加载view
	 * @param  $template
	 * @param  $data
	 */
	function layout($template=null,$data=null,$base="index.tpl"){
		if(!empty($data)){
			foreach($data as $key=>$value){
				$this->assign($key,$value);
			}
		}
		if(empty($template)){
			switch($this->_thisMethod){
				case 'add':
				case 'view':
				case 'edit':
					$type = 'form.tpl';
				break;
				case 'index':
					$type = 'list.tpl';
				break;
				default:
					$type = $this->_thisMethod.'.tpl';
				break;
			}
			$template = $this->_thisModule.$this->_thisController.'/'.$type;
		}else{
			if(!file_exists(FCPATH.APPPATH.'views/'.$template)){
				$tmp = $template;
				if(strpos(strtolower($tmp), '.tpl') === false){
					$tmp .= '.tpl';
				}
				if(file_exists(FCPATH.APPPATH.'views/'.$tmp)){
					$template = $tmp;
				}else if(file_exists(FCPATH.APPPATH.'views/'.$this->_thisModule.$this->_thisController.'/'.$tmp)){
					$template = $this->_thisModule.$this->_thisController.'/'.$tmp;
				}
			}
		}
		if(is_string($base)){
			$this->assign('hero',$template);
			$this->load->view($base, $this->_data);
		}else{
			$this->load->view($template, $this->_data);
		}
	}
	function _setTitle($str=''){
		$this->assign('breadName', $str);
	}
	
	/**
	 * 装载器
	 * @var CI_Loader $load
	 */
	public  $load;
	/**
	 * 输入类
	 * @var CI_Input $input
	 */
	public $input;
	/**
	 * 输出类
	 * @var CI_Output $output
	 */
	public $output;
	/**
	 * 系统配置
	 * @var CI_Config $config
	 */
	public $config;
	/**
	 * 
	 * @var CI_DB_active_record $db
	 */
	public $db;
	/**
	 * 筛选器
	 * @var FormFilter $formfilter
	 */
	public $formfilter;
	/**
	 * 表单验证类 
	 * @var MY_Form_validation $form_validation
	 */
	public $form_validation;
	/**
	 * 会话类
	 * @var MY_Session $session
	 */
	public $session;
	/**
	 * URI 路由
	 * @var MY_Router $router
	 */
	public $router;

}