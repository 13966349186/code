<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_BTN_GROUP'=>array('url'=>site_url('sales/order'),  'class'=>'btn default', 'name'=>'返回列表', 'i'=>'<i class="m-icon-swapleft"></i>')) )?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript" src="<?=image_url('static/js/ajaxfileupload.js')?>"></script>
<script type="text/javascript">
</script>
<?php
$paymentStatesCls = $this->config->item('pay_states_css');
$deliveryStatesCls = $this->config->item('delivery_states_css');
$riskStatesCls = $this->config->item('risk_states_css');
$orderLabelCls = $this->config->item('order_states_label_css');

$icons = array('<i class="fa fa-check-circle font-green"></i>','<i class="fa fa-question-circle font-yellow"></i>','<i class="fa fa-exclamation-circle font-red"></i>');
?>
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet">
			<?php $this->load->view($thisModule.'/title.tpl', Array('order'=>$order))?> 
			<div class="tabbable">
				<?php $this->load->view($thisModule.'/tab.tpl', Array('order_id'=>$order->id))?>
				<div class="tab-content">
					<div class="tab-pane active" id="tab_3">
						<!-- 订单信息和Paypal开始 -->
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="portlet box green">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-cogs"></i>订单信息
										</div>
									</div>
									<div class="portlet-body">
										<div class="row static-info">
											<div class="col-xs-3 name">订单号:</div>
											<div class="col-xs-9 value">
												<?=htmlspecialchars($order->no)?>
												<span class="<?=element($order->state, $orderLabelCls, '')?>"><?=element($order->state, $orderStates, '')?></span>
											</div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">网站:</div>
											<div class="col-xs-9 value"><?=$order->domain?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">创建时间:</div>
											<div class="col-xs-9 value"><span><?=date('Y-m-d H:i:s', $order->create_time)?></span></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">游戏:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($order->game_name)?>
											(<?=htmlspecialchars($order->product_type_name) ?>)</div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">订单金额:</div>
											<div class="col-xs-9 value"><?=$this->currency->format($order->amount, $order->currency)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">支付方式:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($order->payment_method_name)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">支付状态:</div>
											<div class="col-xs-9 value"><span class="<?=element($order->payment_state, $paymentStatesCls, '')?>"><?=element($order->payment_state, $paymentStates, '--')?></span></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">发货状态:</div>
											<div class="col-xs-9 value"><span class="<?=element($order->delivery_state, $deliveryStatesCls, '')?>"><?=element($order->delivery_state, $deliveryStates, '--')?></span></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">邮箱:</div>
											<div class="col-xs-9 value omit" title="<?=htmlspecialchars($order->user_email)?>"><?=htmlspecialchars($order->user_email)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">IP地址:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($order->user_ip)?> &nbsp;
											<a class="popovers" data-trigger="hover" data-container="body" data-placement="right" data-content="<?= $ip_info['country_name'] ?> / <?= $ip_info['region'] ?> / <?= $ip_info['city'] ?>" data-original-title="IP归属地">(所属国家)</a>
											<?php if($paypal->address_country_code) echo $ip_info['country_code'] == $paypal->address_country_code ? '<i class="fa fa-check-circle level_safe"></i>' : '<i class="fa fa-exclamation-circle danger_level"></i>';?>&nbsp;
											</div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">电话:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($order->user_phone)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">关联订单:</div>
											<div class="col-xs-9 value"><?=$count?>个&nbsp;<i class="<?=element($risk, $riskStatesCls, '')?>"></i>
											&nbsp;&nbsp;&nbsp;&nbsp;<a id='old_ioss_orders' href="#">(老系统订单查询)</a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="portlet green box">
									<div class="portlet-title">
										<div class="caption">
											<i class="fa fa-cogs"></i>Paypal
										</div>
									</div>
									<div class="portlet-body">
										<div class="row static-info">
											<div class="col-xs-3 name">姓名:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($paypal->last_name.' '.$paypal->first_name)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">账号:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($paypal->payer_email)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3  name">当前状态:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($paypal->payment_status)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">账号验证:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($paypal->payer_status)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">地址验证:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($paypal->address_status)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">国家:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($paypal->address_country)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">省/州:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($paypal->address_state)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">城市:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($paypal->address_city)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">街道地址:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($paypal->address_street)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">收件人:</div>
											<div class="col-xs-9 value"><?=htmlspecialchars($paypal->receiver_email)?></div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">身份证件:</div>
											<div class="col-xs-9 value">
											<?php if($idcard_flg){?>
												<a href="javascript:void(0);" onclick="ioss_url_modal('<?=site_url($thisModule.$thisController.'/list_idcard/'.$order->id)?>', {big:true});return false;">是</a> <?=$icons[0] ?>
											<?php }else{?>
												否 <?=$icons[1] ?>
											<?php }?>
											</div>
										</div>
										<div class="row static-info">
											<div class="col-xs-3 name">电话验证:</div>
											<div class="col-xs-9 value">
											<?php if($tel_flg){?>
												<a href="javascript:void(0);" onclick="ioss_url_modal('<?=site_url($thisModule.$thisController.'/list_tel/'.$order->id)?>', {big:true});return false;">是</a> <?=$icons[0] ?>
											<?php }else{?>
												否 <?=$icons[1] ?>
											<?php }?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- table3添加时间 -->
						<div class="">
							<table class="table table-hover table-bordered table-striped">
								<thead>
									<tr>
										<th width="10%"><div style="width:125px;">添加时间</div></th>
										<th width="10%"><div style="width:120px;">操作人员</div></th>
										<th width="20%">附加信息</th>
										<th>备注</th>
										<?php if($p->delete){?><th width="8%"><div style="width:60px;"></div></th><?php }?>
									</tr>
								</thead>
								<tbody>
								<?php foreach ($lst as $v){?>
									<tr>
										<td><?=date('Y-m-d H:i:s', $v->create_time)?></td>
										<td><?=htmlspecialchars($v->admin)?></td>
										<td>
										<?php if($v->verify_key == 2){
											$arr = explode('|', $v->verify_value);
											$idx = 1;
											$pre_url = $this->config->item('upload_show');
										?>
											证件附件：
											<?php foreach ($arr as $url){?>
												<?php if(strlen(trim($url)) < 1){continue;}?>
												<a href="<?=$pre_url.$url?>" target="_blank">附件<?=$idx++?></a>
											<?php }?>
										<?php }else{?>
											电话：
											<?=htmlspecialchars($v->verify_value)?>
										<?php }?>
										</td>
										<td style="word-break:break-all;"><?=htmlspecialchars($v->note)?></td>
										<?php if($p->delete){?>
										<td>
											<a href="javascript:void(delCfm('<?=site_url($thisModule.$thisController.'/delete/'.$v->id.'/'.$v->order_id.'/'.$v->update_time)?>'));" class="black about_edit">
											<i class="fa fa-trash-o"></i> 删除 </a>
										</td>
										<?php }?>
									</tr>
								<?php }?>
								</tbody>
							</table>
						</div>
						<!--验证信息表单开始-->
						<div class="portlet grey-cascade box">
							<div class="portlet-title">
								<div class="caption">订单验证</div>
							</div>
							<div class="portlet-body">
								<?=form_open()?> 
									<input type="hidden" name="update_time" value="<?=set_value('update_time', $order->update_time)?>" />
									<div class="form-group<?=form_error('verify_key')?' has-error':''?>">
										<div class="form-inline" role="form">
											<div class="form-group<?=form_error('risk')?' has-error':''?>" style="vertical-align:top;">
												<select class="form-control input-medium<?=($p->edit && $order->state!=MOrder::STATE_CLOSED) ? '' : ' disabled'?>" id="risk" name="risk">
												<?php foreach ($riskStates as $k=>$v){?>
													<option value="<?=htmlspecialchars($k)?>"<?=set_select('risk', $k, $order->risk.''===''.$k)?>><?=htmlspecialchars($v)?></option>
												<?php }?>
												</select>
												<?=form_error('risk', '<p class="help-block">', '</p>')?>
											</div>
											<button type="submit" class="btn blue<?=($p->edit) ? '' : ' disabled'?>">提交</button>
										<?php if($p->add){?>
											<span class="btn blue pull-right" onclick="ioss_url_modal('<?=site_url($thisModule.$thisController.'/verify_idcard/'.$order->id)?>');return false;">上传证件</span>
											<button class="btn blue pull-right" onclick="ioss_url_modal('<?=site_url($thisModule.$thisController.'/verify_tel/'.$order->id)?>');return false;">电话验证</button>
										<?php }?>
										</div>
									</div>
									<div class="form-group<?=form_error('note')?' has-error':''?>">
										<textarea class="form-control" rows="3" placeholder="备注" name="note"><?=set_value('note')?></textarea>
										<?=form_error('note', '<p class="help-block">', '</p>')?>
									</div>
								<?=form_close()?> 
							</div>
						</div>
						<!--验证信息表单结束-->
						<!--验证记录 -->
						<div class="portlet">
							<div class="portlet-title">
								<div class="caption">
									验证记录
								</div>
							</div>
							<div class="portlet-body">
								<div class="">
									<table class="table table-hover table-bordered table-striped">
										<thead>
											<tr>
												<th width="10%"><div style="width:125px;">日期</div></th>
												<th width="10%"><div style="width:120px;">用户</div></th>
												<th width="10%"><div style="width:90px;">风险等级</div></th>
												<th>备注</th>
											</tr>
										</thead>
										<tbody>
										<?php foreach ($logs as $v){?>
											<tr>
												<td><?=date('Y-m-d H:i:s', $v->create_time)?></td>
												<td><?=htmlspecialchars($v->admin)?></td>
												<td>
													<i class="<?=element($v->risk, $riskStatesCls, '')?>"></i>
													<?=element($v->risk, $riskStates, '')?>
												</td>
												<td style="word-break:break-all;"><?=htmlspecialchars($v->note)?></td>
											</tr>
										<?php }?>
										</tbody>
									</table>
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
<script>
$(document).ready(function(){
	var $form = $('<form action="http://192.168.199.248:801/information/orderhistory" target="_BLANK" method="post" ></form>');
	$form.append('<input name="email" value="<?=htmlspecialchars($order->user_email)?>">');
	$form.append('<input name="phone" value="<?=htmlspecialchars($order->user_phone)?>">');
	$form.append('<input name="ip" value="<?=htmlspecialchars($order->user_ip)?>">');
	$('#old_ioss_orders').click(function(){
		$form.submit();
	});
});

</script>