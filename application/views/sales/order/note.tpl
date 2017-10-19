<div class="portlet-body form">
	<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
		<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $order->update_time)?>" />
		<div class="modal-body" style="border-bottom:1px solid #EFEFEF;">
			<div class="form-body">
				<?=edit_form_static('订单号', $order->no)?>
				<?=edit_form_textarea('note', set_value('note', ''), '备注')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-9 control-label m_wrong"><?=$this->load->view('partials/message_modal.tpl')?></label>
			<div class="col-xs-3">
				<div class="form-group modal-footer-style pull-right">
					<button type="button" class="btn default" data-dismiss="modal">关闭</button>
					<button type="submit" class="btn green">保存</button>
				</div>
			</div>
		</div>
	<?=form_close()?>
</div>
<script type="text/javascript">
$('.modal-body').find('.col-xs-4').removeClass('col-xs-4').addClass('col-xs-7');
</script>
