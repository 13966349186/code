<?php
$paymentStatesCls = $this->config->item('pay_states_css');
?>
<!-- BEGIN PAGE HEADER-->
<?php 
$btn = array(
		'url' => site_url ( $thisModule . $thisController . '/' . $thisMethod . '/' ),
		'class' => 'btn  default',
		'name' => 'New &nbsp;<span id="new-order-count" class="badge bg-green">0</span>',
		'i' => '<i class="fa fa-bell-o"></i> '
);
$this->load->view('partials/breadcrumb.tpl', Array('_BTN_GROUP'=>$btn ) )?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="portlet-body">
	<div class="tabbable" >
		<div class="tabbable">
			<div id="tags" class="tagsinput" style="width: auto; margin-top: -10px; margin-bottom: 10px; display:none">
			<span class="tag"><a href="#" title="清除条件"  id="remove_filter"><i class="fa fa-times"></i></a></span>
			</div>
		</div>
	</div>
	<div class="dataTables_wrapper no-footer">
		<table class="table  table-striped  table-bordered table-hover"  id="order_table">
			<thead>
				<tr>
					<th width="15%">订单号</th>
					<th>邮箱</th>
					<th width="20%">游戏</th>
					<th width="8%">支付方式</th>
					<th width="8%">订单金额</th>
					<th width="13%">创建时间</th>
					<th width="6%">支付</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($lst as $v){?> 
				<tr>
					<td><a href="<?=site_url($thisModule.$thisController.'/view/'.$v->id)?>" target="_blank"><?=htmlspecialchars($v->no)?></a></td>
					<td><?=htmlspecialchars($v->user_email)?></td>
					<td><?=htmlspecialchars($v->game_name .' - '.$this->types[$v->product_type].'')?></td>
					<td><?=htmlspecialchars(element($v->payment_method, $paymentMethods, '['.$v->payment_method.']'))?></td>
					<td><?=$this->currency->format($v->amount, $v->currency)?></td>
					<td><?=date('Y-m-d H:i', $v->create_time)?></td>
					<td><span class="<?=element($v->payment_state, $paymentStatesCls, '')?>"><?=element($v->payment_state, $paymentStates, '--')?></span></td>
				</tr>
			<?php }?> 
			</tbody>
		</table>
	</div>
</div>
<!-- END PAGE CONTENT-->
<audio id="new-order-sound" src="<?=image_url('/static/assets/admin/sound/6124.mp3')?>" ></audio>
<script>
jQuery(document).ready(function() {
	var ajaxUrl = "<?= site_url($thisModule . $thisController . '/getCount/' .time() ) ?>";
	var $span = $('#new-order-count');
	var $sound =  $('#new-order-sound');
	$('#order_table').dataTable({
		"dom": '<"top"fi>rt<"bottom"><"clear">',
		"paging": false,
        "bFilter":true,
        "bInfo": true,
        "ordering":false,
        "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
		//"order": [[ 0, "desc" ]],
        "language": {
            "info": "订单： _TOTAL_ ",
        	"infoFiltered": " / _MAX_ "
        }
    });

	function getCount(){
		$.get(ajaxUrl, function(response,status){
			if(status !== "success"){
				console.log("ajax 请求失败：" + response);
				return;
			}
			//console.log(response);
			if(response !== "0" && $span.html() !== response){
				$sound[0].play();
			}
			$span.html(response);
		});
	}
	
	setInterval(getCount, 1000 * 30);
		
});
</script>
