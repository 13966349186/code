<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('order_state')){
	/**
	 * 根据支付和发货状态，计算订单状态
	 * @param object $order 订单信息
	 * @return int
	 */
	function calc_order_state($order){
		switch ($order->payment_state) {
			case MOrder::PAY_STATE_PAID :
			case MOrder::PAY_STATE_PART:
				if ($order->delivery_state == MOrder::DELEVERY_STATE_DELEVERED) {
					$state = MOrder::STATE_CLOSED;
				} else {
					$state = MOrder::STATE_OPEN;
				}
				break;
			case MOrder::PAY_STATE_REFUNDED :
				$state = MOrder::STATE_CLOSED;
				break;
			case MOrder::PAY_STATE_REVERSED:
				$state = MOrder::STATE_HOLDING;
				$order->hold_reason = '200';
				break;
			default:
				$state = $order->state;
		}
	return $state;
	}
}