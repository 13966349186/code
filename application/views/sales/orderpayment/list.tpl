<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_BTN_GROUP'=>array('url'=>site_url('sales/order'),  'class'=>'btn default', 'name'=>'返回列表', 'i'=>'<i class="m-icon-swapleft"></i>')) )?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet">
			<?php $this->load->view($thisModule.'/title.tpl', Array('order'=>$order))?> 
			<div class="tabbable">
				<?php $this->load->view($thisModule.'/tab.tpl', Array('order'=>$order))?> 
				<div class="tab-content">
					<div class="tab-pane active" id="tab_2">
						<!-- 订单支付记录开始 -->
						<div class="portlet grey-cascade box">
							<div class="portlet-title">
								<div class="caption">
									支付记录
								</div>
							</div>
							<div class="portlet-body">
								<div class="">
									<table class="table table-hover table-bordered table-striped">
										<thead>
											<tr>
												<th width="15%">交易时间</th>
												<th width="7%">类型</th>
												<th width="9%"><div style="width:75px;">状态</div></th>
												<th width="10%">金额</th>
												<th width="10%">手续费</th>
												<th width="20%">交易流水号</th>
												<th>备注</th>
											</tr>
										</thead>
										<tbody>
										<?php 
										$states = $this->MOrderPayment->getState(); 
										$stateCls = $this->config->item('payment_state_css'); 
										$typeCls = $this->config->item('payment_type_css');
										
										$t_amount = 0;
										$t_fee = 0;
										?>
										<?php 
										foreach ($lst as $v){
											$t_amount += ($v->state== MOrderPayment::STATE_COMPLETED)? $v->amount:0;
											$t_fee += ($v->state== MOrderPayment::STATE_COMPLETED)? $v->fee:0;
										?>
											<tr>
												<td><?=date('Y-m-d H:i:s', $v->create_time)?></td>
												<td><span class="<?=$typeCls[$v->type] ?>"><?=htmlspecialchars($this->MOrderPayment->getType($v->type))?></span></td>
												<td><span class="<?=$stateCls[$v->state]?>"><?=element($v->state, $states, '--')?></span></td>
												<td><?=$this->currency->format($v->amount, $v->currency)?></td>
												<td><?=$this->currency->format($v->fee, $v->currency)?></td>
												<td>
												<?php if($p->edit && ($v->type == MOrderPayment::TYPE_PAY || $v->type == MOrderPayment::TYPE_REFUND) && $v->state == MOrderPayment::STATE_PENDING){?>
													<a href="javascript:void(0);" onclick="ioss_url_modal('<?=site_url($thisModule.'orderpayment/confirm/'.$v->id)?>');"><?=htmlspecialchars($v->transcation_id)?></a>
												<?php }else if($p->edit && $v->type == MOrderPayment::TYPE_CHARGEBACK && $v->state != MOrderPayment::STATE_CANCELLED){?>
													<a href="javascript:void(0);" onclick="ioss_url_modal('<?=site_url($thisModule.'orderpayment/unfreeze/'.$v->id)?>');"><?=htmlspecialchars($v->transcation_id)?></a>
												<?php }else{?>
													<?=htmlspecialchars($v->transcation_id)?>
												<?php }?>
												</td>
												<td><?=htmlspecialchars($v->note)?></td>
											</tr>
										<?php }?>
										</tbody>
										<tfoot style=" font-size: 14px;  font-weight: 600;">
										<tr>
												<td>合计（已完成）</td>
												<td></td>
												<td></td>
												<td><?=$this->currency->format($t_amount, $order->currency) ?></td>
												<td><?=$this->currency->format($t_fee, $order->currency) ?></td>
												<td></td>
												<td></td>
										</tr>
										</tfoot>
									</table>
								</div>
							</div>
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
