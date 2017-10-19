<div class="portlet-title">
	<div class="caption">
		<i class="fa fa-shopping-cart"></i>订单
		<span style="margin:0px 15px;"><?=htmlspecialchars($order->no)?></span>
	</div>
	<div class="actions">
	<?php 
	$edit_p = UserPower::getPermisionInfo($this->_thisModule.'order'); 
	$pay_p = UserPower::getPermisionInfo($this->_thisModule.'orderpayment'); 
	?>
	<?php if($pay_p->edit && ($order->state == MOrder::STATE_OPEN || $order->state == MOrder::STATE_HOLDING) && ($order->payment_state == MOrder::PAY_STATE_UNPAIED || $order->payment_state == MOrder::PAY_STATE_PEDING || $order->payment_state == MOrder::PAY_STATE_PART)){?> 
		<button type="button" class="btn green" onclick="ioss_url_modal('<?=site_url($thisModule.'orderpayment/pay/'.$order->id)?>');">付款</button>
	<?php }?> 
	<?php if($pay_p->edit && ($order->state == MOrder::STATE_OPEN || $order->state == MOrder::STATE_HOLDING) && ($order->payment_state == MOrder::PAY_STATE_PAID || $order->payment_state == MOrder::PAY_STATE_PART || $order->payment_state == MOrder::PAY_STATE_REVERSED)){?>
		<button type="button" class="btn btn-success" onclick="ioss_url_modal('<?=site_url($thisModule.'orderpayment/refund/'.$order->id)?>');">退款</button>
	<?php }?>
	<?php if($edit_p->edit && ($order->state == MOrder::STATE_OPEN )){?>
		<button type="button" class="btn btn-warning" onclick="ioss_url_modal('<?=site_url($thisModule.'order/state/'.$order->id .'/' . MOrder::STATE_HOLDING. '/' . $order->update_time)?>');">设置问题单</button>
	<?php }?>
	<?php if($edit_p->edit && $order->state == MOrder::STATE_HOLDING ){?>
		<button type="button" class="btn btn-success" onclick="ioss_url_modal('<?=site_url($thisModule.'order/state/'.$order->id .'/' . MOrder::STATE_OPEN. '/' . $order->update_time)?>');">解除问题单</button>
	<?php }?>
	<?php if($edit_p->edit && $order->state == MOrder::STATE_CLOSED){?> 
		<button type="button" class="btn btn-success" onclick="ioss_url_modal('<?=site_url($thisModule.'order/state/'.$order->id .'/' . MOrder::STATE_OPEN. '/' . $order->update_time)?>');">重新打开</button>
	<?php }?> 
	<?php if($edit_p->edit && ($order->state != MOrder::STATE_CLOSED)){?> 
		<button type="button" class="btn default" onclick="ioss_url_modal('<?=site_url($thisModule.'order/state/'.$order->id .'/' . MOrder::STATE_CLOSED. '/' . $order->update_time)?>');">关闭订单</button>
	<?php }?> 
	</div>
</div>
