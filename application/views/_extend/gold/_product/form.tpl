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
				<div class="caption"><i class="fa  fa-cogs"></i><?=($thisMethod=='edit'?'编辑商品':'添加商品') . ' [Gold] ' ?></div>
				<div class="actions">
				<?php if($thisMethod=='edit'){?>
					<a href="<?=site_url($thisModule . $thisController . '/add/' . $product->type_id . '?copy=' .$product->id) ?>" class="btn  btn-default  btn-sm" ><i class="fa fa-plus"></i> 复制 </a>	
					<a href="javascript:;" class="btn btn-default btn-sm" id="btn-delete" data-product-id="<?=$product->id ?>"><i class="fa fa-trash-o"></i> 删除 </a>
				<?php }?>
				</div>
			</div>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $product->update_time)?>" />
					<div class="form-body">
						<?=edit_form_static('游戏', $game->name.' ['.$game->code.']')?>
						<?=edit_form_static('类型', $type->name.' ['.$type->code.']')?>
						<div class="form-group <?=form_error('category_id')?'has-error':''; ?>">
							<label class="col-xs-3 control-label">目录</label>
							<div class="col-xs-4">
							<div id="tree_categoies"  class="category-tree"></div>
							<input type="hidden" name="category_id" value="<?=set_value('category_id', $product->category_id) ?>" />
							<?php echo form_error("category_id", '<p class="help-block">', '</p>'); ?>
							</div>
						</div>
						<?=edit_form_input('name', set_value('name', $product->name), '名称')?>
						<?=edit_form_textarea('description', set_value('description', $product->description), '描述')?>
						<?=edit_form_input('sort', set_value('sort', $product->sort), '排序')?>
						<?=edit_form_input_group('price', set_value('price', $product->price), '价格', '<span class="input-group-addon"><i class="fa fa-'.strtolower(DEFAULT_CURRENCY).'"></i></span>')?>
						<?=edit_form_input_group('discount', set_value('discount', $gold->discount), '折扣率', Array('', '<span class="input-group-addon">%</span>'))?>
						<?=edit_form_input('gold_num', set_value('gold_num', $gold->gold_num), '数量')?>
						<?=edit_form_radio_list('state', $this->MProduct->states, set_value('state', $product->state), '状态')?>
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
		$('#btn-delete').on('click',function(){
			if(confirm('确定要删除当前商品？')){
				location.href = '<?=site_url($this->_thisModule . $this->_thisController . '/delete' ) ?>/' + $(this).data('product-id') + '/' +$('#update_time').val();
			}
		});
	});
</script>