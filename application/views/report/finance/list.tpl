<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl',array('_NO_IOSS_NEW'=>1))?> 
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<div class="portlet">
			<?=form_open('', Array('class'=>'portlet-body'))?> 
			<div class="portlet-body">
				<div class="form-group">
					<div class="form-inline" role="form">
						<?=search_form_dropdown('currency', array(''=>'所有币种...')+parse_options($this->currency), filterValue('currency'),'币种', 'class="form-control input-medium"');?>
						<div class="form-group">
							<div class="input-group input-large date-picker input-daterange" data-date="" data-date-format="yyyy-mm-dd">
								<input type="text" class="form-control" name="create_begin" value="<?=filterValue('create_begin')?>" placeholder="开始时间">
								<span class="input-group-addon">
								to </span>
								<input type="text" class="form-control" name="create_end" value="<?=filterValue('create_end')?>" placeholder="结束时间">
							</div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn green"><i class="fa fa-search"></i> 搜索</button>
						</div>
					<div class="form-group pull-right">
						<div class="btn-group btn-group-devided" data-toggle="buttons">
							<label class="btn btn-transparent grey-salsa btn-circle btn-sm  <?=$type=='site'?'active':'' ?>" >
								<input type="radio" class="toggle"  name="type" value="site">网站统计</label>
							<label class="btn btn-transparent grey-salsa btn-circle btn-sm <?=$type!='site'?'active':'' ?>">
								<input type="radio" class="toggle" name="type" value="game">游戏统计</label>
						</div>
					</div>
					</div>
				</div>

				<table class="table table-striped table-bordered table-hover" id="report_table">
					<thead>
					<tr>
						<th><?=$type=='site'?'网站':'游戏'?></th>
						<th>订单数量</th>
						<th>币种</th>
						<th>金额</th>
						<th>手续费</th>
					</tr>
					</thead>
					<tbody>
					<?php if(isset($lst)){
							foreach ($lst as $v){?>									
					<tr>
						<td>
							<?=htmlspecialchars($type=='site'?$this->sites[$v->site_id]:$this->games[$v->game_id])?>
						</td>
						<td><?=$v->num?></td>
						<td><?=$this->currency[$v->currency]?></td>
						<td><?=$this->currency->format($v->amount, $v->currency)?></td>
						<td><?=$this->currency->format($v->fee, $v->currency)?></td>
					</tr>
					<?php }}?>									
					</tbody>
				</table>
			</div>
			<?=form_close()?> 
			<div >
			</div>
		</div>
	</div>
</div>
<!-- END PAGE CONTENT-->
<script src="<?=image_url('/static/js/report.js')?>"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('input[name=type]').change(function(){
		location.href = "<?= site_url($thisModule.$thisController.'/index')?>" + "/" + $(this).val() + "<?=$params?>";
	});
	report.init();
});
</script>