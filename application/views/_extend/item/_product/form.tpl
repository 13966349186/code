<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<link href="<?=image_url('/static/assets/global/plugins/jstree/dist/themes/default/style.min.css')?>" rel="stylesheet" type="text/css"/>
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<div class="portlet-title">
				<div class="caption"><i class="fa  fa-cogs"></i><?=($thisMethod=='edit'?'编辑商品':'添加商品'). ' [Item] ' ?></div>
				<div class="actions">
				<?php if($thisMethod=='edit'){?>
					<a href="<?=site_url($thisModule . $thisController . '/add/' . $vo->type_id . '?copy=' .$vo->id) ?>" class="btn  btn-default  btn-sm" ><i class="fa fa-plus"></i> 复制 </a>	
					<a href="javascript:;" class="btn btn-default btn-sm" id="btn-delete" data-product-id="<?=$vo->id ?>"><i class="fa fa-trash-o"></i> 删除 </a>
				<?php }?>
				</div>
			</div>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $vo->update_time)?>" />
					<div class="form-body">
						<?=edit_form_static('游戏', $game->name.' ['.$game->code.']')?>
						<?=edit_form_static('类型', $type->name.' ['.$type->code.']')?>
						<div class="form-group <?=form_error('category_id')?'has-error':''; ?>">
							<label class="col-xs-3 control-label">目录</label>
							<div class="col-xs-4">
							<div id="tree_categoies"  class="category-tree"></div>
							<input type="hidden" name="category_id" value="<?=set_value('category_id', $vo->category_id) ?>" />
							<?php echo form_error("category_id", '<p class="help-block">', '</p>'); ?>
							</div>
						</div>
						<?=edit_form_input('name', set_value('name', $vo->name), '名称')?>
						<?=edit_form_textarea('description', set_value('description', $vo->description), '描述')?>
						<?=edit_form_input('sort', set_value('sort', $vo->sort), '排序')?>
						<?=edit_form_input_group('price', set_value('price', $vo->price), '价格', '<span class="input-group-addon"><i class="fa fa-'.strtolower(DEFAULT_CURRENCY).'"></i></span>')?>
						<?=edit_form_radio_list('state', $this->MProduct->states, set_value('state', $vo->state), '状态')?>
						<?=edit_form_input('stock', set_value('stock', $vo->stock), '库存')?>
						<?=edit_form_uploader('image', set_uploader('image', $vo->image), '图片') ?>
				 </div>
			</div>
						</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-sm-offset-3 col-sm-4">
								<?php if($p->$thisMethod){?><button type="submit" class="btn green">保　存</button><?php }?>
									<input type="button" class="btn default" onclick="location.href='<?=site_url($product_ctrl) ?>';" value="取　消" />
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
<script src="<?=image_url('/static/assets/global/plugins/jstree/dist/jstree.min.js')?>"></script>
<script src="<?=image_url('/static/js/product.js')?>"></script>
<script type="text/javascript">
	$(document).ready(function(){
		Category.init( "<?=site_url('/common/Ajax/getCategoryTree' ) . '/' ; ?>","<?=$game->id ?>");
		AjaxUploader.uploadTempUrl = "<?=site_url('/common/AjaxUpload/upload') . '/'  ?>";
		AjaxUploader.init();
		$('#btn-delete').on('click',function(){
			if(confirm('确定要删除当前商品？')){
				location.href = '<?=site_url($this->_thisModule . $this->_thisController . '/delete' ) ?>/' + $(this).data('product-id') + '/' +$('#update_time').val();
			}
		});
	});
</script>