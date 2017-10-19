<?php
/**
 * Paypal支付方式操作类
 * @author lifw
 */
Class IOSS_Paypal {
	const URL = 'https://www.paypal.com/cgi-bin/webscr';
	const SANDBOX_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	const CODE = 'paypal';
	
	public $sandbox = FALSE;
	public $cmd;             //“立即购买”方式
	public $business;       //PayPal账户上的电子邮件地址或者是PayPal ID
	public $item_name;    //物品名称或者整个购物车的名称
	public $custom;         //自定义字段，IPN会返回
	public $invoice;         //系统订单号
	public $amount;        //付款金额
	public $currency_code;   //付款币种
	public $notify_url;      //IPN通知URL
	public $charset;         //提交信息字符集
	public $no_shipping;  //是否需要邮寄地址， 0 选填， 1 不需要，2 必填
	public $image_url;      //paypal付款页面展示logo (150x50)
	public $cpp_logo_image;
	public $cpp_header_image;
	public $cbt;              //PayPal付款成功页面，设置 Return to Merchant 按钮的文本信息
	public $return;          //付款成功返回的url
	public $cancel_return;  //取消付款返回的url

	public function __construct(){
		$this->cmd =  '_xclick';    //“立即购买”方式
		$this->charset =  'UTF-8';
		$this->no_shipping = '0';
		$this->notify_url = IOSS_Conf::getConfig('papal_notify_url');
	}
	/**
	 * 修改paypal设置
	 * @param array $cfg
	 */
	public function setConfig($cfg){
		foreach ($cfg as $k=>$v){
			if(property_exists($this, $k)){
				$this->{$k} = $v;
			}
		}
	}
	
	/**
	 * 生成表单
	 * @param string $name
	 * @return string
	 */
	public function form($name){
		$action = $this->sandbox?self::SANDBOX_URL : self::URL;
		$html = "<form action='$action' method='post' name='$name' id='$name' >";
		foreach ($this as $k=>$v){
			if(is_string($v)  &&  strlen($v) > 0 || is_numeric($v) ){
				$html .= '<input type="hidden" name="'.htmlspecialchars($k).'" value="'.htmlspecialchars($v).'">' . "\n";
			}
		}
		$html .= '</html>';
		return $html;
	}
	
	/**
	 * 创建IOSS_Payal对象实例
	 * @param IOSS_Order $order
	 */
	public static function create(IOSS_Order $order){
		if($order->payment_method != IOSS_PaymentMethod::PAYPAL){
			return NULL;
		}
		$p_method = IOSS_PaymentMethod::getById($order->site_id, $order->payment_method);
		$paypal = new IOSS_Paypal();
		$paypal->sandbox = $p_method->getConfig('sandbox') !== null;
		$paypal->business = $p_method->account;
		$paypal->item_name = 'Order # ' . $order->no;
		$paypal->custom = $order->id;
		$paypal->invoice = $order->no;
		$paypal->amount = $order->amount;
		$paypal->currency_code = $order->currency;
		return $paypal;
	}
	
	/**
	 * @deprecated 已作废函数，保持兼容
	 * 创建paypal支付表单
	 * @param string $name 表单名和ID
	 * @param IOSS_Order $order
	 * @param unknown $params
	 * @return boolean|string
	 */
	public static function createForm($name, IOSS_Order $order, $params=Array()){
		$payment_method = IOSS_PaymentMethod::getById($order->site_id, $order->payment_method);
		if($payment_method->code != self::CODE){
			return false;
		}
		$action = $payment_method->getConfig('sandbox')? self::SANDBOX_URL : $action = self::URL;
		$inputs = Array(
			'cmd'=>'_xclick'                                 //“立即购买”方式
			,'business'=>$payment_method->account        //PayPal账户上的电子邮件地址或者是PayPal ID
			,'item_name'=>'Order No.  ' . $order->no              //物品名称或者整个购物车的名称
			,'custom'=>$order->id                      //自定义字段，系统订单id
			,'invoice'=>$order->no                     //系统订单号
			,'amount'=>$order->amount             //付款金额
			,'currency_code'=>$order->currency       //付款币种
			,'notify_url'=>IOSS_Conf::getConfig('papal_notify_url')    //IPN通知URL
			,'charset'=>'UTF-8'                         //提交信息字符集
			,'no_shipping'=>'0'                        //是否需要邮寄地址， 0 选填， 1 不需要，2 必填
			,'image_url	'=>''                             //paypal付款页面展示logo (150x50)
			,'cpp_logo_image'=>''                    //paypal付款页面展示logo (190x60)
			,'cpp_header_image'=>''                //paypal付款页面展示页头图片(750x90)
			,'return'=>''                                  //付款成功返回url
			,'cbt'=>''                                       //PayPal付款成功页面，设置 Return to Merchant 按钮的文本信息
			,'cancel_return'=>''                        //取消付款返回url
		);
		$inputs = array_merge($inputs, $params);
		$html = "<form action='$action' method='post' name='$name' id='$name' >";
		foreach ($inputs as $k=>$v){
			if(strlen($v) == 0){
				continue;
			}
			$html .= '<input type="hidden" name="'.htmlspecialchars($k).'" value="'.htmlspecialchars($v).'">';
		}
		$html .= '</form>';
		return $html;
	}
	
}

