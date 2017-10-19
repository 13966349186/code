<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 订单模块-订单管理
 */
class Order extends AdminController {
	function __construct(){
		parent::__construct();
		$this->load->model('MOrder');
		$this->load->model('MOrderLog');
		$this->assign('orderStates', $this->MOrder->getState());
		$this->assign('paymentStates', $this->MOrder->getPayState()+Array('paid_or_part'=>'已付款 &amp; 部分付款'));
		$this->assign('deliveryStates', $this->MOrder->getDeliveryState());
		$this->assign('riskStates', $this->MOrder->getRiskState());
		$this->load->library('Currency');
		$this->load->library('Games');
		$this->load->library('Sites');
		$this->load->library('PaymentMethod');
		$this->_required['view'] = VIEWPOWER;
		$this->_required['state'] = EDITPOWER;
		$this->_required['note'] = EDITPOWER;
		
	}
	
	/** 订单列表页 */
	function index($action = 'list'){
		$this->load->library('Types');
		$this->load->library('FormFilter');
		$this->load->helper('formfilter');
		$this->load->library('Currency');

		$this->formfilter->addFilter('orders.site_id', 'where');
		$this->formfilter->addFilter('orders.game_id', 'where');
		$this->formfilter->addFilter('orders.product_type', 'where');
		if(filterValue('payment_state') == 'paid_or_part'){
			$this->formfilter->addFilter('orders.payment_state', 'where',array('('.$this->db->dbprefix('orders').'.payment_state = '.MOrder::PAY_STATE_PART.' or core_orders.payment_state = '.MOrder::PAY_STATE_PAID.')'));
		}else{
			$this->formfilter->addFilter('orders.payment_state', 'where');
		}
		$this->formfilter->addFilter('orders.state', 'where');
		$this->formfilter->addFilter('orders.hold_reason', 'where');
		$this->formfilter->addFilter('orders.delivery_state', 'where');
		$this->formfilter->addFilter('orders.risk', 'where');
		$this->formfilter->addFilter('orders.payment_method', 'where');
		if($begintime = filterValue('create_begin')){
			$this->formfilter->addFilter('create_begin', 'where',array('orders.create_time >= ',strtotime($begintime.' 00:00:00')));
		}
		if($endtime = filterValue('create_end')){
			$this->formfilter->addFilter('create_end', 'where',array('orders.create_time <= ',strtotime($endtime.' 23:59:59')));
		}
		$index_filter = filterValue('stxt');
		
		if($action == 'export'){
			//导出CSV文件
			$this->_export($index_filter);
		}else{
			//显示列表页
			$this->_list($index_filter);
		}
	}
	
