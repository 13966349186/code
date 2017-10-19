<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row" id="mmmmmm">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption('邮件日志详细')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<div class="form-body">
						<div class="form-group">
							<label class="col-xs-3 control-label"><strong>网站</strong></label>
							<div class="col-xs-3"><p class="form-control-static">
								<?=htmlspecialchars($this->sites[$obj->site_id])?>
							</p></div>
							<label class="col-xs-2 control-label"><strong>操作人</strong></label>
							<div class="col-xs-4"><p class="form-control-static">
								<?=htmlspecialchars($obj->admin)?>
							</p></div>
						</div>
						<div class="form-group">
							<label class="col-xs-3 control-label"><strong>模板</strong></label>
							<div class="col-xs-3"><p class="form-control-static">
								<?=htmlspecialchars(element($obj->tmp_code, $tmplates, $obj->tmp_code))?>
							</p></div>
							<label class="col-xs-2 control-label"><strong>发送状态</strong></label>
							<div class="col-xs-4"><p class="form-control-static">
								<?=$obj->send_state==1?'成功':'<font color=red>失败</font>'?>
							</p></div>
						</div>
						<div class="form-group">
							<label class="col-xs-3 control-label"><strong>收件人</strong></label>
							<div class="col-xs-3"><p class="form-control-static">
								<?=htmlspecialchars($obj->email_to)?>
							</p></div>
							<label class="col-xs-2 control-label"><strong>发送时间</strong></label>
							<div class="col-xs-4"><p class="form-control-static">
								<?=date('Y-m-d H:i:s', $obj->create_time)?>
							</p></div>
						</div>
						<div class="form-group">
							<label class="col-xs-3 control-label"><strong>标题</strong></label>
							<div class="col-xs-7"><p class="form-control-static">
								<?=$obj->email_subject?>
							</p></div>
						</div>
						<div class="form-group">
							<label class="col-xs-3 control-label"><strong>内容</strong></label>
							<div class="col-xs-7"><p class="form-control-static">
								<?=$obj->email_message?>
							</p></div>
						</div>
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-xs-offset-3 col-xs-4">
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
