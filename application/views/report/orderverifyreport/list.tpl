<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<div class="portlet">
			<?=form_open('', Array('class'=>'portlet-body',  'id'=>'report_sales_form'))?> 
			<div class="portlet-body">
				<div class="form-group">
					<div class="form-inline" role="form">
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
						<div class="form-group">
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#charts">图表</button>
						</div>
						<div class="form-group pull-right">
						<div class="btn-group btn-group-devided" data-toggle="buttons">
						<?php foreach ($types as $k=>$v){ ?>
							<label class="btn btn-transparent grey-salsa btn-circle btn-sm <?= $k==$type?'active':''?>"><input type="radio" class="toggle" name="type" value="<?=$k ?>"><?=$v ?></label>
						<?php }?>
						</div>
						</div>
						<div class="form-group pull-right" style="margin-right:30px;">
							<div class="btn-group btn-group-devided" data-toggle="buttons">
							</div>
						</div>
					</div>
				</div>
			</div>
			<?=form_close()?> 
			<div >
				<table class="table table-striped table-bordered table-hover" id="report_table">
					<thead>
					<tr>
						<th width="50%"><?=element($type, $types, '标题') ?></th>
						<th width="50%">验证数量</th>
					</tr>
					</thead>
					<tbody>
					<?php if(isset($lst)){
						foreach ($lst as $v){
							$data[] = array($v->title, (int)$v->num);
					?> 
					<tr>
						<td><?= $v->title ?>	</td>
						<td><?= $v->num ?></td>
					</tr>
					<?php }}?>									
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- END PAGE CONTENT-->
<!-- /.modal -->
<div class="modal fade" id="charts" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-full">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">订单验证统计</h4>
			</div>
			<div class="modal-body" >
				<div id="chart-container" style=" height:600px"></div>
			</div>
		</div>
	</div>
</div>
<!-- /.modal -->
<script src="<?=image_url('/static/js/report.js')?>" ></script>
<script src="<?=image_url('/static/assets/global/plugins/Highcharts-4.2.5/js/highcharts.js')?>"></script>
<script src="<?=image_url('/static/assets/global/plugins/Highcharts-4.2.5/js/themes/ioss.js')?>"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('input[name=type]').change(function(){
		location.href = "<?= site_url($thisModule.$thisController.'/index')?>" + "/" + $(this).val();
	});
	report.init();
	//饼图
	$('#charts').on('shown.bs.modal', function (e) {
		$("#chart-container").highcharts({
				tooltip: {
					pointFormat: '{series.name}:{point.percentage:.1f}% ({point.y})',
				},
				series: [{
					type: 'pie',
					name: '验证订单',
					data: <?= isset($data)?json_encode($data):'[]';?>
				}]
			}); 
	});
});
</script>