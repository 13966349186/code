<?php
	$tmp = Array();
	foreach ($lst as $v){
		$tmp['id'.$v->id] = $v;
	}
?>
<script type="text/javascript">
var data_list = <?=json_encode($tmp)?>;
function selProduct(id){
	$("#add_order_product_div").parents("[role='dialog']").modal('hide');
	addRow(data_list["id"+id]);
}
$(document).ready(function(){
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='type_id']",
		url: "<?=site_url('common/Ajax/GetTypes')?>/{0}",
		empty_option: '请选择类型...',
		init_child_val: "<?=filterValue('type_id')?>"
	});
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='category_id']",
		url: "<?=site_url('common/Ajax/GetCategorys')?>/{0}",
		empty_option: '请选择目录...',
		init_child_val: "<?=filterValue('category_id')?>"
	});
	$('#add_order_product_div').parents('.modal-dialog').addClass('modal-lg');
});
</script>
<div class="modal-body" id="add_order_product_div">
	<div class="portlet">
		<!-- 商品信息开始 -->
		<?=form_open('', Array('class'=>'form-inline', 'role'=>'form'))?>
			<?=search_form_dropdown('game_id',array(''=>'请选择游戏...')+parse_options($this->games),filterValue('game_id'),'游戏', 'class="form-control input-medium"');?>
			<?=search_form_dropdown('category_id',array(''=>'请选择目录...'),filterValue('category_id'),'目录', 'class="form-control input-medium"');?>
			<?=search_form_dropdown('type_id',array(''=>'请选择类型...'),filterValue('type_id'),'类型', 'class="form-control input-medium"');?>
			&nbsp; &nbsp; &nbsp; <button type="submit" class="btn green about_search">搜索 <i class="fa fa-search"></i></button>
		<?=form_close()?>
		<div class="portlet-body">
			<div class="row">
				<div class="col-xs-12">
					<div class="table-scrollable table_color">
						<table class="table table-hover table-bordered table-striped about_table ">
							<thead>
								<tr>
									<th style="">游戏</th>
									<th style="">数据类型</th>
									<th style="width:18%;">目录</th>
									<th style="width:18%;">名称</th>
									<th style="width:10%;">价格</th>
									<th style="width:6%;">排序</th>
									<th style="width:6%;">状态</th>
									<th style="width:7%;"><div style="width:50px;">操作</div></th>
								</tr>
							</thead>
							<tbody>
							<?php $productStatesCls = $this->config->item('product_state_css');?>
							<?php foreach ($lst as $v){?>
								<?php $url = site_url(ioss_extend_route('orderproduct/add', $this->types->getModel($v->type_id)) . '/' .$v->id.'/'.$order_id );?>
								<tr>
									<td><?=htmlspecialchars($this->games[$v->game_id])?></td>
									<td><?=htmlspecialchars($this->types[$v->type_id])?></td>
									<td><?=htmlspecialchars($v->category_name)?></td>
									<td>
									<?php if($p->edit){?>
										<a data-ajax=1 href="<?=$url?>"><?=htmlspecialchars($v->name)?></a>
									<?php }else{?>
										<?=htmlspecialchars($v->name)?>
									<?php }?>
									</td>
									<td><?=$this->currency->format($v->price, DEFAULT_CURRENCY)?></td>
									<td><?=htmlspecialchars($v->sort)?></td>
									<td><span class="<?=element($v->state, $productStatesCls, '')?>"><?=element($v->state,$this->MProduct->states,'') ?></span></td>
									<td>
										<a data-ajax=1 href="<?=$url?>" class="black"><i class="fa fa-check"></i> 选择</a>
									</td>
								</tr>
							<?php }?>
							</tbody>
						</table>
					</div>
					<div class="col-xs-12 text-right">
						<?=$pagination?>
					</div>
				</div>
			</div>
		</div>
		<!-- 商品信息结束 -->
	</div>
</div>
