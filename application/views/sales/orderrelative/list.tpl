<?php
	$paymentStatesCls = $this->config->item('pay_states_css');
	$orderStatesCls = $this->config->item('order_states_css');
	$deliveryStatesCls = $this->config->item('delivery_states_css');
	$riskStatesCls = $this->config->item('risk_states_css');
?>
<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_BTN_GROUP'=>array('url'=>site_url('sales/order'),  'class'=>'btn default', 'name'=>'返回列表', 'i'=>'<i class="m-icon-swapleft"></i>')) )?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript">
$(document).ready(function(){
	$("[name='type']").change(function(){
		var url = "<?=site_url($thisModule.$thisController).'/index/'.$order->id?>";
		window.location.href = url+'/'+$(this).val();
	});
});
</script>
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet">
			<?php $this->load->view($thisModule.'/title.tpl', Array('order'=>$order))?> 
			<div class="tabbable">
				<?php $this->load->view($thisModule.'/tab.tpl', Array('order'=>$order))?> 
				<div class="tab-content">
					<div class="tab-pane active" id="tab_4">
						<div class="form-group">
							<label class="col-md-1 control-label">关联项</label>
							<div class="input-group">
								<div class="icheck-inline">
									<label class="radio-inline">
									<input type="radio" name="type" value="0"<?=array_key_exists($stype, $stypes)?'':' checked=""'?>>全部</label>
								<?php foreach ($stypes as $k=>$v){?> 
									<label class="radio-inline">
									<input type="radio" name="type" value="<?=htmlspecialchars($k)?>"<?=($k==$stype)?' checked="checked"':''?>><?=htmlspecialchars($v)?></label>
								<?php }?> 
								</div>
							</div>
						</div>
						<!-- table4表格 -->
						<div class="row">
							<div class="col-xs-12">
								<table class="table table-hover table-bordered">
									<thead>
										<tr>
											<th width="16%">订单号</th>
											<th width="10%">网站</th>
											<th >邮箱</th>
											<th width="15%">游戏</th>
											<th width="11%">创建时间</th>
											<th width="8%">金额</th>
											<th width="8%">支付方式</th>
											<th width="6%"><div style="width:65px;">支付状态</div></th>
											<th width="6%"><div style="width:65px;">发货状态</div></th>
										</tr>
									</thead>
									<tbody>
									<?php foreach ($lst as $v){?> 
										<tr <?=($v->id == $order->id)?'class="warning"':'' ?> >
											<td>
											<a href="<?=site_url($thisModule.'order/view/'.$v->id)?>"><?=htmlspecialchars($v->no)?></a>
											<i style="cursor:default;" class="<?=element($v->risk, $riskStatesCls, '')?>" title="<?=element($v->risk, $riskStates, '--')?>"></i>
											</td>
											<td><?=htmlspecialchars($v->site_name)?></td>
											<td><?=htmlspecialchars($v->user_email)?></td>
											<td><?=htmlspecialchars($v->game_name.' ('.$this->types[$v->product_type].')')?></td>
											<td title="<?=date('Y-m-d H:i:s', $v->create_time)?>"><?=date('Y-m-d H:i', $v->create_time)?></td>
											<td><?=$this->currency->format($v->amount, $v->currency)?></td>
											<td><?=htmlspecialchars(element($v->payment_method, $paymentMethods, '未知['.$v->payment_method.']'))?></td>
											<td><span class="<?=element($v->payment_state, $paymentStatesCls, '')?>"><?=element($v->payment_state, $paymentStates, '--')?></span></td>
											<td><span class="<?=element($v->delivery_state, $deliveryStatesCls, '')?>"><?=element($v->delivery_state, $deliveryStates, '--')?></span></td>
										</tr>
									<?php }?> 
									</tbody>
								</table>
							</div>
						 	<div class="col-xs-12  text-right">
						 		<?=$pagination?>
						 	</div>
						 	<div class="col-xs-12"><div class="well"><i>* 黄色为当前订单</i></div></div>
						 	
						</div>
						<!-- 订单支付记录结束 -->
					</div>
				</div>
			</div>
		</div>
		<!-- END SAMPLE FORM PORTLET-->
	</div>
</div>
<!-- END PAGE CONTENT-->
