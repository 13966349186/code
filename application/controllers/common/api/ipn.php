<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
/**
 * PayPal IPN通知处理
 * @author zhangyk
 *
 */
class Ipn extends CI_Controller {
	private $order_log;
	function __construct() {
		parent::__construct ();
		$this->log = IOSS_Logger::getIntance ();
		$this->load->helper ('ioss_order');
		$this->load->helper ( 'populate' );
		$this->load->model ( 'MOrder' );
		$this->load->model ( 'MPaypalTxn' );
		$this->load->model ( 'MOrderPayment' );
		$this->load->model ( 'MOrderLog' );
		$this->load->model ( 'MOrderIndex' );
		$this->load->model ( 'MCurrency' );
		$this->load->model ( 'MPaymentAccount' );
		$this->order_log = Array (	'type' => MOrderLog::TYPE_PAY,	'note' => '','admin_id' => 0,'admin' => 'system (IPN)' );
	}
	
	/**
	 * 处理PAYPAL发送的IPN报文
	 */
	public function index() {
		if ($return = $this->input->post()) {
			//$this->log->message ( http_build_query ( $return ), IOSS_Logger::IPN_MSG ); // 以url参数形式打印ipn日志
			
			//向paypal服务器验证报文是否合法
			// if(!$this->_verify($return)){
			// $this->log->errorMessage("未通过paypal认证,txnid==".(array_key_exists('txn_id', $return)?$return['txn_id']:''));
			// exit();
			// }
			
			if (array_key_exists ( 'txn_type', $return ) && $return ['txn_type'] == 'new_case') {
				//@todo 争议通知报文
			}else{
				$this->dealMsg ( $return);
			}
		}else{
			show_error('data error!');
		}
	}
	

	/**
	 * 测试用方法
	 */
	public function test() {
		if(ENVIRONMENT != 'development'){
			return ;
		}
		$this->log->display = true;
		$ipnMessage = $this->input->get(); 
		$this->dealMsg ( $ipnMessage );
	}
	
