<?php $myId = 'myId'.time().rand(1, 10000);?>
<div class="modal-body" id="<?=$myId?>">
	<div class="portlet-body form">
		<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
			<div class="form-body">
				<?=edit_form_static('游戏', $game->name)?>
				<?=edit_form_static('类型', $type->name)?>
				<?=edit_form_dropdown('category_id', Array(''=>'请选择目录...')+parse_options($categorys), set_value('category_id', $product->category_id), '目录')?>
				<?=edit_form_dropdown('product_id', Array(''=>'请选择商品...'), '', '商品')?>
				<div class="form-group">
					<label class="col-xs-3 control-label">金币数量</label>
					<div class="col-xs-7">
						<p class="form-control-static"><strong id="gold_num_id"></strong></p>
					</div>
				</div>
				<div class="form-group<?=form_error('price')?' has-error':''?>">
					<label class="col-xs-3 control-label">价格</label>
					<div class="col-xs-7">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-<?=strtolower(DEFAULT_CURRENCY)?>"></i></span>
							<input id="price" class="form-control" type="text" value="<?=set_value('price', $product->price)?>" name="price"<?=form_error('price')?'':' readonly="true"'?> />
							<span class="input-group-addon" onclick="$('#price').attr('readonly', !$('#price').attr('readonly'));" style="cursor:pointer;">修改</span>
						</div>
						<?=form_error('price', '<p class="help-block">', '</p>')?>
					</div>
				</div>
				<?=edit_form_input('num', set_value('num', 1), '商品数量')?>
				<div class="form-group">
					<label class="col-xs-3 control-label">总价</label>
					<div class="col-xs-7">
						<p class="form-control-static"><strong id="price_total"></strong></p>
					</div>
				</div>

				<div class="form-group">
					<div class="col-xs-3"></div>
					<div class="col-xs-7">
			   		   <button type="submit" class="btn green">添加</button>
					   &nbsp; &nbsp; &nbsp; &nbsp; <a type="button" data-ajax=1 class="btn default" href="<?=site_url('sales/orderproduct/add_pre/0')?>">取消</a>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		<?=form_close()?>
	</div>
</div>
<script type="text/javascript">
$('.modal-body').find('.col-xs-4').removeClass('col-xs-4').addClass('col-xs-7');
$('.modal-body').find('textarea').attr('rows', '4');
$('#<?=$myId?>').parents('.modal-dialog').removeClass('modal-lg');

window.sel_product = <?=isset($sel_product)?json_encode($sel_product):'false'?>;
window.sel_json = "<?=isset($sel_json)?$sel_json:''?>";
//下拉框改变后修改价格
function refreshPrice(){
	var opt = $("[name='product_id']").find("option:selected");
	var price = 0;
	var gold_num = 0;
	if(opt.length > 0){
		price = opt.attr('data-price')*1;
		gold_num = opt.attr('data-gold_num')*1;
	}
	if(price > 0){
		$("#gold_num_id").html(opt.attr('data-gold_num'));
		$("[name='price']").val(price);
		$("#price_total").html( price * $("[name='num']").val() );
	}else{
		$("#gold_num_id").html('');
		$("[name='price']").val('');
		$("#price_total").html('');
	}
}
//价格商品数量修改后，重新写总价
function calcPrice(){
	var price = $("[name='price']").val()*1;
	var gold_num = $("[name='num']").val()*1;
	if(price > 0){
		$("#price_total").html( price * $("[name='num']").val() );
	}else{
		$("#price_total").html('');
	}
}
$(document).ready(function(){
	$("[name='product_id']").change(function(){
		refreshPrice();
	});
	$("[name='num']").add("[name='price']").keyup(function(){
		calcPrice();
	});
	$("[name='num']").add("[name='price']").keydown(function(){
		calcPrice();
	});
	$("[name='category_id']").RelativeDropdown({
		childid: "[name='product_id']",
		url: "<?=site_url($thisModule.$thisController.'/AJAX_readProducts')?>/{0}",
		empty_option: '请选择商品...',
		init_child_val: "<?=set_value('product_id', $product->id)?>",
		extern_attr:['price', 'gold_num']
	});
	if(window.sel_product){
		cartAddRow(window.sel_product, window.sel_json);
		$('#<?=$myId?>').parents("[role='dialog']").modal("hide");
	}
});
</script>
