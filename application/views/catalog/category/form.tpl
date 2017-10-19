<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption(l($thisMethod).'商品目录')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form', 'id'=>'form_edit', 'data-category-id'=>$obj->id))?>
					<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $obj->update_time)?>" />
					<div class="form-body">
						<?=edit_form_dropdown('game_id', Array(''=>'请选择游戏...')+parse_options($this->games), set_value('game_id', $obj->game_id), '游戏', ($thisMethod=='edit'?'disabled="disabled"':''))?>
						<?=edit_form_input('name', set_value('name', $obj->name), '名称')?>
						<?=edit_form_input('code', set_value('code', $obj->code), '标识', ($thisMethod=='edit'?'disabled="disabled"':''))?>
						<div class="form-group <?=form_error('parent_id')?'has-error':''; ?>">
							<label class="col-xs-3 control-label">父目录</label>
							<div class="col-xs-4"><div id="tree_categoies"  style="border: 1px solid #CCC; padding: 6px 12px 6px 12px ;min-height: 34px"></div>
							<input type="hidden" name="parent_id" value="<?=set_value('parent_id', $obj->parent_id) ?>" />
							<?php echo form_error("parent_id", '<p class="help-block">', '</p>'); ?>
							</div>
						</div>
						<?=edit_form_textarea('description', set_value('description', $obj->description), '描述')?>
						<?=edit_form_radio_list('state', Array(MCategory::STATE_ENABLE=>'启用', MCategory::STATE_DISABLE=>'禁用'), set_value('state', ($thisMethod == 'edit'?$obj->state:MCategory::STATE_DISABLE)), '状态')?>
					<?php $this->load->view('partials/submitButtons.tpl')?>
					</div>
				<?=form_close()?>
			</div>
		</div>
		<!-- END SAMPLE FORM PORTLET-->
	</div>
</div>
<!-- END PAGE CONTENT-->
<link href="<?=image_url('/static/assets/global/plugins/jstree/dist/themes/default/style.min.css')?>" rel="stylesheet" type="text/css"/>
<script src="<?=image_url('/static/assets/global/plugins/jstree/dist/jstree.min.js')?>"></script>
<script src="<?=image_url('/static/js/category.js')?>"></script>
<script type="text/javascript">
	$(document).ready(function(){
		Category.init("<?=site_url('/common/Ajax/getCategoryTree' ) . '/' ; ?>");
	});
</script>