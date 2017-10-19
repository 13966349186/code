<!-- BEGIN PAGE HEADER-->
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<span><?=$ctrlObj->class?></span>
			<?php
			$baseUrl = base_url().$thisModule.$thisController.'/index';
			$time_param = '?'.http_build_query(Array('create_begin'=>filterValue('create_begin'), 'create_end'=>filterValue('create_end')));
			$args = '';//根据查询条件，拼接基本的参数链接
			$last_name = $thisControllerName;
			//面包霄的链接，将查询条件拼的参数，往前错位显示，当前条件作为group项拼入链接
			foreach ($where as $k=>$v){
				echo '<i class="fa fa-angle-right"></i> <a href="'.$baseUrl.$args.'/'.urlencode(base64_encode($k)).$time_param.'">'.htmlspecialchars($last_name).'</a>';
				$last_name = $names[$k];
				$args .= '/'.urlencode(base64_encode(($v.$k.'-'.$names[$k])));
			}
			if(!$last_name){$last_name = $thisControllerName;}//条件最后一项只显示成文本，不做链接
			?>
			<i class="fa fa-angle-right"></i> <?=htmlspecialchars($last_name)?>
		</li>
	</ul>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<div class="portlet">
			<?=form_open('', Array('class'=>'portlet-body', 'method'=>'get', 'id'=>'report_sales_form'))?> 
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
						</div>
						<div class="form-group pull-right" style="margin-right:30px;">
							<div class="btn-group btn-group-devided" data-toggle="buttons">
							<?php foreach ($struct as $k=>$v){?>
								<label class="btn btn-transparent grey-salsa btn-circle btn-sm  <?=$k==$group?'active':'' ?>" >
									<input type="radio" class="toggle"  name="sub_type" value="<?=htmlspecialchars($baseUrl.$args.'/'.urlencode(base64_encode($k)).$time_param)?>"><?=$all[$k]?></label>
							<?php }?>
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
						<th><?=array_key_exists($group, $time_keys)?'日期':$all[$group]?></th>
						<th width="10%">订单数量</th>
						<th width="10%">总价格</th>
						<th width="8%">处理中</th>
						<th width="8%">问题单</th>
						<th width="8%">关闭</th>
						<th width="8%">欺诈</th>
						<th width="8%">未发货</th>
						<th width="10%">部分发货</th>
						<th width="8%">已发货</th>
					</tr>
					</thead>
					<tbody>
					<?php 
					if(isset($lst)){
							$baseUrl = base_url().$thisModule.$thisController.'/index';
							foreach ($lst as $v){ 
								$titles[] = $v->disp_name;
								$nums[] = isset($chart_type)?(int)$v->num: array($v->disp_name,(int)$v->num);
								$amounts[] =isset($chart_type)? (float)$v->amount :  array($v->disp_name, (float)$v->amount);
					?>
					<tr>
						<td>
						<?php if(!array_key_exists($group, $time_keys) && $struct[$group]){?> 
							<a href="<?=$baseUrl.$args.'/'.urlencode(base64_encode($v->disp_id.$group.'-'.$v->disp_name)).$time_param?>"><?=htmlspecialchars($v->disp_name)?></a>
						<?php }else{?> 
							<?=htmlspecialchars($v->disp_name)?> 
						<?php }?> 
						</td>
						<td><?=$v->num?></td>
						<td><?=$this->currency->format($v->amount, DEFAULT_CURRENCY)?></td>
						<td><?=$v->state_open?></td>
						<td><?=$v->state_holding?></td>
						<td><?=$v->state_closed?></td>
						<td><?=$v->risk_fraud?></td>
						<td><?=$v->not_delivered?></td>
						<td><?=$v->part_delivered?></td>
						<td><?=$v->delivered?></td>
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
				<h4 class="modal-title">销售报表 - <?= element($group, $all, '')?></h4>
			</div>
			<div class="modal-body">
				<?php 
					if($group == 'r_day' || $group =='r_week' ||  $group =='r_month'){ 
					$chart_type = 'line';
					?>
					<div class="row"><div class="col-xs-12">
							<div class="portlet box blue-hoki">
								<div class="portlet-title">
									<div class="caption"><i class="fa fa-bar-chart-o"></i>销售报表</div>
								</div>
									<div class="portlet-body">
									<div id="chart-container" class="chart" style="height: 500px;"></div>
								</div>
							</div>
					</div></div>
					<?php }else{?>
					<div class="row">
					<div class="col-xs-6">
							<div class="portlet box blue-hoki">
								<div class="portlet-title">
									<div class="caption"><i class="fa fa-bar-chart-o"></i>订单数量</div>
								</div>
									<div class="portlet-body"><div id="chart-container-num" class="chart" style="height: 500px;"></div></div>
							</div>
					</div>
					<div class="col-xs-6">
							<div class="portlet box blue-hoki">
								<div class="portlet-title">
									<div class="caption"><i class="fa fa-bar-chart-o"></i>销售额</div>
								</div>
									<div class="portlet-body"><div id="chart-container-amount" class="chart" style="height: 500px;"></div></div>
							</div>
					</div>
					</div>
					<?php }?>
			</div>
		</div>
	</div>
</div>
<!-- /.modal -->
<script src="<?=image_url('/static/js/report.js')?>"></script>
<script src="<?=image_url('/static/assets/global/plugins/Highcharts-4.2.5/js/highcharts.js')?>"></script>
<script src="<?=image_url('/static/assets/global/plugins/Highcharts-4.2.5/js/themes/ioss.js')?>"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('input[name=sub_type]').change(function(){
		location.href = $(this).val();
	});
	report.init();
});
</script>
<?php if(isset($chart_type) && $chart_type == 'line'){?>
<script type="text/javascript">
var titles = <?= isset($titles)?json_encode($titles):'[]';?>;
var amounts = <?= isset($amounts)?json_encode($amounts):'[]';?>;
var nums = <?= isset($nums)?json_encode($nums):'[]';?>;
$('#charts').on('shown.bs.modal', function(){
	//订单数量图
	$("#chart-container").highcharts({
		tooltip: {
			pointFormat: '{series.name}: {point.y:2f}',
		},
		xAxis: {  //x轴
			categories: titles
		},
		yAxis: [
			{
				min:0,
				title: { text: '订单数量'}
			},
			{
				min:0,
				title: { text: '金额 （$）'},
				opposite:true
			}
		],
		series: [{
			yAxis:0,
			name: '订单数量',
			data: nums
		},
		{
			yAxis:1,
			name: '销售额',
			data: amounts
		}]
	});
});
</script>
<?php }else{ ?>
<script type="text/javascript">
var amounts = <?= isset($amounts)?json_encode($amounts):'[]';?>;
var nums = <?= isset($nums)?json_encode($nums):'[]';?>;
$('#charts').on('shown.bs.modal', function(){
	//饼图
	$("#chart-container-num").highcharts({
		tooltip: {
			pointFormat: '{series.name}: {point.y} ({point.percentage:.1f}%)',
		},
		series: [{
			type: 'pie',
			name: '订单数量',
			data: nums
		}]
	}); 
	$("#chart-container-amount").highcharts({
		tooltip: {
			pointFormat: '{series.name}: {point.y} ({point.percentage:.1f}%)',
		},
		series: [{
			type: 'pie',
			name: '销售额',
			data: amounts
		}]
	}); 
});
</script>
<?php }?>