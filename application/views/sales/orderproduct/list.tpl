<?php
$CI = &get_instance();
$modelName = 'MOrderProduct';
$CI->load->model($modelName);
$lst = $CI->{$modelName}->getListByOrderId($order->id);

$deliveryStatesCls = $this->config->item('delivery_states_css');
$deliveryStates = $this->MOrder->getDeliveryState();
?>
<?php $modalId = 'modalId'.time().rand(1, 10000);?>
<form id="<?=$modalId?>" action="<?=site_url('sales/orderproduct/edit/'.$order->id)?>" style="display:none;"></form><div class="portlet grey-cascade box">
	<div class="portlet-title">
		<div class="caption"><i class="icon-basket"></i>订单商品</div>
		<div class="tools">
		<?php $customer_p = UserPower::getPermisionInfo('sales/orderproduct'); ?>
		<?php if($customer_p->edit && $order->state != MOrder::STATE_CLOSED){?>
			<span class="about_edit" style="cursor:pointer;color:white;" id="edit_order_products" data-original-title="" title=" "><i class="fa fa-edit"></i> 编辑</span>
		<?php }?>
		</div>
	</div>
	<div class="portlet-body">
		<div class="">
			<table class="table table-hover table-bordered table-striped">
				<thead>
					<tr>
						<th width="30%">游戏-目录</th>
						<th width="17%">商品名</th>
						<th width="8%">单价</th>
						<th width="10%">数量</th>
						<th width="8%">总价</th>
						<th width="9%"><span style="width:67px;">状态</span></th>
						<th width="9%"><div style="width:50px;"></div></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($lst as $v){?>
					<?php $deliveryPower = UserPower::getPermisionInfo('/sales/delivery'); ?>
					<tr>
						<td><?=htmlspecialchars($v->game_name.'-'.$v->category_name.' ('.$v->type_name.')')?></td>
						<td><?=htmlspecialchars($v->name)?></td>
						<td><?=$this->currency->format($v->price, DEFAULT_CURRENCY)?></td>
						<td><?=htmlspecialchars($v->num)?></td>
						<td><?=$this->currency->format($v->price*$v->num, DEFAULT_CURRENCY)?></td>
						<td><span class="<?=element($v->delivery_state, $deliveryStatesCls, '')?>"><?=element($v->delivery_state, $deliveryStates, '--')?></span></td>
						<td>
					<?php if($deliveryPower->edit && $order->state == MOrder::STATE_OPEN && ($order->payment_state == MOrder::PAY_STATE_PAID || $order->payment_state == MOrder::PAY_STATE_PART)){?>
							<a href="javascript:void(0);" class="black about_edit" onclick="ioss_url_modal('<?=site_url('/sales/delivery/edit/'.$v->id)?>', {big:true});">
							<i class="fa fa-check"></i> 详细</a>
					<?php }?>
						</td>
					</tr>
				<?php }?>
				</tbody>
			</table>
		</div>
		<?=$this->load->view('partials/message_modal.tpl')?>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("#<?=$modalId?>").ajaxForm({
		target:$("#<?=$modalId?>").parent(),
		type:'get'
	});
	$("#edit_order_products").click(function(){
		$("#<?=$modalId?>").submit();
	});
});
</script>
