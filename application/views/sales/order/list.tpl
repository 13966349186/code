<?php
$paymentStatesCls = $this->config->item('pay_states_css');
$orderStatesCls = $this->config->item('order_states_css');
$deliveryStatesCls = $this->config->item('delivery_states_css');
$riskStatesCls = $this->config->item('risk_states_css');

unset($orderStates[MOrder::STATE_UNSUBMITTED]);
?>
<!-- BEGIN PAGE HEADER-->
<?php 
$btn = array(
		'url' => site_url ( $thisModule . $thisController . '/' . $thisMethod . '/export' ),
		'class' => 'btn default',
		'name' => '导出订单',
		'i' => '<i class="fa fa-cloud-download"></i> ' 
);
$this->load->view('partials/breadcrumb.tpl', Array('_BTN_GROUP'=>$btn ))?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="portlet-body">
	<div class="tabbable" >
		<div class="navbar navbar-default navbar-static" role="navigation">
			<div class="navbar-header">
				<button class="navbar-toggle" type="button" data-toggle="collapse" 	data-target=".bs-js-navbar-scrollspy">
					<span class="sr-only">Filter</span> <span class="icon-bar"></span>
					<span class="icon-bar"></span> <span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#" data-toggle="modal" 	data-target="#myModal1">订单筛选 :</a>
			</div>
			<div class="collapse navbar-collapse bs-js-navbar-scrollspy">
				<ul class="nav navbar-nav" id="tab_head">
				<?php foreach ($filter_define as $k=>$v){?> 
					<li class="<?=$v==$filters?'active':'' ?>"><a href="javascript:void(0);"><?=$k?></a></li>
				<?php }?> 
				</ul>
				<div class="navbar-form navbar-right">
					<div class="input-group">
						<input type="text" id="stxt" class="form-control input-large" placeholder="订单号 / Email / Phone / IP / 游戏账号" value="<?=htmlspecialchars(filterValue('stxt')) ?>">
						<div class="input-group-btn">
							<button type="button" class="btn green search-type" tabindex="-1" ><i class="fa fa-search"></i> 搜索</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tabbable">
			<div id="tags" class="tagsinput" style="width: auto; margin-top: -10px; margin-bottom: 10px; display:none">
			<span class="tag"><a href="#" title="清除条件"  id="remove_filter"><i class="fa fa-times"></i></a></span>
			</div>
		</div>
	</div>
	<div class="table-scrollable">
		<table class="table table-bordered table-hover table-striped">
			<thead>
				<tr>
					<th width="15%">订单号</th>
					<th width="10%">网站</th>
					<th>邮箱</th>
					<th width="15%">游戏</th>
					<th width="11%">创建时间</th>
					<th width="8%">订单金额</th>
					<th width="8%">支付方式</th>
					<th width="6%">支付状态</th>
					<th width="6%">发货状态</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($lst as $v){?> 
				<tr class="<?=$orderStatesCls[$v->state] ?>">
					<td><a href="<?=site_url($thisModule.$thisController.'/view/'.$v->id)?>"	class="order_number <?=$orderStatesCls[$v->state] ?>"><?=htmlspecialchars($v->no)?></a>
						   &nbsp;<i style="cursor: default;" 	class="<?=element($v->risk, $riskStatesCls, '')?>" title="<?=element($v->risk, $riskStates, $v->risk)?>"></i>
					</td>
					<td><?=htmlspecialchars($v->site_name)?></td>
					<td><?=htmlspecialchars($v->user_email)?></td>
					<td><?=htmlspecialchars($v->game_name .' - '.$this->types[$v->product_type])?></td>
					<td><?=date('Y-m-d H:i', $v->create_time)?></td>
					<td><?=$this->currency->format($v->amount, $v->currency)?></td>
					<td><?=htmlspecialchars(element($v->payment_method, $paymentMethods, '['.$v->payment_method.']'))?></td>
					<td><span class="<?=element($v->payment_state, $paymentStatesCls, '')?>"><?=element($v->payment_state, $paymentStates, '--')?></span></td>
					<td><span class="<?=element($v->delivery_state, $deliveryStatesCls, '')?>"><?=element($v->delivery_state, $deliveryStates, '--')?></span></td>
				</tr>
			<?php }?> 
			</tbody>
		</table>
	</div>
	<div class="row">
		<div class="col-xs-12 text-right">
			<?=$pagination?>
		</div>
	</div>
