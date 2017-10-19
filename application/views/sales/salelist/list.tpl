<?php
$paymentStatesLineCls = Array(MOrder::PAY_STATE_UNPAIED=>'unpaid', MOrder::PAY_STATE_PEDING=>'pending', MOrder::PAY_STATE_PAID=>'paid', MOrder::PAY_STATE_REFUNDED=>'refunded', MOrder::PAY_STATE_PART=>'part_refunded', MOrder::PAY_STATE_REVERSED=>'reversed');
$paymentStatesCls = $this->config->item('pay_states_css');
$orderStatesCls = $this->config->item('order_states_css');
$deliveryStatesCls = $this->config->item('delivery_states_css');
$riskStatesCls = $this->config->item('risk_states_css');
$deliveryPermission = UserPower::getPermisionInfo($thisModule.'delivery');
?>
<!-- BEGIN PAGE HEADER-->
<?php
$this->load->view ( 'partials/breadcrumb.tpl', Array (
		'_BTN_GROUP' => array (
				'url' => site_url ( $thisModule . $thisController . '/' . $thisMethod . '/export' ),
				'class' => 'btn default',
				'name' => '导出 ',
				'i' => '<i class="fa fa-cloud-download"></i> ' 
		) 
) )
?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript">
$(document).ready(function(){
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='type']",
		url: "<?=site_url('common/Ajax/GetTypes')?>/{0}",
		empty_option: '请选择类型...',
		init_child_val: "<?=filterValue('type')?>"
	});
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='category_id']",
		url: "<?=site_url('common/Ajax/GetCategorys')?>/{0}",
		empty_option: '请选择目录...',
		init_child_val: "<?=filterValue('category_id')?>"
	});
	$("#reset").click(function(){
		$("select").val('');
		$("input[type='text']").val('');
	});

	$('.popovers').popover({ "html": true,'content':getContent});
	
});

function getContent(){
	var game_id = $(this).attr('data-game');
	var category_id = $(this).attr('data-category');
	$.post("<?=site_url('sales/salelist/getTree')?>"+'/'+game_id+'/'+category_id,{}
			,function(data){
				if(data){
					$(".data_content").html(data);
				}
			});
	return "<p class='data_content' style='width:300px;word-wrap:break-word;'>加载中....</p>"
}
</script>
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<?=form_open('', Array('class'=>'portlet-body'))?> 
				<div class="form-group">
				<div class="form-inline" role="form">
						<?=search_form_dropdown('site_id', array(''=>'选择网站...')+parse_options($this->sites), filterValue('site_id'),'网站');?>
						<?=search_form_dropdown('game_id', array(''=>'选择游戏...')+parse_options($this->games), filterValue('game_id'),'游戏');?>
						<?=search_form_dropdown('type', array(''=>'选择类型...'), filterValue('type'),'类型');?>
						<?=search_form_dropdown('category_id', array(''=>'选择目录...'), filterValue('category_id'),'目录');?>
					</div>
			</div>
			<div class="form-group">
				<div class="form-inline" role="form">
						<?=search_form_dropdown('state',array(''=>'订单状态...')+$orderStates,filterValue('state'),'订单状态', 'class="form-control input-medium"');?>
						<?=search_form_dropdown('payment_state',array(''=>'支付状态...')+$paymentStates,filterValue('payment_state'),'支付状态', 'class="form-control input-medium"');?>
						<?=search_form_dropdown('delivery_state',array(''=>'发货状态...')+$deliveryStates,filterValue('delivery_state'),'发货状态', 'class="form-control input-medium"');?>
						<?=search_form_dropdown('risk',array(''=>'风险等级...')+$riskStates,filterValue('risk'),'风险等级', 'class="form-control input-medium"');?>
					</div>
			</div>
			<div class="form-inline" role="form">
				<div class="form-group">
					<input type="text" class="form-control input-large	form-filter"
						value="<?=filterValue('no')?>" name="no" placeholder="请输入订单号">
				</div>
				<div class="form-group">
					<div class="input-group input-large date-picker input-daterange"
						data-date="" data-date-format="yyyy-mm-dd">
						<input type="text" class="form-control" name="create_begin"
							value="<?=filterValue('create_begin')?>" placeholder="起始时间"> <span
							class="input-group-addon"> to </span> <input type="text"
							class="form-control" name="create_end"
							value="<?=filterValue('create_end')?>" placeholder="结束时间">
					</div>
				</div>
				<div class="form-group">
					<button type="button" class="btn default" id="reset">重置条件</button>
				</div>
				<div class="form-group">
					<button type="submit" class="btn green" style="padding: 7px 14px">
						搜索 <i class="fa fa-search"></i>
					</button>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="table-scrollable">
						<table class="table table-hover table-bordered table-striped">
							<thead>
								<tr>
									<th width="165px">订单号</th>
									<th >游戏</th>
									<th >目录</th>
									<th >商品名称</th>
									<th width="80px">单价</th>
									<th width="65px">数量</th>
									<th width="130px">创建时间</th>
									<th width="80px">付款</th>
									<th width="80px">发货状态</th>
									<th width="80px">操作</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($lst as $v){?> 
									<tr
									<?=array_key_exists($v->state, $orderStatesCls)?' class="'.$orderStatesCls[$v->state].'"':''?>>
									<td class="<?=element($v->payment_state, $paymentStatesLineCls, '')?>">
										<a href="<?=site_url($thisModule.'order/view/'.$v->order_id)?>"><?=htmlspecialchars($v->no)?></a>
											<i style="cursor: default;" class="<?=element($v->risk, $riskStatesCls, '')?>" 	title="<?=element($v->risk, $riskStates, '--')?>"></i>
									</td>
									<td><?= htmlspecialchars($v->game_name . ' - ' . $this->types[$v->type])?></td>
									<td><a class="popovers" data-original-title="商品目录"  role="button" tabindex="0" 
						data-placement="top" data-container="body" data-trigger="focus" data-game="<?=$v->game_id?>" data-category="<?=$v->category_id?>"><?=htmlspecialchars($v->category_name)?></a></td>
									<td><?=htmlspecialchars($v->name)?></td>
									<td><?=htmlspecialchars($this->currency->format($v->price, DEFAULT_CURRENCY))?></td>
									<td><?=$v->num?></td>
									<td><?=date('Y-m-d H:i', $v->create_time)?></td>
									<td><span class="<?=element($v->payment_state, $paymentStatesCls, '')?>"><?=element($v->payment_state, $paymentStates, '--')?></span></td>
									<td><span class="<?=element($v->delivery_state, $deliveryStatesCls, '')?>"><?=element($v->delivery_state, $deliveryStates, '--')?></span></td>
									<td><a href="javascript:void(0);" class="btn about_edit <?=$deliveryPermission->read?'':'disabled' ?>" 	onclick="ioss_url_modal('<?=site_url('/sales/delivery/view/'.$v->id)?>', {big:true});">
											<i class="fa fa-truck"></i> 详细
									</a></td>
								</tr>
								<?php }?> 
								</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 text-right">
						<?=$pagination?>
					</div>
			</div>
			<?=form_close()?>
		</div>
		<!-- End: life time stats -->
	</div>
</div>
<!-- END PAGE CONTENT-->
