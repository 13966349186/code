<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption(l($thisMethod).'网站')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $site->update_time)?>" />
					<div class="form-body">
						<?=edit_form_input('name',set_value('name', $site->name),  l('site_name')); ?>
						<?=edit_form_input('code',set_value('code', $site->code), l('site_code'), ($thisMethod == 'edit')?'disabled="disabled"':'' )?>
						<?=edit_form_input('domain',set_value('domain',$site->domain), l('site_domain')) ?>
						<?=edit_form_radio_list('state',Array(MSite::STATE_ENABLE=>'启用', MSite::STATE_DISABLE=>'禁用'), set_value('state',$site->state) , l('site_state'))?>
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
