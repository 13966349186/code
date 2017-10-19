<div class="portlet-body form" id="order_hold_frm">
	<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form', 'id'=>'refund_frm'))?>
		<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $order->update_time)?>" />
		<input type="hidden" id="confirm_flg" name="confirm_flg" value="<?=htmlspecialchars($this->input->get_post('confirm_flg'));?>" />
		<input type="hidden" id="delivery_confirm_flg" name="delivery_confirm_flg" value="<?=htmlspecialchars($this->input->get_post('delivery_confirm_flg'));?>" />
		<div class="modal-body" style="border-bottom:1px solid #EFEFEF;">
			<div class="form-body">
				<?=edit_form_static('订单号', $order->no)?>
				<?=edit_form_static('支付方式', $payment_method?$payment_method->name:'')?>
				<?=edit_form_static('订单金额', $this->currency->format($order->amount, $order->currency))?>
				<?=edit_form_input_group('amount', set_value('amount', isset($amount)?$amount:''), '退款金额', '<span class="input-group-addon"><i class="fa fa-'.strtolower($order->currency).'"></i></span>')?>
				<?=edit_form_input_group('fee', set_value('fee', ''), '手续费', '<span class="input-group-addon"><i class="fa fa-'.strtolower($order->currency).'"></i></span>')?>
				<?=edit_form_input('transcation_id', set_value('transcation_id', ''), '流水号')?>
				<?=edit_form_textarea('note', set_value('note', ''), '备注')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-9 control-label m_wrong"><?=$this->load->view('partials/message_modal.tpl')?></label>
			<div class="col-xs-3">
				<div class="form-group modal-footer-style pull-right">
					<button type="button" class="btn default" data-dismiss="modal">关闭</button>
					<?php if($flg){?><button type="submit" onclick="return btn_submit();" class="btn green <?=$amount===''&&$order->payment_state==MOrder::PAY_STATE_REVERSED?'disabled':''?>">保存</button><?php }?>
				</div>
			</div>
		</div>
	<?=form_close()?>
</div>
<script type="text/javascript">
window.btn_submit = function(){
<?php if($order->delivery_state != MORder::DELEVERY_STATE_NOT_DELEVERED){?> 
	if($("#delivery_confirm_flg").val().length < 1){
		if(confirm('该订单有过发货记录，确定要退款吗？')){
			$("#delivery_confirm_flg").val('1');
			return true;
		}
		return false;
	}
<?php }?> 
	return true;
}
$('.modal-body').find('.col-xs-4').removeClass('col-xs-4').addClass('col-xs-7');
function modal_init(){
	var cfm_flg = "<?=(isset($cfm_flg)&&$cfm_flg)?'1':'0'?>";
	if(cfm_flg == '1'){
		if(confirm("退款金额已超出付款金额，确定要继续吗？")){
			$("#confirm_flg").val('1');
			$("#refund_frm").submit();
		}
	}
}
</script>
