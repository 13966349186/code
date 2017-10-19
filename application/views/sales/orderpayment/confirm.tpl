<div class="portlet-body form" id="order_hold_frm">
	<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form', 'id'=>'modal_pay_confirm_frm'))?>
		<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $obj->update_time)?>" />
		<input type="hidden" id="op_flg" name="op_flg" value="1" />
		<div class="modal-body" style="border-bottom:1px solid #EFEFEF;">
			<div class="form-body">
				<?=edit_form_static('订单号', $order->no)?>
				<?=edit_form_static('流水号', $obj->transcation_id)?>
				<?=edit_form_static('金额', $this->currency->format($obj->amount, $obj->currency))?>
				<?=edit_form_static('费用', $this->currency->format($obj->fee, $obj->currency))?>
				<?=edit_form_static('时间', date('Y-m-d H:i:s', $obj->create_time))?>
				<?=edit_form_textarea('note', set_value('note', $obj->note), '备注')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-7 control-label m_wrong">
			<?=$this->load->view('partials/message_modal.tpl')?>
			</label>
			<div class="col-xs-5">
				<div class="form-group modal-footer-style pull-right">
					<input type="button"  onclick="cancelBtn()" class="btn btn-warning" value="取消<?=$title?>" />
					<button type="submit" class="btn green">确认<?=$title?></button>
				</div>
			</div>
		</div>
	<?=form_close()?>
</div>
<script type="text/javascript">
$('.modal-body').find('.col-xs-4').removeClass('col-xs-4').addClass('col-xs-7');
function cancelBtn(){
	if($("#modal_pay_confirm_frm").attr("submit_flg") == "1"){
		return false;
	}
	$("#modal_pay_confirm_frm").attr("submit_flg", "1");
	$("#op_flg").val("0");
	$("#modal_pay_confirm_frm").submit();
}
</script>
