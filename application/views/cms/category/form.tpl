<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption(l($thisMethod).'文章目录')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form', 'id'=>'form_edit', 'data-category-id'=>$obj->id))?>
					<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $obj->update_time)?>" />
					<div class="form-body">
						<?=edit_form_dropdown('site_id', Array(''=>'请选择网站...')+parse_options($this->sites), set_value('site_id', $obj->site_id), '所属网站')?>
						<?=edit_form_dropdown('model_id', Array(''=>'请选择类型...')+parse_options($models), set_value('model_id', $obj->model_id), '文章类型')?>						
						<div class="form-group <?=form_error('parent_id')?'has-error':''; ?>">
							<label class="col-xs-3 control-label">父目录</label>
							<div class="col-xs-4"><div id="tree_categoies"  style="border: 1px solid #CCC; padding: 6px 12px 6px 12px ;min-height: 34px"></div>
							<input type="hidden" name="parent_id" value="<?=set_value('parent_id', $obj->parent_id) ?>" />
							<?php echo form_error("parent_id", '<p class="help-block">', '</p>'); ?>
							</div>
						</div>
						<?=edit_form_input('code', set_value('code', $obj->code), '代码')?>
						<?=edit_form_input('name', set_value('name', $obj->name), '名称')?>
						<?=edit_form_input('disp_sort', set_value('disp_sort', $obj->disp_sort), '显示顺序')?>
						<?=edit_form_input_group('record_limit', set_value('record_limit', $obj->record_limit), '限制数量', Array('', '<span class="input-group-addon" onclick="$(\'[name=record_limit]\').val(\'-1\');" style="cursor:pointer;">-1 不限制<span>'))?>
					 	<?=edit_form_textarea('note', set_value('note', $obj->note), '目录说明')?>
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
<link href="<?=image_url('/static/assets/global/plugins/jstree/dist/themes/default/style.min.css')?>" rel="stylesheet" type="text/css"/>
<script src="<?=image_url('/static/assets/global/plugins/jstree/dist/jstree.min.js')?>"></script>
<script src="<?=image_url('/static/js/cms_category.js')?>"></script>
<script>
$(document).ready(function(){
	Category.init("<?=site_url('/cms/category/ajaxGetTree' ) . '/' ; ?>");
});
</script>
