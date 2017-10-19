<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption('登录信息')?>
			<div class="portlet-body form">
				<form class="form-horizontal" role="form">
					<div class="form-body">
						<div class="form-group">
							<label class="col-xs-3 control-label">用户标识</label>
							<div class="col-xs-4">
								<p class="form-control-static"><?=htmlspecialchars($_user->id)?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-3 control-label">账号</label>
							<div class="col-xs-4">
								<p class="form-control-static"><?=htmlspecialchars($_user->account)?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-3 control-label">用户名</label>
							<div class="col-xs-4">
								<p class="form-control-static"><?=htmlspecialchars($_user->name)?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-3 control-label">当前IP</label>
							<div class="col-xs-4">
								<p class="form-control-static"><?=$this->input->ip_address()?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-3 control-label">上次登录时间</label>
							<div class="col-xs-4">
								<p class="form-control-static"><?=date('Y年m月d日 H点i分s秒', $_user->last_login_time)?></p>
							</div>
							<div class="col-xs-4"></div>
						</div>
						<div class="form-group">
							<label class="col-xs-3 control-label">上次登录IP</label>
							<div class="col-xs-4">
								<p class="form-control-static"><?=htmlspecialchars($_user->last_login_ip)?></p>
							</div>
							<div class="col-xs-4"></div>
						</div>
						<div class="form-group">
							<label class="col-xs-3 control-label">浏览器信息</label>
							<div class="col-xs-4">
								<p class="form-control-static"><?=htmlspecialchars($this->input->user_agent());?></p>
							</div>
							<div class="col-xs-4"></div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- END SAMPLE FORM PORTLET-->
		<!-- BEGIN SAMPLE FORM PORTLET-->
	</div>
</div>
<!-- END PAGE CONTENT-->
