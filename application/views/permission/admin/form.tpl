<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script language="javascript">
function setPwdType(obj){
	idStr = obj.id;
	objHtml = "<input type=\"password\"";
	objHtml += " id=\"" + obj.name + "\"";
	objHtml += " name=\"" + obj.name + "\"";
	if(obj.value){
		objHtml += " value=\"" + obj.value + "\"";
	}
	if(obj.className){
		objHtml += " class=\"" + obj.className + "\"";
	}
	objHtml += " />";
	obj.parentElement.innerHTML = objHtml;
	firstObj = document.getElementById(idStr);
	if(firstObj != null){
		firstObj.value = "　";
		firstObj.focus();
		firstObj.value = "";
		firstObj.select();
	}
}
</script>
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption(l($thisMethod).'帐号')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<input type="hidden" id="update_time" name="update_time" value="<?= set_value('update_time', property_exists($admin, 'update_time')?$admin->update_time:'')?>" />
					<div class="form-body">
					<?php 
					$addon = '<span class="input-group-addon"><i class="fa fa-user"></i></span>';
					?>
						<?=edit_form_input_group('account',set_value('account',$admin->account),l('account'), $addon, $thisMethod=='edit'?'disabled':''); ?>
						<?=edit_form_input_group('name', set_value('name',$admin->name),  '用户名', $addon)?>
						<div class="form-group<?=form_error('password')?' has-error':''?>">
							<label class="col-xs-3 control-label">密码</label>
							<div class="col-xs-4">
								<div class="input-group">
									<span class="input-group-addon">
									<i class="glyphicon glyphicon-lock"></i>
									</span>
									<span><input type="text" class="form-control" onfocus="setPwdType(this);" id="password" name="password" value=""></span>
								</div>
								<?=form_error('password', '<p class="help-block">', '</p>')?>
							</div>
							<?php if($thisMethod == 'edit'){?><div class="col-xs-5 edit_password">*留空则不更改密码</div><?php }?>
						</div>
						<?=edit_form_dropdown('role_id', Array(''=>'选择角色...')+$roles, set_value('role_id', $admin->role_id), '角色') ?>
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