	/**
	 * IPN处理代码
	 */
	public function dealMsg($return = array()) {
		// *** 记录日志 ***
		$msg = 'custom=' . element ( 'custom', $return, '' ) . ' invoice=' . element ( 'invoice', $return, '' ) . ' payment_status=' . element ( 'payment_status', $return, '' ) . ' txn_type=' . element ( 'txn_type', $return, '' ) . ' txn_id=' . element ( 'txn_id', $return, '' ) . ' mc_gross=' . element ( 'mc_gross', $return, '' );
		$this->log->message ( $msg, IOSS_Logger::IPN_LOG );

		$order = $this->_checkIPN($return);
		if (!$order) {
			$this->log->message ('报文数据异常！', IOSS_Logger::IPN_LOG );
			exit ();
		}
		$this->order_log['note'] = "【IPN】状态: {$return['payment_status']}  " . (array_key_exists('memo', $return) ? ', memo: ' . $return['memo'] : '' );
		
		// 付款记录
		$time = time();
		$order_payment = array (
				'order_id' => $order->id,
				'order_no' => $order->no,
				'payment_method' => $order->payment_method,
				'currency' =>$return ['mc_currency'],
				'transcation_id' => $return ['txn_id'],
				'amount' => $return ['mc_gross'],
				'fee'=>array_key_exists ( 'mc_fee', $return )?$return['mc_fee']:0,
				'admin_id' => '0',
				'admin' => 'system',
				'note' => "IPN - 状态: {$return['payment_status']}，金额:{$return['mc_gross']}",
				'create_time' => $time,
				'update_time' =>$time
		);
		// 如果存在交易编号相同记录则更新，否则插入新纪录
		$order_payment_exists = $this->MOrderPayment->getOne(array('transcation_id'=>$order_payment['transcation_id'], 'order_no'=>$order_payment['order_no']));
		if ($order_payment_exists) {
			$order_payment ['id'] = $order_payment_exists->id;
			$order_payment ['update_time'] = $order_payment_exists->update_time;
		}
		$order_payment = ( object ) $order_payment;
		
		/*********************************************************************************************************************************************
		 * IPN状态 ：
		 * txn_type = web_accept ，payment_status= Pending             订单付款正在进行中，可能是echeck等支付方式
		 * txn_type = web_accept ，payment_status= Completed         订单付款完成
		 * txn_type = web_accept ，payment_status= Denied / Failed   订单付款失败，由 Pending 状态变更
		 * txn_type = 空 ，payment_status= Refunded                         卖家主动退款，可能完全退款货部分退款
		 * txn_type = 空 ，payment_status= Reversed	                       买家投诉，有两种可能：mc_gross 等于付款金额的负值，当做完全退款处理；或mc_gross金额小于付款金额，是临时状态，之后会变为Canceled_Reversal 状态
		 * txn_type = 空 ，payment_status= Canceled_Reversal            买家投诉取消
		 * txn_type = adjustment ,  payment_status= Completed          买家投诉结束后的最终状态，可能有多次。金额可以为正或负，绝对值等于付款金额。
		 ************************************************************************************************************************************************/
		if(array_key_exists('txn_type', $return) && $return ['txn_type'] == 'adjustment'){
			$order_payment->note = "IPN - 状态: {$return['txn_type']}";
			$this->order_log['note'] = "【IPN】状态: {$return['txn_type']}";
			$this->_doAdjustment( $order_payment, $order );
		}else{
			switch ($return ['payment_status']) {
				case MPaypalTxn::PP_Pending :
					$this->_doPending ( $order_payment, $order );
					break;
				case MPaypalTxn::PP_Completed :
					$this->_doCompleted ( $order_payment, $order );
					$this->_saveOrderIndex($order->id, $return);
					break;
				case MPaypalTxn::PP_Denied :
				case MPaypalTxn::PP_Failed :
					$this->_doDeniedOrFailed ( $order_payment, $order );
					break;
				case MPaypalTxn::PP_Refunded :
					$this->_doRefunded ( $order_payment, $order );
					break;
				case MPaypalTxn::PP_Reversed :
					$this->_doReversal ( $order_payment, $order );  //临时冻结
					break;
				case MPaypalTxn::PP_Canceled_Reversal :
					$this->_doCancelReversal ( $order_payment, $order );
					break;
				default :
					$this->log->errorMessage ( "IPN状态码无法识别!return['payment_status']==" . $return ['payment_status'] . ",txn_id==" . $return ['txn_id'] );
					exit ();
			}
		}
		
		//根据支付状态，自动更新订单状态
		$this->_updateOrderState($order);
		
		//PayPal发生投诉时，根据支付状态，自动修改订单验证
		if(element('txn_type', $return) == 'adjustment' ||  element('payment_status',$return) == MPaypalTxn::PP_Reversed){
			$this->_updateOrderRisk($order);
		}
		
		//插入 IPN 记录
		if( !$this->_savePaypalTxn($return, $order_payment->id, $order->id)){
			$this->log->errorMessage ( "保存paypal记录失败!txn_id==" . $return ['txn_id'] );
		}
		
		$this->log->message ( "ipn处理完成\r\n", IOSS_Logger::IPN_LOG );
	}
	
	/*
	 * 处理pending报文
	 */
	function _doPending(&$order_payment, $order) {
		if ($order->payment_state != MOrder::PAY_STATE_UNPAIED || float_compare ( $order_payment->amount, $order->amount ) != 0) {
			$this->log->errorMessage ( "IPN返回状态为Pending时，订单付款状态不是未付款，或者付款金额和支付金额不相等, txn_id==" . $order_payment->transcation_id );
			exit ();
		}
		// order_payment表type为pay,state为Pending
		$order_payment->type = MOrderPayment::TYPE_PAY;
		$order_payment->state = MOrderPayment::STATE_PENDING;
		if (! $this->MOrderPayment->save ( $order_payment, $order, (object)$this->order_log )) {
			$this->log->errorMessage ( "修改订单和订单付款信息失败! txn_id==" . $order_payment->transcation_id );
			show_error('Update error: Pending');
		}
	}
	
	/*
	 * 处理completed报文
	 */
	function _doCompleted(&$order_payment, $order) {
		if ($order->payment_state != MOrder::PAY_STATE_PEDING && $order->payment_state != MOrder::PAY_STATE_UNPAIED) { // 检查订单当前支付状态，防止重复处理报文
			$this->log->errorMessage ( "订单付款状态和IPN返回付款状态不符-1，order->payment_state==" . $order->payment_state . " txn_id==" . $order_payment->transcation_id );
			exit ();
		}
		if (float_compare ( $order_payment->amount, $order->amount ) == - 1) { //检查金额，防止伪造交易
			$this->log->errorMessage ( "付款金额不足，mc_gross==" . $order_payment->amount . ",amount===" . $order->amount . ",txn_id==" . $order_payment->transcation_id );
			exit ();
		}
		
		// order_payment表type为pay,state为Completed
		$order_payment->type = MOrderPayment::TYPE_PAY;
		$order_payment->state = MOrderPayment::STATE_COMPLETED;
		if (! $this->MOrderPayment->save ( $order_payment, $order, (object)$this->order_log )) {
			$this->log->errorMessage ( "修改订单和订单付款信息失败!txn_id==" . $order_payment->transcation_id );
			show_error('Update error: Completed');
		}
	}
	
	
	function _doAdjustment(&$order_payment, $order){
		$order_payment->type = MOrderPayment::TYPE_CHARGEBACK;
		$order_payment->state = MOrderPayment::STATE_COMPLETED;
		if (! $this->MOrderPayment->save ( $order_payment, $order, (object)$this->order_log )) {
			$this->log->errorMessage ( "修改订单和订单付款信息失败!txn_id==" . $order_payment->transcation_id );
			show_error('Update error - 3');
		}
	}
	
