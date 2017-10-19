<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption(l($thisMethod).'支付帐号')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $paymentAccount->update_time)?>" />
					<div class="form-body">
					<?php $method_optinons = Array(''=>'请选择支付方式...') + parse_options($paymentMethods); ?>
						<?=edit_form_dropdown('method_id',$method_optinons, set_value('method_id', $paymentAccount->method_id), l('payment_method'),  $thisMethod=='edit'?'disabled':'') ?>
						<?=edit_form_input('account',  set_value('account', $paymentAccount->account), l('payment_account'));?>
						<?=edit_form_textarea('config',  set_value('config',$paymentAccount->config), l('payment_account_config'))?>
						<?=edit_form_radio_list('state', Array(MPaymentAccount::STATE_ENABLE=>'启用', MPaymentAccount::STATE_DISABLE=>'禁用'), set_value('state',$paymentAccount->state),l('payment_account_state') )?>
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


