<script type="text/javascript" src="<?=image_url('static/js/ajaxfileupload.js')?>"></script>
<script src="<?=image_url('/plugin/ueditor/ueditor.config.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/plugin/ueditor/ueditor.all.min.js')?>" type="text/javascript"></script>
<link rel="Stylesheet" type="text/css" href="<?=image_url('/plugin/ueditor/themes/default/css/ueditor.css')?>" />
<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green">
			<?=edit_form_caption(l($thisMethod).'邮件模板')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $obj->update_time)?>" />
					<div class="form-body">
						<?=edit_form_input('code',set_value('code', $obj->code), '模板标识', 'disabled="disabled"')?>
						<?=edit_form_input('name',set_value('name', $obj->name),  '模板名称'); ?>
						<?=edit_form_static('说明', $obj->note, false)?>
						<?=edit_form_input('subject',set_value('subject',$obj->subject), '邮件标题') ?>
						<?=edit_form_textarea('message',set_value('message',$obj->message), '邮件内容', 'id="message"') ?>
						<?=edit_form_radio_list('state',Array(MMailTemplate::STATE_ENABLED=>'启用', MMailTemplate::STATE_DISABLED=>'禁用'), set_value('state',$obj->state) , l('site_state'))?>
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-xs-offset-3 col-xs-4">
								<button type="submit" class="btn green <?=$p->edit?'':'disabled'?>">保存</button>
								<a href="<?=site_url($thisModule.$thisController)?>" class="btn default">返回</a>
							</div>
						</div>
					</div>
				<?=form_close()?>
			</div>
		</div>
		<!-- END SAMPLE FORM PORTLET-->
		<!-- BEGIN SAMPLE FORM PORTLET-->
	</div>
</div>
<!-- END PAGE CONTENT-->
<script type="text/javascript" charset="utf-8">
$('.col-xs-4').removeClass('col-xs-4').addClass('col-xs-7');
var editor_content = new baidu.editor.ui.Editor();
editor_content.render("message");
</script>