	function _list($index_filter){
		//标签定义
		$filter_define = Array(
				'全部' => Array()
				,'处理中' => Array('state'=>MOrder::STATE_OPEN)
				,'待验证' => Array('risk'=>MOrder::RISK_MEDIUM, 'state'=>MOrder::STATE_OPEN)
				,'待发货' => Array('payment_state'=>'paid_or_part', 'state'=>MOrder::STATE_OPEN, 'risk'=>MOrder::RISK_LOW)
				,'问题单' => Array('state'=>MOrder::STATE_HOLDING)
		);
		$filters_all = array ('site_id',	'game_id',	'product_type' ,	'payment_method' ,'payment_state',	'delivery_state','risk' ,'state',	'create_begin' ,'create_end' );
		$filters = array();
		foreach ($filters_all as $k){
			if(filterValue($k) != ''){
				$filters[$k] = filterValue($k);
			}
		}
		
		$sort = filterValue('sort');
		$limit = $this->pagination($this->MOrder->getCount($index_filter), $this->_thisModule.$this->_thisController.'/'.$this->_thisMethod);
		$lst = $this->MOrder->getList($limit, $sort, $index_filter);
		$this->assign('lst', $lst);
		$this->assign('paymentMethods', $this->paymentmethod->toArray());
		$this->assign('filters', $filters);
		$this->assign('filters_all', $filters_all);
		$this->assign('filter_define', $filter_define);
		$this->layout();
	}
	
	
	function _export($index_filter){
		$this->load->helper('download');
		//如果要下载的条数超过10万就抛出错误
		if($this->MOrder->getCount($index_filter) > 100000){
			show_error("要下载的订单条数超过10万条，请输入筛选条件，减少下载订单数量");
		}
		$lst = $this->MOrder->getList(NULL, FALSE, $index_filter);
		$output = '订单号,网站,用户名,邮箱,电话,IP,游戏,商品类型,支付方式,货币,金额,创建时间,风险等级,支付状态,发货状态,订单状态';
		foreach ($lst as $v){
			$output .= "\r\n" . '"	' . $v->no . '",' . '"' . element ( $v->site_id, $this->sites, $v->site_id ) . '",' . '"' . $v->user_full_name . '",' . '"' . $v->user_email . '",' . '"' . $v->user_phone . '",' . '"' . $v->user_ip . '",' . '"' . element ( $v->game_id, $this->games, $v->game_id ) . '",' . '"' . element ( $v->product_type, $this->types, $v->product_type ) . '",' . '"' . element ( $v->payment_method, $this->paymentmethod, $v->payment_method ) . '",' . '"' . $v->currency . '",' . '"' . $v->amount . '",' . '"' . date ( 'Y-m-d H:i:s', $v->create_time ) . '",' . '"' . $this->MOrder->risks [$v->risk] . '",' . '"' . $this->MOrder->paymentStates [$v->payment_state] . '",' . '"' . $this->MOrder->deliveryStates [$v->delivery_state] . '",' . '"' . $this->MOrder->states [$v->state] . '"';
		}
		$name = 'orders' . date('YmdHis', time()) . '.csv' ;
		//$output = iconv("UTF-8", "GBK//IGNORE", $data);
		$output = "\xEF\xBB\xBF" . $output; //兼容execl
		force_download($name, $output);
	}

	
	/**
	 * 订单详细
	 * @param integer $order_id 订单标识
	 */
	function view($order_id){
		if(!is_numeric($order_id) || !($order = $this->MOrder->getOne(Array('id'=>$order_id)))){
			model_error('当前订单不存在!');
		}
		$this->load->model('MOrderAttribute');
		$this->load->model('MOrderPayment');
		$this->load->model('MOrderProduct');
		$this->load->library('Types');
		$this->load->library('Categories',array($order->game_id));
		
		$attrs = $this->MOrderAttribute->getAll(Array('order_id'=>$order->id));
		$payments = $this->MOrderPayment->getAll(array('order_id' =>$order->id));
		$tmp = Array(MOrderPayment::TYPE_REFUND=>0, MOrderPayment::TYPE_PAY=>0, MOrderPayment::TYPE_CHARGEBACK=>0, 'total'=>0);
		foreach ($payments as $v){
			if($v->state == MOrderPayment::STATE_COMPLETED){
				$tmp[$v->type] += $v->amount;
				$tmp['total'] += $v->amount;
			}
		}
		$payments = $tmp;
		$logs = $this->MOrderLog->getAll('order_id ='.$order->id.' order by id desc');
		$products = $this->MOrderProduct->getListByOrderId($order->id);
		$this->assign('products', $products);
		$this->assign('payments', $payments);
		$this->assign('attrs', $attrs);
		$this->assign('order', $order);
		$this->assign('logs', $logs);
		$this->layout('view');
	}
	/** 添加备注 */
	function note($order_id){
		if(!is_numeric($order_id) || !($order = $this->MOrder->getOne(Array('id'=>$order_id)))){
			model_error('当前订单不存在!');
		}
		//保存数据
		$validation =array(
				array('field'=>'note', 'label'=>'备注', 'rules'=>'max_length[120]')
				,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
		);
		$this->load->library('Form_validation');
		$this->form_validation->set_rules($validation);
		if($this->form_validation->run()){
			$this->load->model('MOrderLog');
			$log = new stdClass();
			$log->order_id = $order->id;
			$log->admin = $this->_user->name.'('.$this->_user->account.')';
			$log->admin_id = $this->_user->id;
			$log->note = '【备注】'.$this->input->get_post('note');
			$log->type = MOrderLog::TYPE_NOTE;
			$log->payment_state = $order->payment_state;
			$log->delivery_state = $order->delivery_state;
			$log->state = $order->state;
			$log->risk = $order->risk;
			$log->create_time = $this->update_time = time();
			if($this->MOrderLog->add($log)){
				model_success(l('edit_success'));
			}else{
				model_error(l('data_error'));
			}
		}
		$this->assign('thisControllerName', '添加订单备注');
		$this->assign('order', $order);
		$this->layout_modal();
	}
	/**
	 * 顾客信息编辑
	 * @param integer $order_id 订单编号
	 */
	function edit($order_id){
		if(!is_numeric($order_id) || !($order = $this->MOrder->getOne(Array('id'=>$order_id)))){
			model_error('当前订单不存在!');
		}
		//保存数据
		$validation =array(
				array('field'=>'user_full_name', 'label'=>'姓名', 'rules'=>'max_length[64]')
				,array('field'=>'user_email', 'label'=>'Email', 'rules'=>'max_length[128]|callback__checkMail')
				,array('field'=>'user_phone', 'label'=>'电话', 'rules'=>'max_length[32]')
				,array('field'=>'note', 'label'=>'备注', 'rules'=>'max_length[120]')
				,array('field'=>'update_time', 'label'=>'更新时间', 'rules'=>'')
		);
		$this->load->library('Form_validation');
		$this->form_validation->set_rules($validation);
		if($this->form_validation->run()){
			$this->load->helper('populate');
			$order = populate($order, $this->form_validation->post());
			unset($order->note);
			$log = (Object)Array('type'=>MOrderLog::TYPE_EDIT, 'note'=>'【修改顾客信息】'.$this->input->get_post('note'), 'admin_id'=>$this->_user->id, 'admin'=>$this->_user->name.'('.$this->_user->account.')');
			if($this->MOrder->save($order, $log)){
				model_success(l('edit_success'));
			}else{
				model_error(l('data_error'));
			}
		}
		$this->assign('thisControllerName', '顾客信息');
		$this->assign('order', $order);
		$this->layout_modal('edit');
	}
	function _checkMail($mail){
		if(!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $mail)){
			$this->form_validation->set_message('_checkMail', 'Email 格式不正确！');
			return false;
		}
		return true;
	}
	/**
	 * 修改订单状态
	 * @param int $order_id
	 * @param int $state
	 * @param int $update_time
	 */
	function state($order_id, $state, $update_time){
		if(!is_numeric($order_id) || !($order = $this->MOrder->getOne(Array('id'=>$order_id)))){
			model_error('当前订单不存在!');
		}
		if($update_time != $order->update_time){
			model_error('当前订单已被修改，请重新刷新订单页面!');
		}
		if ($state == MOrder::STATE_OPEN && $order->state == MOrder::STATE_HOLDING){
			//解除问题单
			$title = '解除问题单';
		}else if ($state == MOrder::STATE_OPEN && $order->state == MOrder::STATE_CLOSED){
			//重新打开订单
			$title = '重新打开订单';
		}else if ($state == MOrder::STATE_HOLDING && $order->state == MOrder::STATE_OPEN){
			//设置问题单
			$title = '设置问题单';
		}else if ($state == MOrder::STATE_CLOSED ){
			//关闭订单
			$title = '关闭订单';
		}else{
			model_error('订单操作错误，请刷新订单页面后重试!');
		}
		$validation[] =array('field'=>'note', 'label'=>'备注', 'rules'=>'max_length[120]');
		if($state == MOrder::STATE_HOLDING){
			$validation[] = array('field'=>'hold_reason', 'label'=>'问题种类', 'rules'=>'required|integer');
		}
		$this->load->library('Form_validation');
		$this->form_validation->set_rules($validation);
		if($this->form_validation->run()){
			$this->load->helper('populate');
			$order = populate($order, $this->form_validation->post());
			$order->update_time = $update_time;
			$order->state = $state;
			$order->hold_reason = ($state != MOrder::STATE_HOLDING) ? 0 : $order->hold_reason;
			$log = new stdClass();
			$log->type = MOrderLog::TYPE_EDIT;
			$log->note = "【{$title}】" .  $this->input->post('note');
			$log->admin_id = $this->_user->id;
			$log->admin = "{$this->_user->name}({$this->_user->account})";
			if($this->MOrder->save($order, $log, false)){
				model_success(l('edit_success'));
			}else{
				model_error(l('data_error'));
			}
		}
		$this->assign('thisControllerName', $title);
		$this->assign('order', $order);
		$this->assign('state', $state);
		$this->layout_modal();
	}
	
	/** 
	 * @todo
	 * 添加测试订单
	 * 测试用代码，上线后移出
	 *  */
	function add(){
		if(!$this->p->add){
			//权限不足
			show_error(l('user_has_nopower'));
		}
		$this->load->library('form_validation');
		$this->_validation = array(
			array('field'=>'site_id', 'label'=>'网站', 'rules'=>'required|integer')
			,array('field'=>'user_full_name', 'label'=>'姓名', 'rules'=>'required|max_length[64]')
			,array('field'=>'user_email', 'label'=>'Email', 'rules'=>'required|max_length[128]')
			,array('field'=>'user_phone', 'label'=>'电话', 'rules'=>'required|max_length[32]')
			,array('field'=>'user_ip', 'label'=>'IP', 'rules'=>'required|max_length[15]')
			,array('field'=>'user_agent', 'label'=>'浏览器', 'rules'=>'required|max_length[128]')
			//,array('field'=>'user_state', 'label'=>'', 'rules'=>'required|integer')
			,array('field'=>'refer_url', 'label'=>'来源url', 'rules'=>'required|max_length[255]')
			,array('field'=>'game_id', 'label'=>'游戏', 'rules'=>'required|integer')
			,array('field'=>'product_type', 'label'=>'商品类型', 'rules'=>'required|integer')
			,array('field'=>'currency', 'label'=>'货币', 'rules'=>'required|max_length[3]')
			,array('field'=>'amount', 'label'=>'金额', 'rules'=>'required|numberic')
			,array('field'=>'type', 'label'=>'订单类型', 'rules'=>'required|integer')
			,array('field'=>'note', 'label'=>'备注', 'rules'=>'max_length[255]')
			,array('field'=>'risk', 'label'=>'风险等级', 'rules'=>'required|integer')
			,array('field'=>'state', 'label'=>'订单状态', 'rules'=>'required|integer')
			,array('field'=>'hold_reason', 'label'=>'问题种类', 'rules'=>'required|integer')
			,array('field'=>'payment_method', 'label'=>'支付方式', 'rules'=>'required|integer')
			,array('field'=>'payment_state', 'label'=>'支付状态', 'rules'=>'required|integer')
			,array('field'=>'payment_time', 'label'=>'支付时间', 'rules'=>'required|strtotime|integer')
			,array('field'=>'delivery_state', 'label'=>'发货状态', 'rules'=>'required|integer')
			,array('field'=>'delivery_time', 'label'=>'发货时间', 'rules'=>'required|strtotime|integer')
		);
		$this->load->model('MCurrency');
		$this->load->model('MPaymentMethod');
		$this->load->model('MOrderProduct');
		$this->load->model('MOrderIndex');
		$this->form_validation->set_rules($this->_validation);
		$this->load->helper('populate');
		$obj = $this->MOrder->createVo();
		if($this->form_validation->run()==TRUE){
			$newObj = populate($obj, $this->form_validation->post());
			$newObj->no = 'P'.date('YmdHis', time()).rand(10000, 99999);
			$newObj->site_name = $this->sites[$newObj->site_id];
			$newObj->game_name = $this->games[$newObj->game_id];
			$json_arr = Array();
			if(array_key_exists('add_jsons', $_POST) && ($add_jsons=$_POST['add_jsons'])){
				foreach ($add_jsons as $v){
					$json_arr[] = json_decode($v);
				}
			}
			if(!$json_arr){
				error('请添加商品');
			}else{
				$log = Array('type'=>MOrderLog::TYPE_NOTE, 'note'=>'测试生成订单', 'admin_id'=>$this->_user->id, 'admin'=>$this->_user->name.'('.$this->_user->account.')');
				if($this->MOrder->save($newObj, null)){
					$this->MOrderProduct->updateCart($newObj, Array(), $json_arr, (Object)$log);
					$this->load->model('MOrderAttributeDef');
					$this->load->model('MOrderAttribute');
					$type = $this->MOrderAttributeDef->getAll(Array('type_id'=>$newObj->product_type));
					$this->MOrderIndex->save((Object)Array('order_id'=>$newObj->id, 'table_name'=>'orders', 'col_name'=>'no', 'col_value'=>$newObj->no));
					$this->MOrderIndex->save((Object)Array('order_id'=>$newObj->id, 'table_name'=>'orders', 'col_name'=>'user_email', 'col_value'=>$newObj->user_email));
					$this->MOrderIndex->save((Object)Array('order_id'=>$newObj->id, 'table_name'=>'orders', 'col_name'=>'user_phone', 'col_value'=>$newObj->user_phone));
					$this->MOrderIndex->save((Object)Array('order_id'=>$newObj->id, 'table_name'=>'orders', 'col_name'=>'user_ip', 'col_value'=>$newObj->user_ip));
					foreach ($type as $v){
						$tmpValue = '';
						if($v->data_config == 2 || $v->data_config == 3){
							$tmpValue = time();
						}else if($v->data_config == 4){
							$tmpValue = rand(121, 14324);
						}else if($v->data_config == 5){
							$tmpValue = rand(121, 14324).'.'.rand(0,9).rand(0,9);
						}else{
							$tmpValue = $this->_createStr(rand(10, 64));
						}
						$this->MOrderAttribute->add((Object)Array('order_id'=>$newObj->id, 'name'=>$v->name, 'code'=>$v->code, 'value'=>$tmpValue));
						if($v->index_flg){
							$this->MOrderIndex->save((Object)Array('order_id'=>$newObj->id, 'table_name'=>'order_attributes', 'col_name'=>$v->code, 'col_value'=>$tmpValue));
						}
					}
					success(l('add_success'));
				}else{
					error(l('data_fail'));
				}
			}
		}
		$paymentMethods = parse_options($this->MPaymentMethod->getAll(array('state'=>MPaymentMethod::STATE_ENABLE)));
		$currencies = parse_options($this->MCurrency->getAll(), 'code');
		$names = Array('白玉芬', '仓春莲', '仓红', '陈超云', '陈高', '陈国祥', '陈宏柳', '陈金娣', '陈丽丽', '陈丽丽', '陈平', '陈向东', '陈晓冬', '陈小荣', '陈秀芬', '陈艳华', '陈兆国', '成秀山', '仇腊梅', '戴金辉');
		$obj->user_full_name = $names[rand(0, count($names)-1)];
		$mail_tails = Array('@qq.com', '@163.com', '@gmail.com', '@outlook.com', '@sina.com', '@sohu.com');
		$obj->user_email .= $this->_createStr(rand(5, 20));
		$obj->user_email .= $mail_tails[rand(0, count($mail_tails)-1)];
		$obj->user_phone = rand(130, 139);
		for($i=0;$i<8;$i++){
			$obj->user_phone .= rand(0, 9);
		}
		$obj->state = MOrder::STATE_OPEN;
		$obj->amount = rand(10, 100);
		
		$this->assign('currencies', $currencies);
		
		$this->assign('paymentMethods', $paymentMethods);
		$obj->user_ip = $this->input->ip_address();
		$obj->user_agent = $this->input->user_agent();
		$obj->refer_url = site_url($this->_thisModule.$this->_thisController.'/'.$this->_thisMethod);
		$obj->payment_time = 0;
		$obj->delivery_time = 0;
		
		$this->assign('obj', $obj);
		$this->layout();
	}
	
	private function _createStr($len){
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$rtn = '';
		for($i=0;$i<$len;$i++){
			if($i==0){
				$rtn .= $chars[rand(0, strlen($chars)-10)];
			}else{
				$rtn .= $chars[rand(0, strlen($chars)-1)];
			}
		}
		return $rtn;
	}

 }
