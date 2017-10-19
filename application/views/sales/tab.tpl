<ul class="nav nav-tabs nav-tabs-lg">
<?php $ctrls = Array('sales/order/view/{0}'=>'订单详细', 'sales/orderpayment/index/{0}'=>'支付明细', 'sales/orderverify/view/{0}'=>'订单验证', 'sales/orderrelative/index/{0}'=>'关联订单');?>
<?php foreach ($ctrls as $k=>$v){?>
	<?php $tab_p = UserPower::getPermisionInfo(substr($k, 0, strpos($k, '/', strpos($k, '/')+1)));?>
	<?php if(!$tab_p->read){continue;}?>
	<?php if(strpos($k, $thisModule.$thisController.'/')!==false){?>
	<li class="active"><a href="javascript:void(0);"><?=$v?></a></li>
	<?php }else{?>
	<li><a href="<?=site_url( str_replace('{0}', $order->id, $k ))?>"><?=$v?></a></li>
	<?php }?>
<?php }?>
</ul>
