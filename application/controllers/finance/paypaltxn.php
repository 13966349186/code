<?php
class PaypalTxn extends AdminController{
	
	function __construct(){
		parent::__construct();
		$this->load->model('MPaypalTxn');
		$this->load->model('MOrder');
		
		$payStates = $this->MPaypalTxn->getState();
		$orderStatus = $this->MOrder->getState();
		$this->assign('orderStatus',$orderStatus);
		$this->assign('payStates',$payStates);
		$this->load->library('Currency');
		$this->load->library('Sites');
	}
	
	public function index($action="list"){
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		
		if($begintime = filterValue('create_begin')){
			$this->formfilter->addFilter('create_begin', 'where',array('paypal_txn.payment_datetime >= ',strtotime($begintime.' 00:00:00')));
		}
		if($endtime = filterValue('create_end')){
			$this->formfilter->addFilter('create_end', 'where',array('paypal_txn.payment_datetime <= ',strtotime($endtime.' 23:59:59')));
		}
		$this->formfilter->addFilter('payment_status','where');
		$this->formfilter->addFilter('invoice','where');
		$this->formfilter->addFilter('txn_id','where');
		$this->formfilter->addFilter('receipt_id','where');
		$this->formfilter->addFilter('payer_email','where');
		$this->formfilter->addFilter('receiver_email','where');
		
		if($action == 'list'){
			$this->_list();
		}else{
			$this->_export();
		}
		
	}
	
	
	function _list(){
		$limit = $this->pagination($this->MPaypalTxn->getCount());
		$lst = $this->MPaypalTxn->getList($limit);
		$this->assign('lst', $lst);
		$this->layout();
	}
	
	function _export(){
		$this->load->helper('download');
		//如果要下载的条数超过10万就抛出错误
		if($this->MPaypalTxn->getCount() > 100000){
			show_error("要下载的订单paypal交易流水超过10万条，请输入筛选条件，减少下载paypal交易流水数量");
		}
		$lst = $this->MPaypalTxn->getList();
		$output = 'invoice,交易流水号,父交易流水号,recept_id,交易类型,城市,国家,国家代码'
		.',接收人姓名,州,地址状态,街道,邮编,名,姓,买家账户名称,买家邮箱,买家ID,买家状态,目的地国家'
		.',item_name,备注,税,付款时间,付款状态,支付类型,pending_reason,reason_code,币种,手续费,金额'
		.',custom,卖家账号,卖家邮箱,卖家ID,ipn_track_id,创建时间,更新时间';
		foreach ($lst as $v){
			$output .= "\r\n" . '"	' . $v->invoice . '",' . '"	' . $v->txn_id . '",' . '"	' . $v->parent_txn_id 
			. '",' . '"' . $v->receipt_id . '",' . '"' . $v->txn_type . '",' . '"' . $v->address_city 
			 . '",' . '"' .$v->address_country   . '",' . '"' .$v->address_country_code
			  . '",'. '"' . $v->address_name . '",' . '"' . $v->address_state .  '",'. '"'.$v->address_status
			  .  '",'. '"'.$v->address_street.  '",'. '"'.$v->address_zip.  '",'. '"'.$v->first_name
			  .  '",'. '"'.$v->last_name.  '",'. '"'.$v->payer_business_name.  '",'. '"'.$v->payer_email
			  .  '",'. '"	'.$v->payer_id.  '",'. '"'.$v->payer_status.  '",'. '"'.$v->residence_country
			  .  '",'. '"	'.$v->item_name.  '",'. '"'.$v->memo.  '",'. '"'.$v->tax
			  .  '",'. '"'.date('Y-m-d H:i:s', $v->payment_datetime).  '",'. '"'.$v->payment_status
			  .  '",'. '"'.$v->payment_type.  '",'. '"'.$v->pending_reason.  '",'. '"'.$v->reason_code
			  .  '",'. '"'.$v->mc_currency.  '",'. '"'.$v->mc_fee.  '",'. '"'.$v->mc_gross
			  .  '",'. '"	'.$v->custom.  '",'. '"'.$v->business.  '",'. '"'.$v->receiver_email
			  .  '",'. '"'.$v->receiver_id.  '",'. '"'.$v->ipn_track_id
			  .  '",'. '"'. date ( 'Y-m-d H:i:s', $v->create_time )
			  .  '",'. '"'.date ( 'Y-m-d H:i:s', $v->update_time ).'"' ;
		}
		$name = 'paypaltxn' . date('YmdHis', time()) . '.csv' ;
		//$output = iconv("UTF-8", "GBK//IGNORE", $data);
		$output = "\xEF\xBB\xBF" . $output; //兼容execl
		force_download($name, $output);
	}
}