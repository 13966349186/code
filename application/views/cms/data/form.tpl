<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">文章内容</h3>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<!-- span>网站【<?=$site->name?>】的CMS内容</span -->
		<?php if(isset($category) && $category){?>
			<span><a href="<?=site_url($thisModule.$thisController.'/index/'.$site->id)?>"><?=htmlspecialchars($site->name)?></a></span>
		<?php }else{?>
			<span><?=htmlspecialchars($site->name)?></span>
		<?php }?>
		<?php foreach ($parents as $v){?>
			<i class="fa fa-angle-right"></i> <span><a href="<?=site_url($thisModule.$thisController.'/index/'.$site->id.'/'.$v->id)?>">目录[<?=htmlspecialchars($v->name)?>]</a></span>
		<?php }?>
		<?php if(isset($category) && $category){?>
			<i class="fa fa-angle-right"></i> <span><a href="<?=site_url($thisModule.$thisController.'/index/'.$site->id.'/'.$category->id)?>"><?=htmlspecialchars($category->name)?></a></span>
		<?php }?>
			<i class="fa fa-angle-right"></i>
			<span><?=l($thisMethod)?></span>
		</li>
	</ul>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript" src="<?=image_url('static/js/ajaxfileupload.js')?>"></script>
<script src="<?=image_url('/plugin/ueditor/ueditor.config.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/plugin/ueditor/ueditor.all.min.js')?>" type="text/javascript"></script>
<link rel="Stylesheet" type="text/css" href="<?=image_url('/plugin/ueditor/themes/default/css/ueditor.css')?>" />
<script>
var uping = false;
function beforeSubmit(){
	//防止重复提交
	if(uping){return false;}
	uping = true;
	return true;
}
</script>
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption(l($thisMethod).'目录【'.htmlspecialchars($category->name).'】下的文章')?>
			<div class="portlet-body form">
				<?=form_open_multipart('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<input type="hidden" name="update_time" id="update_time" value="<?=$obj->update_time?>" />
					<div class="form-body">
					<?php if($category->note){?>
						<div class="alert alert-block alert-info"><h4 class="alert-heading">目录说明</h4><p><?=nl2br( html_escape($category->note))  ?></p>	</div>
					<?php }?>
						<?=edit_form_input('title', set_value('title', $obj->title), '标题')?>
						<?=edit_form_input('code', set_value('code', $obj->code), '标识', empty($obj->code)?'':'disabled="disabled"') ?>
						<?php 
						foreach($list as $key=>$value){
							$default_value = isset($obj->{$value->col_name})?$obj->{$value->col_name}:null;
							echo cms_form_input($value->data_type, $value->col_name,$value->disp_name,$value->data_format, $default_value);
						 }?> 
						<?=edit_form_radio_list('status', Array('1'=>'正常', '0'=>'禁用'), set_value('status', ($thisMethod == 'edit'?$obj->status:'0')), '状态')?> 
						<div class="form-group<?=form_error('publish_time')?' has-error':''?>">
							<label class="col-xs-3 control-label">发布时间</label>
							<div class="col-xs-4">
								<input type="text" class="form-control datetime-picker" name="publish_time" data-date-format="yyyy-mm-dd hh:ii:ss"  value="<?=@date('Y-m-d H:i:s', set_value('publish_time', $obj->publish_time))?>" />
								<?=form_error('publish_time', '<p class="help-block">', '</p>')?>
							</div>
						</div>
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-xs-offset-3 col-xs-4">
								<?php if($p->$thisMethod){?><button type="submit" class="btn green">保存</button><?php }?>
								<a href="<?=site_url($thisModule.$thisController.'/index/'.$site->id.'/'.$category->id)?>" class="btn default">返回</a>
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
<script type="text/javascript">
	$(document).ready(function(){
		AjaxUploader.uploadTempUrl = "<?=site_url('/common/AjaxUpload/upload') . '/'  ?>";
		AjaxUploader.init();
	});
</script>
