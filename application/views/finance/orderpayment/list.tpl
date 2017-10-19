<!-- BEGIN PAGE HEADER-->
<?php 
$btn = array(
		'url' => site_url ( $thisModule . $thisController . '/' . $thisMethod . '/export' ),
		'class' => 'btn default',
		'name' => '导出',
		'i' => '<i class="fa fa-cloud-download"></i> ' 
);
$this->load->view('partials/breadcrumb.tpl', Array('_BTN_GROUP'=>$btn ))?>
<!-- END PAGE HEADER-->
<script type="text/javascript">
$(document).ready(function(){
	$("#reset").click(function(){
		$("select").val('');
		$("input[type='text']").val('');
	});
});
</script>
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<div class="portlet">
			<div class="portlet-body">
				<?=form_open('', Array('class'=>'portlet-body'))?> 
				<div class="form-group">
					<div class="form-inline" role="form">
						<?=search_form_dropdown('site_id', array(''=>'选择网站...')+parse_options($sites), filterValue('site_id'),'网站', 'class="form-control input-medium"');?>
						<?=search_form_dropdown('payment_method', array(''=>'支付方式...')+parse_options($paymentMethods), filterValue('payment_method'),'支付方式', 'class="form-control input-medium"');?>
						<?=search_form_dropdown('type', array(''=>'交易类型...')+parse_options($paymentTypes), filterValue('type'),'交易类型', 'class="form-control input-medium"');?>
						<?=search_form_dropdown('state', array(''=>'交易状态...')+parse_options($paymentStates), filterValue('state'),'交易状态', 'class="form-control input-medium"');?>
					</div>
				</div>
				
				<div class="form-group">
					<div class="form-inline" role="form">
							<div class="form-group">
								<input type="text" class="form-control input-medium" placeholder="请输入订单号" name="order_no" value="<?=filterValue('order_no')?>" >
							</div>
							<div class="form-group">
								<input type="text" class="form-control input-medium" placeholder="请输入流水号" name="transcation_id" value="<?=filterValue('transcation_id')?>" >
							</div>
							<div class="form-group">
								<div class="input-group input-large date-picker input-daterange" data-date="" data-date-format="yyyy-mm-dd">
									<input type="text" class="form-control" name="create_begin" value="<?=filterValue('create_begin')?>" placeholder="开始时间">
									<span class="input-group-addon">to </span>
									<input type="text" class="form-control" name="create_end" value="<?=filterValue('create_end')?>" placeholder="结束时间">
								</div>
								<div class="form-group">
									<button type="button" class="btn default" id="reset">重置条件</button>
								</div>
								<div class="form-group">
									<button type="submit" class="btn green">搜索 <i class="fa fa-search"></i></button>
								</div>
							</div>
					</div>
				</div>
				<?=form_close();?> 
			</div>
				
			<div class="table-scrollable">
				<table class="table table-hover table-bordered table-striped about_table">
					<thead>
					<tr>
						<th width="13%">交易时间</th>
						<th width="7%">支付方式</th>
						<th width="7%">交易类型</th>
						<th width="8%">金额(货币)</th>
						<th width="7%">手续费</th>
						<th width="10%">交易状态</th>
						<th>交易流水号</th>
						<th width="16%">订单号</th>
					</tr>
					</thead>
					<tbody>
					<?php if(isset($lst)){
						$state_css = $this->config->item('payment_state_css');
						$type_css = $this->config->item('payment_type_css');
						foreach ($lst as $r){
					?>
					<tr>
						<td><?=date('Y-m-d H:i',$r->create_time)?></td>
						<td><?=$paymentMethods[$r->payment_method]?></td>
						<td><span class="<?=element($r->type, $type_css, '')?>"><?=$paymentTypes[$r->type]?></span></td>
						<td><?=$this->currency->format($r->amount, $r->currency)?></td>
						<td><?=$this->currency->format($r->fee, $r->currency)?></td>
						<td><span class="<?=element($r->state, $state_css, '')?>"><?=element($r->state, $paymentStates, $r->state)?></span></td>
						<td><?=$r->transcation_id?></td>
						<td><a href="<?=site_url('sales/orderpayment/index/'.$r->order_id)?>"><?=$r->order_no?></a></td>
					</tr>
					<?php }}?>
					
					</tbody>
				</table>
			</div>
		</div>
	</div>
		<!-- End: life time stats -->
</div>
<div class="row">
	<div class="col-xs-12 text-right">
		<?=$pagination?>
	</div>
</div>
<!-- END PAGE CONTENT-->