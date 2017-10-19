<div class="modal-body">
	<div class="portlet-body form">
		<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
			<div class="form-body">
				<?=edit_form_input('verify_value', set_value('verify_value', $order->user_phone), '电话')?>
				<?=edit_form_textarea('note', set_value('note'), '备注')?>
				<div class="form-group modal-footer-style pull-right">
					<div class="col-xs-12">
					   <button type="button" class="btn default" data-dismiss="modal">关闭</button>
			   		   <button type="submit" class="btn green">保存</button>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		<?=form_close()?>
	</div>
</div>
<script type="text/javascript">
$('.modal-body').find('.col-xs-4').removeClass('col-xs-4').addClass('col-xs-7');
$('.modal-body').find('textarea').attr('rows', '4');
</script>
