<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_BTN_GROUP'=>array('url'=>site_url('sales/order'),  'class'=>'btn default', 'name'=>'返回列表', 'i'=>'<i class="m-icon-swapleft"></i>')))?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<?php
$paymentStatesCls = $this->config->item('pay_states_css');
$deliveryStatesCls = $this->config->item('delivery_states_css');
$riskStatesCls = $this->config->item('risk_states_css');
$orderStatesCls = $this->config->item('order_states_css');
$holdTypes = $this->config->item('hold_reason');
$deliveryPermission = UserPower::getPermisionInfo($thisModule.'delivery');
$orderLabelCls = $this->config->item('order_states_label_css');

?>
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet">
			<?php $this->load->view($thisModule.'/title.tpl', Array('order'=>$order))?> 
			<div class="tabbable">
				<?php $this->load->view($thisModule.'/tab.tpl', Array('order_id'=>$order->id))?>
				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<!-- 订单信息和顾客信息开始 -->
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="portlet box green">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-cogs"></i>订单信息
										</div>
										<div class="actions">
										<?php if($p->edit){?>
											<a class="btn btn-default btn-sm"  href="javascript:ioss_url_modal('<?=site_url($thisModule. $thisController. '/note/'.$order->id)?>');" data-original-title="" ><i class="fa  fa-edit"></i> 添加备注</a>
										<?php }?>
										</div>
									</div>
									<div class="portlet-body">
										<div class="row static-info">
											<div class="col-xs-3 name">订单号:</div>
											<div class="col-xs-9 value">
												<span class="<?=element($order->state, $orderStatesCls, '')?>"><?=htmlspecialchars($order->no)?></span>
											</div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">订单状态:</div>
											<div class="col-xs-9 value">
												<span class="<?=element($order->state, $orderLabelCls, '')?>"><?=element($order->state, $orderStates, '--')?></span>
												<?=MOrder::STATE_HOLDING == $order->state?   '&nbsp;（' .element($order->hold_reason , $holdTypes, '--') . '）' :''?>
											</div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">网站:</div>
											<div class="col-xs-9 value"><?=$order->site_name?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">创建时间:</div>
											<div class="col-xs-9 value">
												<span><?=date('Y-m-d H:i:s', $order->create_time)?> </span>
											</div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">游戏:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($order->game_name.' ('. $this->types[$order->product_type].')')?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">订单金额:</div>
											<div class="col-xs-9 value"><?=$this->currency->format($order->amount, $order->currency)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">支付方式:</div>
											<div class="col-xs-9 value"><?=$this->paymentmethod[$order->payment_method]?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">支付状态:</div>
											<div class="col-xs-9 value"><span class="<?=element($order->payment_state, $paymentStatesCls, '')?>"><?=element($order->payment_state, $paymentStates, '--')?></span></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">发货状态:</div>
											<div class="col-xs-9 value"><span class="<?=element($order->delivery_state, $deliveryStatesCls, '--')?>"><?=element($order->delivery_state, $deliveryStates, '--')?></span></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="portlet green box">
									<div class="portlet-title">
										<div class="caption"><i class="fa fa-cogs"></i> 顾客信息</div>
										<div class="actions">
										<?php if($p->edit && $order->state != MOrder::STATE_CLOSED){?>
											<a class="btn btn-default btn-sm"  href="javascript:ioss_url_modal('<?=site_url($thisModule. $thisController. '/edit/'.$order->id)?>');" data-original-title="" ><i class="fa  fa-edit"></i> 编辑</a>
										<?php }?>
										</div>
									</div>
									<div class="portlet-body">
										<div class="row static-info">
											<div class="col-xs-3 name">类型:</div>
											<div class="col-xs-9 value" ><?=($order->user_id == 0)?'非注册用户':'注册用户' ?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">姓名:</div>
											<div class="col-xs-9 value omit" title="<?=htmlspecialchars($order->user_full_name)?>"><?=htmlspecialchars($order->user_full_name)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">Email:</div>
											<div class="col-xs-9 value omit" title="<?=htmlspecialchars($order->user_email)?>"><?=htmlspecialchars($order->user_email)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">电话:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($order->user_phone)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">IP:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($order->user_ip)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">浏览器:</div>
											<div class="col-xs-9 value omit" title="<?=htmlspecialchars($order->user_agent)?>"><?=htmlspecialchars($order->user_agent)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">来源url:</div>
											<div class="col-xs-9 value omit" title="<?=htmlspecialchars($order->refer_url)?>"><?=htmlspecialchars($order->refer_url)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">订单风险:</div>
											<div class="col-xs-9 value">
												<?=element($order->risk, $riskStates, '--')?>
												<i class="<?=element($order->risk, $riskStatesCls, '')?>"></i>
											</div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">&nbsp;</div>
											<div class="col-xs-9 value">&nbsp;</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- 订单信息和顾客信息结束 -->
						<!-- 订单商品列表开始 -->
						<div>
						<div class="portlet grey-cascade box">
							<div class="portlet-title">
								<div class="caption"><i class="icon-basket"></i>订单商品</div>
								<div class="action"></div>
							</div>
							<div class="portlet-body">
								<div class="">
									<table class="table table-hover table-bordered table-striped">
										<thead>
											<tr>
												<th width="25%">游戏/目录</th>
												<th width="18%">类型</th>
												<th >商品名</th>
												<th width="8%">单价</th>
												<th width="8%">数量</th>
												<th width="8%">状态</th>
												<th width="7%"></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach ($products as $v){?>
											<tr>
												<td><?= ($c = $this->categories->getById($v->category_id)) ?  $this->categories->displayPath($c->id, $this->games[$c->game_id]) : $v->category_name?></td>
												<td><?=htmlspecialchars( $this->types[$v->type])?></td>
												<td><?=htmlspecialchars($v->name)?></td>
												<td><?=$this->currency->format($v->price, DEFAULT_CURRENCY)?></td>
												<td><?=htmlspecialchars($v->num)?></td>
												<td><span class="<?=element($v->delivery_state, $deliveryStatesCls, '')?>"><?=element($v->delivery_state, $deliveryStates, '--')?></span></td>
												<td>
													<a href="javascript:void(0);" class="btn about_edit <?=$deliveryPermission->read?'':'disabled' ?>" onclick="ioss_url_modal('<?=site_url('/sales/delivery/view/'.$v->id)?>', {big:true});">
													<i class="fa fa-truck"></i> 详细</a>
												</td>
											</tr>
										<?php }?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						</div>
						<!-- 订单商品列表结束 -->
						<!-- 订单附加属性和金额统计 -->
						<div class="row">
							<div class="col-md-6">
								<div class="well">
									<table width="100%" border=0 class="static-info">
									<?php foreach ($attrs as $v){?>
										<tr style="line-height:24px;">
											<td class="name" style="width:45%;text-align:right;"><?=htmlspecialchars($v->name)?></td>
											<td class="name">:</td>
											<td style="word-break:break-all;padding-left:20px;"><?=htmlspecialchars($v->value)?></td>
										</tr>
									<?php }?>
									</table>
								</div>
							</div>
							<div class="col-md-6">
								<div class="well">
									<div class="row static-info align-reverse">
										<div class="col-xs-9 name">
											付款合计:
										</div>
										<div class="col-xs-3">
											<?=$this->currency->format($payments[MOrderPayment::TYPE_PAY], $order->currency)?>
										</div>
									</div>
									<div class="row static-info align-reverse">
										<div class="col-xs-9 name">
											退款合计:
										</div>
										<div class="col-xs-3">
											<?=$this->currency->format($payments[MOrderPayment::TYPE_REFUND], $order->currency)?>
										</div>
									</div>
									<div class="row static-info align-reverse">
										<div class="col-xs-9 name">
											CB合计:
										</div>
										<div class="col-xs-3">
											<?=$this->currency->format($payments[MOrderPayment::TYPE_CHARGEBACK], $order->currency)?>
										</div>
									</div>
									<div class="row static-info align-reverse">
										<div class="col-xs-9 name">
											 <b>总计:</b>
										</div>
										<div class="col-xs-3">
											 <b><?=$this->currency->format($payments['total'], $order->currency)?></b>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- 订单附加属性和金额统计结束 -->
						<!-- 订单操作记录开始 -->
						<div class="portlet">
							<?php $types = $this->MOrderLog->getType(); ?>
							<div class="portlet-title" id="log_type_tit">
								<div class="caption">
									<i class="fa fa-history"></i>订单操作记录
									<label class="radio-inline"><input type="radio" name="log_type" checked="checked" value="all"> 全部</label>
								<?php foreach ($types as $k=>$v){?> 
									<label class="radio-inline"><input type="radio" name="log_type" value="<?=$k?>"> <?=htmlspecialchars($v)?></label>
								<?php }?> 
								</div>
							</div>
							<script type="text/javascript">
							$(document).ready(function(){
								$("[name=log_type]").change(function(){
									$("[data-log-type]").hide();
									if($(this).val() == 'all'){
										$("[data-log-type]").show();
									}else{
										$("[data-log-type="+$(this).val()+"]").show();
									}
									$(document).scrollTop($("#log_type_tit")[0].offsetTop);
								});
							});
							</script>
							<div class="portlet-body" id="log_type_body">
								<!-- 时间轴 -->
								<div class="timeline">
								<?php $typePics = $this->config->item('order_log_type_css');?>
								<?php foreach ($logs as $v){?>
									<div class="timeline-item" data-log-type="<?=$v->type?>">
										<div class="timeline-badge timeline_pic">
											<img class="timeline-badge-userpic" title="<?=element($v->type, $types, '')?>" src="<?=image_url('/static/assets/admin/img/'.element($v->type, $typePics, '').'.png')?>">
										</div>
										<div class="timeline-body timeline_mg">
											<div class="timeline-body-arrow">
											</div>
											<div class="timeline-body-head">
												<div class="timeline-body-head-caption">
													<a href="javascript:;" class="timeline-body-title font-blue-madison"><?=htmlspecialchars($v->admin)?></a>
													<span class="timeline-body-time font-grey-cascade"><?=date('Y-m-d H:i:s', $v->create_time)?></span>
												</div>
											</div>
											<div class="timeline-body-content" style="word-break:break-all;">
												<span class="font-grey-cascade">
													<!-- <span class="<?=element($v->state, $orderStatesCls, '')?>"><?=element($v->state, $orderStates, '--')?></span> -->
													<span class="<?=element($v->payment_state, $paymentStatesCls, '')?>"><?=element($v->payment_state, $paymentStates, '--')?></span>
													<span class="<?=element($v->delivery_state, $deliveryStatesCls, '')?>"><?=element($v->delivery_state, $deliveryStates, '--')?></span>
													<?=htmlspecialchars($v->note)?>
												</span>
											</div>
										</div>
									</div>
								<?php }?>
								</div>
							</div>
						</div>
						<!-- 订单操作记录结束 -->
					</div>
				</div>
			</div>
		</div>
		<!-- END SAMPLE FORM PORTLET-->
	</div>
</div>
<!-- END PAGE CONTENT-->
