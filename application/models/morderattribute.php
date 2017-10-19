<?php
/**
 * 后台订单商品附加信息表操作模型
 * @author lifw
 */
class MOrderAttribute extends MY_Model {
	protected $table = 'order_attributes';
    function __construct() {
        parent::__construct();
    }
}
