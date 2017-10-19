<div class="portlet-body form" id="order_hold_frm">
	<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
		<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $order->update_time)?>" />
		<div class="modal-body" style="border-bottom:1px solid #EFEFEF;">
			<div class="form-body">
				<?=edit_form_static('订单号', $order->no)?>
				<?=edit_form_static('支付方式', $payment_method?$payment_method->name:'')?>
				<?=edit_form_static('金额', $this->currency->format($order->amount-$payAmount, $order->currency))?>
				<?=edit_form_input_group('fee', set_value('fee', ''), '手续费', '<span class="input-group-addon"><i class="fa fa-'.strtolower($order->currency).'"></i></span>')?>
				<?=edit_form_input('transcation_id', set_value('transcation_id', ''), '流水号')?>
				<?=edit_form_textarea('note', set_value('note', ''), '备注')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-9 control-label m_wrong">
			<?=$this->load->view('partials/message_modal.tpl')?>
			</label>
			<div class="col-xs-3">
				<div class="form-group modal-footer-style pull-right">
					<button type="button" class="btn default" data-dismiss="modal">关闭</button>
					<?php if($flg){?><button type="submit" class="btn green">保存</button><?php }?>
				</div>
			</div>
		</div>
	<?=form_close()?>
</div>
<script type="text/javascript">
$('.modal-body').find('.col-xs-4').removeClass('col-xs-4').addClass('col-xs-7');
</script>
