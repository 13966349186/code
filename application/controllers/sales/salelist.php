<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 订单模块-销售商品列表
 */
class SaleList extends AdminController {
	function __construct(){
		parent::__construct();

		
	}
	/** 订单查询 */
	function index($action = 'list'){
		$this->load->model('MOrder');
		$this->load->model('MOrderProduct');
		$this->load->library('Currency');
		$this->load->library('Games');
		$this->load->library('Sites');
		$this->load->library('Types');
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->formfilter->addFilter('orders.site_id', 'where');
		$this->formfilter->addFilter('orders.game_id', 'where');
		$this->formfilter->addFilter('order_product.type', 'where');
		$this->formfilter->addFilter('order_product.category_id', 'where');
		$this->formfilter->addFilter('orders.payment_state', 'where');
		$this->formfilter->addFilter('orders.state', 'where');
		$this->formfilter->addFilter('orders.state1', 'where',array('orders.state <> ',MOrder::STATE_UNSUBMITTED));
		$this->formfilter->addFilter('orders.risk', 'where');
		$this->formfilter->addFilter('orders.delivery_state', 'where');
		$this->formfilter->addFilter('orders.no', 'where');
		if($begintime = filterValue('create_begin')){
			$this->formfilter->addFilter('create_begin', 'where',array('orders.create_time >= ',strtotime($begintime.' 00:00:00')));
		}
		if($endtime = filterValue('create_end')){
			$this->formfilter->addFilter('create_end', 'where',array('orders.create_time <= ',strtotime($endtime.' 23:59:59')));
		}
		if($action == 'export'){  //导出CSV文件
			$this->_exprot();
			return ;
		}
		$limit = $this->pagination($this->MOrderProduct->getCount());
		$lst = $this->MOrderProduct->getList($limit);
		
		$orderStates = $this->MOrder->getState();
		unset($orderStates[MOrder::STATE_UNSUBMITTED]);
		$this->assign('lst', $lst);
		$this->assign('orderStates', $orderStates);
		$this->assign('paymentStates', $this->MOrder->getPayState());
		$this->assign('deliveryStates', $this->MOrder->getDeliveryState());
		$this->assign('riskStates', $this->MOrder->getRiskState());
		$this->layout();
	}
	
	private function _exprot(){
		$this->load->helper('download');
		//如果要下载的条数超过限制就抛出错误
		if($this->MOrderProduct->getCount() > 100000){
			show_error("要下载的订单条数超过10万条，请输入筛选条件，减少下载订单数量");
		}
		$lst = $this->MOrderProduct->getList(NULL);
		$output = '订单号,网站,游戏,类型,目录,商品,单价,数量,创建时间,发货时间,发货状态,风险等级,支付状态,订单状态';
		foreach ($lst as $v){
			$output .="\r\n" . '"	' .   $v->no . '",' 
			. '"' . element ( $v->site_id, $this->sites, $v->site_id ) . '",' 
			. '"' . element ( $v->game_id, $this->games, $v->game_id ) . '",' 
			. '"' . element ( $v->type, $this->types, $v->type )  . '",' 
			. '"' .$v->category_name . '",' 
			. '"' .$v->name . '",' 
			. '"' .$v->price . '",' 
			. '"' .$v->num . '",' 
			. '"' . date ( 'Y-m-d H:i:s', $v->create_time ) . '",' 
			. '"' . (($v->delivery_time==0) ?'' : date ('Y-m-d H:i:s', $v->delivery_time)) . '",' 
			. '"' .$this->MOrder->deliveryStates[$v->delivery_state] . '",' 
			. '"' .$this->MOrder->risks[$v->risk] . '",' 
			. '"' .$this->MOrder->paymentStates[$v->payment_state] . '",' 
			. '"' .$this->MOrder->states[$v->state]  . '"' ;
		}
		$name = 'sales' . date('YmdHis', time()) . '.csv' ;
		$output = "\xEF\xBB\xBF" . $output; //兼容execl
		force_download($name, $output);
	} 
	
	public function getTree($game_id,$category_id){
		$this->load->library('Games');
		$this->load->library('Categories',array($game_id));
		$game_name = $this->games[$game_id]?:null;
		echo $this->categories->displayPath($category_id, $game_name);
	}
	
}
