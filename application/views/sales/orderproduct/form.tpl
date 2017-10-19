<?php
$deliveryStatesCls = $this->config->item('delivery_states_css');
$deliveryStates = $this->MOrder->getDeliveryState();
?>
<?php $modalCancelId = 'modalCancelId'.time().rand(1, 10000);?>
<form id="<?=$modalCancelId?>" action="<?=site_url('sales/orderproduct/index/'.$order->id)?>" style="display:none;"></form>
<?php $modalId = 'modalId'.time().rand(1, 10000);?>
<form id="<?=$modalId?>" action="<?=site_url('sales/orderproduct/edit/'.$order->id)?>" method="post">
<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $order->update_time)?>" />
<div class="portlet grey-cascade box">
	<div class="portlet-title">
		<div class="caption"><i class="icon-basket"></i>订单商品</div>
		<div class="tools">
			<span class="about_edit" style="cursor:pointer;color:white;" id="edit_order_products_cancel" data-original-title="" title=" "><i class="fa fa-mail-forward"></i> 取消</span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="form-horizontal" role="form">
			<div class="">
				<table class="table table-hover table-bordered table-striped">
					<thead>
						<tr>
							<th width="30%">游戏-目录</th>
							<th width="17%">商品名</th>
							<th width="8%">单价</th>
							<th width="10%">数量</th>
							<th width="8%">总价</th>
							<th width="9%"><span style="width:67px;">状态</span></th>
							<th width="9%"></th>
						</tr>
					</thead>
					<tbody id="add_body">
					<?php
					if(array_key_exists('del_jsons', $_POST)){
						$lst = Array();
						foreach ($_POST['del_jsons'] as $v){
							$lst[] = json_decode($v);
						}
					}else{
						$lst = $this->MOrderProduct->getListByOrderId($order->id);
					}
					?>
					<?php foreach ($lst as $v){?>
						<?php $del_val = set_value('del_flgs[]', '0');?>
						<tr id="tr<?=$v->id?>"<?=$del_val?' style="display:none;"':''?>>
							<td><?=htmlspecialchars($v->game_name.'-'.$v->category_name.' ('.$v->type_name.')')?></td>
							<td><?=htmlspecialchars($v->name)?></td>
							<td><?=$this->currency->format($v->price, DEFAULT_CURRENCY)?></td>
							<td><?=htmlspecialchars($v->num)?></td>
							<td><?=$this->currency->format($v->price*$v->num, DEFAULT_CURRENCY)?></td>
							<td><span class="<?=element($v->delivery_state, $deliveryStatesCls, '')?>"><?=element($v->delivery_state, $deliveryStates, '--')?></span></td>
							<td>
								<input type="hidden" name="del_jsons[]" value="<?=htmlspecialchars(json_encode($v))?>" />
								<input type="hidden" id="del<?=$v->id?>" data-delflg="1" name="del_flgs[]" value="<?=$del_val?>" />
							<?php if($v->delivery_state == MOrder::DELEVERY_STATE_NOT_DELEVERED){?>
								<a href="javascript:void(mydel('#tr<?=$v->id?>', false));" class="black about_edit"><i class="fa fa-trash-o"></i> 删除</a>
							<?php }?>
							</td>
						</tr>
					<?php }?>
					<?php
					$undelivery_str = '<span class="'.element(MOrder::DELEVERY_STATE_NOT_DELEVERED, $deliveryStatesCls, '').'">'.element(MOrder::DELEVERY_STATE_NOT_DELEVERED, $deliveryStates, '--').'</span>';
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
				<?=$this->load->view('partials/message_modal.tpl')?>
			</div>
			<div class="form-actions">
				<button class="btn blue-madison" onclick="ioss_url_modal('<?=site_url('sales/orderproduct/add_pre/'.$order->id)?>', {big:true});" type="button">添加行</button>
				<input type="submit" class="btn green-haze pull-right" value="保存" />
			</div>
		</div>
	</div>
</div>
</form>
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
		html += '<td>'+htmlspecialchars(info.game_name+'-'+info.category_name+'('+info.type_name+')')+'</td>';
		html += '<td>'+htmlspecialchars(info.name)+'</td>';
		html += '<td>'+htmlspecialchars(info.price_disp)+'</td>';
		html += '<td>'+htmlspecialchars(info.num)+'</td>';
		html += '<td>'+htmlspecialchars(info.total_price_disp)+'</td>';
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
$(document).ready(function(){
	$("#<?=$modalCancelId?>").ajaxForm({
		target:$("#<?=$modalCancelId?>").parent(),
		type:'get'
	});
	$("#<?=$modalId?>").ajaxForm({
		target:$("#<?=$modalId?>").parent(),
		type:'post'
	});
	$("#edit_order_products_cancel").click(function(){
		$("#<?=$modalCancelId?>").submit();
	});
});
</script>
