<?php
/**
 * 后台订单商品日志表操作模型
 * @author lifw
 */
class MOrderLog extends MY_Model {
	/** 仅备注 */
	const TYPE_NOTE = 0;
	/** 支付 */
	const TYPE_PAY = 1;
	/** 验证 */
	const TYPE_VERIFY = 2;
	/** 发货 */
	const TYPE_DELEVERY = 3;
	/** 编辑 */
	const TYPE_EDIT = 4;
	protected $table = 'order_log';
	function __construct() {
		parent::__construct();
	}
	public function getType($type=''){
		$rtn = Array(self::TYPE_NOTE=>'备注', self::TYPE_PAY=>'支付', self::TYPE_VERIFY=>'验证', self::TYPE_DELEVERY=>'发货', self::TYPE_EDIT=>'编辑');
		if($type === ''){
			return $rtn;
		}
		return $rtn[$type];
	}
}
