<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
		<!-- BEGIN FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption('价格批量更新')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form', 'id'=>'batchprice'))?>
					<div class="form-body">
						<div class="page-bar">
								<ul class="page-breadcrumb">
									<li><i class="fa  fa-asterisk"></i> <?=htmlspecialchars($game->name)?> (<?=htmlspecialchars($type->name)?>)
									<?php
									foreach ($path as $v){
										echo '<i class="fa fa-angle-right"></i> ' .htmlspecialchars($v->name);
									}
									?>
									</li>
								</ul>
								<div class="page-toolbar">
									<div class="btn-group pull-right" data-toggle="buttons">
										<label class="btn btn-default active"><input name="synflag" id="synflag"  type="checkbox" class="toggle"  checked="checked" > 同步修改</label>
									</div>
								</div>
						</div>
						<div class="alert alert-danger alert alert-danger display-none" <?=form_error('price')?'style="display:block"':'' ?> >
							<button class="close" data-dismiss="alert"></button><?=form_error('price') ?>
						</div>
							<table class="table table-hover table-bordered table-striped">
								<thead>
									<tr>
										<th style="width:8%;">ID</th>
										<th style="width:35%;">名称</th>
										<th style="width:15%;">金币数量</th>
										<th style="width:15%;">折扣率</th>
										<th style="width:8%;">排序</th>
										<th >价格</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach ($lst as $g){?>
									<tr>
										<td><?=$g->product_id?></td>
										<td><?=$g->name?></td>
										<td><?=$g->gold_num?></td>
										<td><?=$g->discount?> %</td>
										<td><?=$g->sort?></td>
										<td>
											<input type="text"  value="<?=set_value("price" , $g->price)?>" name="price[<?=$g->product_id?>]" data-gold-num="<?=$g->gold_num?>" data-discount="<?=$g->discount?>" class="form-control  input-sm price-input" >
											<input type="hidden" value="<?=set_value('update_time', $g->update_time) ?>" name="update_time[<?=$g->product_id?>]">
										</td>
									</tr>
								<?php }?>
								</tbody>
						</table>
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-xs-offset-3 col-xs-4">
								<?php if($lst){?><button type="submit" class="btn green">保存价格</button><?php }?>
								<a href="<?=$back_url?>" class="btn default">返回</a>
							</div>
						</div>
					</div>
				<?=form_close()?>
			</div>
		</div>
		<!-- END FORM PORTLET-->
<script type="text/javascript" src="<?=image_url('/static/assets/global/plugins/jquery-validation/js/jquery.validate.min.js')?>"></script>
<script type="text/javascript" src="<?=image_url('/static/js/bachprice.js')?>"></script>
<script>
jQuery(document).ready(function() {
	Page.init();
});
</script>