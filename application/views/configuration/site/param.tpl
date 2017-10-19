<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption('网站配置')?>
			<div class="portlet-body form">
				<?=form_open('r', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<div class="form-body">
					<?php
					$tmpGroup = null;
					$group_id = false;
					foreach ($defs as $def){
						if($group_id !== false && $group_id != $def->group_id){
							echo '<hr style="border-color:#ddd;">';
						}
						$group_id = $def->group_id; 
						if($def->data_type == '2'){
							echo edit_form_dropdown($def->config_key,$def->selectList,set_value($def->config_key,$def->value),$def->name );
						}else if($def->data_type == '1' || (isset($def->mule_line) && $def->mule_line)){
							echo edit_form_textarea($def->config_key, set_value($def->config_key,$def->value),$def->name);
						}else{
							echo edit_form_input($def->config_key, set_value($def->config_key,$def->value), $def->name);
						}
					}
					?>
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