	/*
	 * 处理denied/failed报文
	 */
	function _doDeniedOrFailed(&$order_payment, $order) {
		$order_payment->state = MOrderPayment::STATE_CANCELLED;
		if (! $this->MOrderPayment->save ( $order_payment, $order, (object)$this->order_log )) {
			$this->log->errorMessage ( "修改订单和订单付款信息失败!txn_id==" . $order_payment->transcation_id );
			show_error('Update error - 2');
		}
	}
	
	/*
	 * 处理refunded报文
	 */
	function _doRefunded(&$order_payment, $order) {
		// 收到IPN状态为Refunded
		if ($order->payment_state == MOrder::PAY_STATE_UNPAIED || $order->payment_state == MOrder::PAY_STATE_REFUNDED) {
			$this->log->errorMessage ( "订单付款状态和IPN返回付款状态不符-2，order->payment_state===" . $order->payment_state . ",return['payment_status']===" . MPaypalTxn::PP_Refunded . ",txn_id==" . $order_payment->transcation_id );
			exit ();
		}
		// order_payment表type为refund,state为complete，金额为paypal发过来的金额
		$order_payment->type = MOrderPayment::TYPE_REFUND;
		$order_payment->state = MOrderPayment::STATE_COMPLETED;
		if (! $this->MOrderPayment->save ( $order_payment, $order, (object)$this->order_log )) {
			$this->log->errorMessage ( "修改订单和订单付款信息失败!txn_id==" . $order_payment->transcation_id );
			show_error('Update error: Refunded');
		}
	}
	
	/*
	 * 处理reversal报文
	 */
	function _doReversal(&$order_payment, $order) {
		$order_payment->type = MOrderPayment::TYPE_CHARGEBACK;
		if(float_compare($order_payment->amount + $order->amount, 0) == 1){
			$order_payment->state = MOrderPayment::STATE_PENDING;  //临时冻结
		}else{
			$order_payment->state = MOrderPayment::STATE_COMPLETED; //投诉处理结束
		}
		// 保存订单付款记录
		if (! $this->MOrderPayment->save ( $order_payment, $order, (object)$this->order_log )) {
			$this->log->errorMessage ( "修改订单和订单付款信息失败!txn_id==" . $order_payment->transcation_id );
			show_error('Update error - 4');
		}
	}
	
	/*
	 * 处理cancel_reversal报文
	 */
	function _doCancelReversal(&$order_payment, $order) {
		// order_payment表type为chargeback,state为complete,金额为0
		$order_payment->type = MOrderPayment::TYPE_CHARGEBACK;
		$order_payment->state = MOrderPayment::STATE_CANCELLED;
		$order_payment->amount = 0;
		$order_payment->fee = 0;
		// 保存订单付款记录
		if (! $this->MOrderPayment->save ( $order_payment, $order, (object)$this->order_log )) {
			$this->log->errorMessage ( "修改订单和订单付款信息失败!txn_id==" . $order_payment->transcation_id );
			show_error('Update error - 5');
		}
	}
	
