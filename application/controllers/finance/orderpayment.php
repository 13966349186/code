<?php
class OrderPayment extends AdminController{
	
	function __construct(){
		parent::__construct();
		
		
		$this->load->model('MOrderPayment');
		$this->load->model('MPaymentMethod');
		$this->load->model('MSite');
		$this->paymentStates = $this->MOrderPayment->getState();
		$this->paymentTypes = $this->MOrderPayment->getType();
		
		$this->sites = parse_options($this->MSite->getAll());
		
		$this->load->library('PaymentMethod');
		$this->assign('paymentMethods', $this->paymentmethod->toArray());
		$this->assign('paymentStates', $this->paymentStates);
		$this->assign('paymentTypes', $this->paymentTypes);
		
		$this->assign('sites', $this->sites);
		$this->load->library('Currency');
		
	}
	
	public function index($action = "list"){
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		
		$this->formfilter->addFilter('orders.site_id', 'where');
		$this->formfilter->addFilter('order_no', 'where');
		$this->formfilter->addFilter('transcation_id', 'where');
		$this->formfilter->addFilter('order_payment.state', 'where');
		$this->formfilter->addFilter('order_payment.type', 'where');
		$this->formfilter->addFilter('orders.currency', 'where');
		$this->formfilter->addFilter('order_payment.payment_method', 'where');
		
		if($begintime = filterValue('create_begin')){
			$this->formfilter->addFilter('create_begin', 'where',array('order_payment.update_time >= ',strtotime($begintime.' 00:00:00')));
		}
		if($endtime = filterValue('create_end')){
			$this->formfilter->addFilter('create_end', 'where',array('order_payment.update_time <= ',strtotime($endtime.' 23:59:59')));
		}
		
		if($action == 'list'){
			$this->_list();
		}else{
			$this->_export();
		}
	}
	
	function _list(){
		$limit = $this->pagination($this->MOrderPayment->getCount());
		$lst = $this->MOrderPayment->getList($limit);
		$this->assign('lst', $lst);
		$this->layout();
	}
	
	function _export(){
		$this->load->helper('download');
		//如果要下载的条数超过10万就抛出错误
		if($this->MOrderPayment->getCount() > 100000){
			show_error("要下载的订单交易流水超过10万条，请输入筛选条件，减少下载交易流水数量");
		}
		$lst = $this->MOrderPayment->getList();
		$output = '订单号,网站,交易流水号,支付方式,交易类型,货币,金额,手续费,交易状态,创建时间,更新时间';
		foreach ($lst as $v){
			$output .= "\r\n" . '"	' . $v->order_no . '",' . '"' . element ( $v->site_id, $this->sites, $v->site_id ) 
			. '",' . '"' . $v->transcation_id . '",' . '"' . element ( $v->payment_method, $this->paymentmethod, $v->payment_method ) 
			 . '",' . '"' . element ( $v->type, $this->paymentTypes, $v->type )  
			 . '",' . '"' . $v->currency . '",' . '"' . $v->amount . '",' . '"' . $v->fee 
			  . '",' . '"' . element ( $v->state, $this->paymentStates, $v->state ) 
			    . '",' . '"' . date ( 'Y-m-d H:i:s', $v->create_time )  
			   . '",' . '"' . date ( 'Y-m-d H:i:s', $v->update_time )  .'"' ;
		}
		$name = 'orderpayment' . date('YmdHis', time()) . '.csv' ;
		//$output = iconv("UTF-8", "GBK//IGNORE", $data);
		$output = "\xEF\xBB\xBF" . $output; //兼容execl
		force_download($name, $output);
	}
}