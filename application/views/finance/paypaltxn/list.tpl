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
		<?=form_open('', Array('class'=>'portlet-body'))?> 
			<div class="portlet-body">
				<div class="form-group">
					<div class="form-inline" role="form">
						<div class="form-group">
							<input type="text" class="form-control input-medium" placeholder="invoice_id" name="invoice" value="<?=filterValue('invoice')?>" >
						</div>
						<div class="form-group">
							<input type="text" class="form-control input-medium" placeholder="transaction id" name="txn_id" value="<?=filterValue('txn_id')?>" >
						</div>
						<div class="form-group">
							<input type="text" class="form-control input-medium" placeholder="receipt id" name="receipt_id" value="<?=filterValue('receipt_id')?>" >
						</div>
						<div class="form-group">
							<input type="text" class="form-control input-medium" placeholder="payer email" name="payer_email" value="<?=filterValue('payer_email')?>" >
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="form-inline" role="form">
						<div class="form-group">
							<input type="text" class="form-control input-medium" placeholder="receiver email" name="receiver_email" value="<?=filterValue('receiver_email')?>" >
						</div>
						<?=search_form_dropdown('payment_status', array(''=>'交易状态...')+parse_options($payStates), filterValue('payment_status'),'交易状态', 'class="form-control input-medium"');?>
						<div class="form-group">
							<div class="input-group input-large date-picker input-daterange" data-date="" data-date-format="yyyy-mm-dd">
								<input type="text" class="form-control" name="create_begin" value="<?=filterValue('create_begin')?>" placeholder="开始时间">
								<span class="input-group-addon">
								to </span>
								<input type="text" class="form-control" name="create_end" value="<?=filterValue('create_end')?>" placeholder="结束时间">
							</div>
						</div>
						<div class="form-group">
							<button type="button" class="btn default" id="reset">重置条件</button>
						</div>
						<div class="form-group">
							<button type="submit" class="btn green">搜索<i class="fa fa-search"></i></button>
						</div>
					</div>
				</div>
			</div>
		<?=form_close();?>
			<div class="table-scrollable">
				<table class="table table-hover table-bordered table-striped about_table">
					<thead>
					<tr>
						<th>交易时间</th>
						<th>姓名</th>
						<th>邮箱</th>
						<th>交易类型</th>
						<th>交易状态</th>
						<th>金额(货币)</th>
						<th>交易流水号</th>
						<th>Invoice</th>
					</tr>
					</thead>
					<tbody>
					<?php if(isset($lst)){
							foreach ($lst as $r){?>									
					<tr>
						<td><?=date('Y-m-d H:i',strtotime($r->payment_date))?></td>
						<td><?=$r->first_name?>&nbsp;<?=$r->last_name?> </td>
						<td><?=$r->payer_email?></td>
						<td><?=$r->txn_type?></td>
						<td><?=$r->payment_status?></td>
						<td><?=$this->currency->format($r->mc_gross, $r->mc_currency)?></td>
						<td><?=$r->txn_id?></td>
						<td><a href="<?=site_url('/sales/order/view/'.$r->custom)?>"><?=$r->invoice?></a></td>
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