	/**
	 * IPN验证
	 * @param array $return        	
	 * @return string
	 */
	private function _verify($return) {
		// paypal付款方式配置信息
		// @todo
		$url = '';
		// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
		$req = 'cmd=_notify-validate';
		foreach ( $return as $key => $value ) {
			$value = urlencode ( $value );
			$req .= "&$key=$value";
		}
		// Step 2: POST IPN data back to PayPal to validate
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $req );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 1 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt ( $ch, CURLOPT_FORBID_REUSE, 1 );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (	'Connection: Close'	) );
		
		if (! ($res = curl_exec ( $ch ))) {
			curl_close ( $ch );
			show_error ( 'VERIFY ERROR' );
		}
		curl_close ( $ch );
		return strcmp ( $res, "VERIFIED" ) == 0;
	}
	
	/**
	 * 检查付款相关报文的数据合法性
	 * @param array $msg
	 * @return object
	 */
	private function _checkIPN($msg) {
		if (! (array_key_exists ( 'invoice', $msg ) && $msg ['invoice'])) {
			$this->log->errorMessage ( "订单号不存在 invoice" );
			return false;
		}
		if (! (array_key_exists ( 'txn_id', $msg ) && $msg ['txn_id'])) {
			$this->log->errorMessage ( "订单号不存在, txn_id" );
			return false;
		}
		//如果报文有父交易ID，检查父交易是否存在
		if (array_key_exists ( 'parent_txn_id', $msg )) {
			$parent_txn_id = $msg ['parent_txn_id'];
			$paypal = $this->MPaypalTxn->getByTxnId ( $parent_txn_id );
			if (! $paypal) {
				$this->log->errorMessage ( "父交易不存在，parent_txn_id==" . $parent_txn_id );
				return false;
			}
		}else if (!$this->MPaymentAccount->getByAccount($msg ['receiver_email'])) {   //如果没有父交易id，则检查收款 email 是否存在
			$this->log->errorMessage ( "收款账号不合法,  return['receiver_email']===" . $msg ['receiver_email'] . ",txn_id==" . $msg ['txn_id']  . ',txn_type=' . $msg ['txn_type'] );
			return false;
		}
		
		// 检查订单号是否存在
		$order = $this->MOrder->getOne ( array('no'=>$msg ['invoice']) );
		if (! $order) {
			$this->log->errorMessage ( "根据订单ID获取订单信息出错，订单no =" . $msg ['invoice'] . ",txn_id==" . $msg ['txn_id'] );
			return false;
		}
		// 检查付款币种
		if ($order->currency != $msg ['mc_currency']) {
			$this->log->errorMessage ( "订单币种与PAYPAL支付币种不符,order->currency=" . $order->currency . ", return['mc_currency']=" . $msg ['mc_currency'] . ",txn_id==" . $msg ['txn_id'] );
			return false;
		}
		return $order;
	}
	/**
	 * 检查投诉报文数据合法性
	 * @param array $msg        	
	 */
	private function _checkCaseIPN($msg) {
	}
	

	/**
	 * 变更订单状态字段 (order.state)
	 * @param object $order
	 */
	private function _updateOrderState($order){
		$state = calc_order_state($order);
		if($state != $order->state){
			$order->state = $state;
			$log = (object) $this->order_log;
			$log->type = MOrderLog::TYPE_EDIT;
			$log->note = '【自动】修改订单状态为：' . $this->MOrder->getState($state);
			$this->MOrder->save($order, $log);
		}
	}
	/**
	 * 修改订单验证状态
	 * @param object $order
	 */
	private function _updateOrderRisk($order){
		//已发货完成，用户投诉成功，修改订单为“欺诈”
		if($order->payment_state == MOrder::PAY_STATE_REFUNDED 
			&& $order->delivery_state == MOrder::DELEVERY_STATE_DELEVERED
			&& $order->risk != MOrder::RISK_FRAUD){
				$order->risk = MOrder::RISK_FRAUD;
				$log = (object) $this->order_log;
				$log->type = MOrderLog::TYPE_VERIFY;
				$log->note = '【自动】订单风险等级为 ' . $this->MOrder->getRiskState($order->risk);
				$this->MOrder->save($order, $log);
		}
	}
	
	/**
	 * 入库paypal_txn信息
	 * @param array $msg
	 * @param int $order_paymet_id
	 * @param int $order_id
	 * @return boolean
	 */
	private function _savePaypalTxn($msg, $order_paymet_id, $order_id){
		$paypal = $this->MPaypalTxn->createVo();
		$paypal = populate ( $paypal, $msg );
		$paypal->payment_datetime = strtotime ( $msg ['payment_date'] );
		$paypal->order_payment_id = $order_paymet_id;
		return $this->MPaypalTxn->add ( $paypal );
	}
	
	/**
	 * 修改订单索引值
	 * @param int $order_id
	 * @param object $paypal
	 */
	private function _saveOrderIndex($order_id, $msg){
		$cols = array('payer_email','payer_id');
		foreach ($cols as $col){
			if(array_key_exists($col, $msg) && $msg[$col]){
				$order_index = array ('order_id' => $order_id, 'table_name' => 'paypal_txn','col_name' => $col,'col_value' =>$msg[$col]);
				$this->MOrderIndex->save ( ( object ) $order_index );
			}
		}
	}
}
/* End of file ipn.php */
/* Location: ./application/controllers/ipn.php */