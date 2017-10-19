<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption(l($thisMethod).'游戏')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $game->update_time)?>" />
					<div class="form-body">
						<?=edit_form_input('name', set_value('name', $game->name), '名称');?>
						<?=edit_form_input('code', set_value('code', $game->code), '标识', $thisMethod=='edit'?'disabled="disabled"':'');?>
						<?=edit_form_input('sort', set_value('sort', $game->sort), '排序');?>
						<?=edit_form_textarea('description', set_value('description', $game->description), '描述');?>
						<?=edit_form_radio_list('state', Array(MGame::STATE_ENABLE=>'启用', MGame::STATE_DISABLE=>'禁用'), set_value('state', ($thisMethod == 'edit'?$game->state:MGame::STATE_DISABLE)), '状态')?>
					</div>
					<?php $this->load->view('partials/submitButtons.tpl')?>
				<?=form_close()?>
			</div>
		</div>
		<!-- END FORM PORTLET-->
	</div>
</div>
<!-- END PAGE CONTENT-->