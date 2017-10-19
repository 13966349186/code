<?php
$deliveryStatesCls = $this->config->item('delivery_states_css');
$deliveryStates = $this->MOrder->getDeliveryState();
?>
<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_NO_IOSS_NEW'=>'1'))?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript">
$(document).ready(function(){
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='product_type']",
		url: "<?=site_url('common/Ajax/GetTypes')?>/{0}",
		empty_option: '请选择类型...',
		init_child_val: "<?=set_value('product_type', $obj->product_type)?>"
	});
});
</script>
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption(l($thisMethod).'测试订单')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $obj->update_time)?>" />
					<div class="form-body">
						<?=edit_form_dropdown('site_id', $this->sites, set_value('site_id', $obj->site_id), '网站') ?>
						<?=edit_form_input_group('user_full_name',set_value('user_full_name',$obj->user_full_name),'姓名'); ?>
						<?=edit_form_input_group('user_email', set_value('user_email',$obj->user_email),  'Email')?>
						<?=edit_form_input_group('user_phone', set_value('user_phone',$obj->user_phone),  '电话')?>
						<?=edit_form_input_group('user_ip', set_value('user_ip',$obj->user_ip),  'IP')?>
						<?=edit_form_input_group('user_agent', set_value('user_agent',$obj->user_agent),  '浏览器')?>
						<?=edit_form_input_group('refer_url', set_value('refer_url',$obj->refer_url),  '来源url')?>
						<?=edit_form_dropdown('game_id', $this->games, set_value('game_id', $obj->game_id), '游戏') ?>
						<?=edit_form_dropdown('product_type', Array(), set_value('product_type', $obj->product_type), '商品类型') ?>
						<?=edit_form_dropdown('currency', $currencies, set_value('currency', $obj->currency), '货币') ?>
						<?=edit_form_input_group('amount', set_value('amount',$obj->amount),  '金额')?>
						<?=edit_form_dropdown('type', $this->config->item('order_type'), set_value('type', $obj->type), '订单类型') ?>
						<?=edit_form_input_group('note',set_value('note',$obj->note),'备注'); ?>
						<?=edit_form_dropdown('state', $this->MOrder->getState(), set_value('state', $obj->state), '订单状态') ?>
						<?=edit_form_dropdown('risk', $this->MOrder->getRiskState(), set_value('risk', $obj->risk), '风险等级') ?>
						<?=edit_form_dropdown('hold_reason', Array('0'=>'未填写')+ $this->config->item('hold_reason'), set_value('hold_reason', $obj->hold_reason), '问题种类') ?>
						<?=edit_form_dropdown('payment_method', $paymentMethods, set_value('payment_method', $obj->payment_method), '支付方式') ?>
						<?=edit_form_dropdown('payment_state', $this->MOrder->getPayState(), set_value('payment_state', $obj->payment_state), '支付状态') ?>
						<?php $payment_time = set_value('payment_time', $obj->payment_time);?>
						<?=edit_form_input_group('payment_time', is_numeric($obj->payment_time)?date('Y-m-d H:i:s', $obj->payment_time):$obj->payment_time,'支付时间'); ?>
						<?=edit_form_dropdown('delivery_state', $this->MOrder->getDeliveryState(), set_value('delivery_state', $obj->delivery_state), '发货状态') ?>
						<?php $payment_time = set_value('delivery_time', $obj->delivery_time);?>
						<?=edit_form_input_group('delivery_time', is_numeric($obj->delivery_time)?date('Y-m-d H:i:s', $obj->delivery_time):$obj->delivery_time,'发货时间'); ?>

		<div class="form-horizontal" role="form">
			<div class="">
				<table class="table table-hover table-bordered table-striped">
					<thead>
						<tr>
							<th width="15%">商品类型</th>
							<th width="15%">目录</th>
							<th width="17%">商品名</th>
							<th width="8%">价格</th>
							<th width="10%">购物车数量</th>
							<th width="8%">总价</th>
							<th width="9%"><span style="width:67px;">状态</span></th>
							<th width="9%"></th>
						</tr>
					</thead>
					<tbody id="add_body">
					<?php
					$undelivery_str = '<span class="'.$deliveryStatesCls[MOrder::DELEVERY_STATE_NOT_DELEVERED].'">'.$deliveryStates[MOrder::DELEVERY_STATE_NOT_DELEVERED].'</span>';
					if(array_key_exists('add_jsons', $_POST) && ($json_arr = $_POST['add_jsons'])){
						foreach ($json_arr as $str){
							$v = json_decode($str);
					?>
						<tr id="tr_new<?=$v->id?>" data-newflg=1>
							<td><?=htmlspecialchars($v->type_name)?></td>
							<td><?=htmlspecialchars($v->category_name)?></td>
							<td><?=htmlspecialchars($v->name)?></td>
							<td><?=htmlspecialchars($v->price)?></td>
							<td><?=htmlspecialchars($v->num)?></td>
							<td><?=htmlspecialchars($v->price*$v->num)?></td>
							<td><?=$undelivery_str?></td>
							<td>
								<input type="hidden" name="add_ids[]" value="<?=$v->id?>" />
								<input type="hidden" name="add_jsons[]" value="<?=htmlspecialchars($str)?>" />
								<a href="javascript:void(mydel('#tr_new<?=$v->id?>', true));" class="black about_edit"><i class="fa fa-trash-o"></i> 删除</a>
							</td>
						</tr>
					<?php }?>
					<?php }?>
					</tbody>
				</table>
			</div>
			<div class="form-actions">
				<button class="btn blue-madison" onclick="ioss_url_modal('<?=site_url('sales/orderproduct/add_pre/0')?>', {big:true});" type="button">添加行</button>
			</div>
		</div>
					</div>
					<?php $this->load->view('partials/submitButtons.tpl')?>
				<?=form_close()?>
			</div>
		</div>
		<!-- END SAMPLE FORM PORTLET-->
		<!-- BEGIN SAMPLE FORM PORTLET-->
	</div>
</div>
<!-- END PAGE CONTENT-->
<script type="text/javascript">
var undelivery_str = "<?=parse_js($undelivery_str)?>";
if(!window.mydel){
	window.mydel = (function(trid, delflg){
		if(delflg){
			$(trid).remove();
		}else{
			$(trid).hide();
			$(trid).find("[data-delflg=1]").val("1");
		}
	});
}
if(!window.cartAddRow){
	window.cartAddRow = function(info, json_str){
		var html = '<tr id="tr_new'+info.id+'" data-newflg=1>';
		html += '<td>'+htmlspecialchars(info.type_name)+'</td>';
		html += '<td>'+htmlspecialchars(info.category_name)+'</td>';
		html += '<td>'+htmlspecialchars(info.name)+'</td>';
		html += '<td>'+htmlspecialchars(info.price)+'</td>';
		html += '<td>'+htmlspecialchars(info.num)+'</td>';
		html += '<td>'+htmlspecialchars(info.num*info.price)+'</td>';
		html += '<td>'+undelivery_str+'</td>';
		html += '<td><input type="hidden" name="add_ids[]" value="'+info.id+'" />';
		html += '<input type="hidden" data-type="json" name="add_jsons[]" value="" />';
		html += '<a href="javascript:void(mydel(\'#tr_new'+info.id+'\', true));" class="black about_edit"><i class="fa fa-trash-o"></i> 删除</a>';
		html += '</td></tr>';
		$("#add_body").append(html);
		$("#tr_new"+info.id).find("[data-type='json']").val(json_str);
	};
}
if(!window.htmlspecialchars){
	window.htmlspecialchars = function(info){
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(info));
		return div.innerHTML;
	};
}
</script>
