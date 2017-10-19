<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_NO_IOSS_NEW'=>'1'))?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript">
function mychg(selfObj){
	frm = document.forms[0];
	firstObj = null;
	for(var i=0;i<frm.length;i++){
		if(frm[i].type == "password" || frm[i].type == "text" || frm[i].type == "hidden"){
			obj = frm[i];
			idStr = obj.id;
			valueStr = obj.value;
			obj.parentElement.innerHTML = "<input type=\"password\" class=\"form-control\" id=\"" + idStr + "\" name=\"" + idStr + "\" value=\"" + valueStr + "\">";
			if(firstObj == null){
				firstObj = document.getElementById(idStr);
				firstObj.value = "　";
				firstObj.focus();
				firstObj.value = "";
				firstObj.select();
			}
		}
	}
	selfObj.onfocus = null;
}
</script>
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption('修改密码')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<div class="form-body">
						<div class="form-group<?=form_error('oldpwd')?' has-error':''?>">
							<label class="col-xs-3 control-label">原密码</label>
							<div class="col-xs-4">
								<div class="input-group">
									<span class="input-group-addon">
									<i class="fa fa-unlock"></i>
									</span>
									<span><input type="text" onfocus="mychg(this);" class="form-control" id="oldpwd" name="oldpwd" value=""></span>
								</div>
								<?=form_error('oldpwd', '<p class="help-block">', '</p>')?>
							</div>
						</div>
						<div class="form-group<?=form_error('newpwd')?' has-error':''?>">
							<label class="col-xs-3 control-label">新密码</label>
							<div class="col-xs-4">
								<div class="input-group">
									<span class="input-group-addon">
									<i class="fa fa-lock"></i>
									</span>
									<span><input type="text" id="newpwd" name="newpwd" onfocus="mychg(this);" class="form-control" value=""></span>
								</div>
								<?=form_error('newpwd', '<p class="help-block">', '</p>')?>
							</div>
						</div>
						<div class="form-group<?=form_error('newpwdrpt')?' has-error':''?>">
							<label class="col-xs-3 control-label">确认新密码</label>
							<div class="col-xs-4">
								<div class="input-group">
									<span class="input-group-addon">
									<i class="fa fa-lock"></i>
									</span>
									<span><input type="text" id="newpwdrpt" name="newpwdrpt" onfocus="mychg(this);" class="form-control" value=""></span>
								</div>
								<?=form_error('newpwdrpt', '<p class="help-block">', '</p>')?>
							</div>
						</div>
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-xs-offset-3 col-xs-4">
								<button type="submit" class="btn green">保存</button>
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