</div>

<!-- 模态框1 -->
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog"
	aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true"></button>
				<h4 class="modal-title">订单筛选</h4>
			</div>
			<div class="modal-body">
				<div class="portlet-body form">
			  		<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form', 'id'=>'order_frm'))?> 
						<div class="form-body">
							<?=edit_form_dropdown('site_id',array(''=>'') + $this->sites->toArray() ,filterValue('site_id'),'网站');?>
							<?=edit_form_dropdown('game_id',array(''=>'') + $this->games->toArray(), filterValue('game_id'),'游戏');?>
							<?=edit_form_dropdown('product_type',array(''=>''), '','类型' ,"class='form-control' id='product_type' data-default='". filterValue('product_type'). "' data-default-text='". $this->types[filterValue('product_type')] ."' ");?>
							<?=edit_form_dropdown('payment_method',array(''=>'')+$paymentMethods,filterValue('payment_method'),'支付方式');?>
							<?=edit_form_dropdown('payment_state',array(''=>'')+$paymentStates,filterValue('payment_state'),'支付状态');?>
							<?=edit_form_dropdown('delivery_state',array(''=>'')+$deliveryStates,filterValue('delivery_state'),'发货状态');?>
							<?=edit_form_dropdown('risk',array(''=>'')+$riskStates,filterValue('risk'),'风险等级');?>
							<?=edit_form_dropdown('state',array(''=>'')+$orderStates,filterValue('state'),'订单状态');?>
							<div id="hold_reason_div" style="display: none;">
								<?=edit_form_dropdown('hold_reason',array(''=>'')+$this->config->item('hold_reason'),filterValue('hold_reason'),'问题种类');?>
							</div>
						<div class="form-group">
							<label class="col-xs-3 control-label">创建时间</label>
							<div class="col-xs-6">
								<div class="input-group date-picker input-daterange" data-date="" data-date-format="yyyy-mm-dd">
									<input type="text" class="form-control" name="create_begin" value="<?=filterValue('create_begin')?>" placeholder="起始时间">
									<span class="input-group-addon">to</span>
									<input type="text" class="form-control" name="create_end" 	value="<?=filterValue('create_end')?>" placeholder="结束时间">
								</div>
							</div>
						</div>
							<?=edit_form_input('stxt',filterValue('stxt'),'订单内容', 'placeholder="订单号 / Email / Phone / IP / 游戏账号"') ?>
						<div class="form-group">
							<label class="col-xs-3 control-label"></label>
							<div class="col-xs-9">
								<div class="checkbox" style="padding-top: 7px;">
									<label><span><input type="checkbox" name="sort" value=1 <?=set_checkbox('sort', '1', filterValue('sort')==='1')?>></span>按时间排序</label>
								</div>
							</div>
						</div>
					</div>
					<?=form_close()?> 
				</div>
			</div>
			<!-- 模态框footer部分 -->
			<div class="modal-footer">
				<button type="button" id="reset" class="btn default">清除条件</button>
				<button type="button" id="search" class="btn green">应用</button>
			</div>
		</div>
	</div>
</div>
<script src="<?=image_url('/static/js/order.js?1943')?>" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	Order._nav_define = (<?=json_encode($filter_define)?>);
	Order._nav_filters_all =  (<?=json_encode($filters_all)?>);
	Order._state_holding = (<?=MOrder::STATE_HOLDING?>);
	Order.init();
});
<!--
$("#myModal1  .modal-body").find(".col-xs-4").removeClass("col-xs-4").addClass("col-xs-6");
//-->
</script>
<!-- 模态框结束 -->
<!-- END PAGE CONTENT-->