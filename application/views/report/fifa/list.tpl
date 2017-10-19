<!-- BEGIN PAGE HEADER-->
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<span><?=$ctrlObj->class?></span>
			<i class="fa fa-angle-right"></i>
			<span><?=(isset($thisControllerName) && $thisControllerName)?$thisControllerName:((isset($ctrlObj) && $ctrlObj)?$ctrlObj->name:'')?></span>
		<?php if($category_id){?>
			<i class="fa fa-angle-right"></i>
			<a href="<?=site_url($thisModule.$thisController.'/index/'.$game->code).$params?>"><?=$game->name?></a>
			<i class="fa fa-angle-right"></i>
			【<?=base64_decode(urldecode($category_name))?>】
		<?php }else{?>
			<i class="fa fa-angle-right"></i>
			<?=$game->name?>
		<?php }?>
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
					<div class="form-inline">
						<div class="form-group">
						<?php foreach ($fifa_games as $game_code){?>
							&nbsp; &nbsp;
							<?php 
							$game_names = $this->games->toArray('code','name');
							if($game_code == $game->code && !$category_id){?>
								<?=$game_names[$game_code]?>
							<?php }else{?>
								<a href="<?=site_url($thisModule.$thisController.'/index/'.$game_code).$params?>"><?=$game_names[$game_code]?></a>
							<?php }?>
						<?php }?>
						</div>
					</div>
				</div>
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
					</div>
				</div>
			</div>
			<?=form_close()?> 
			<div class="">
				<table class="table table-striped table-bordered table-hover" id="report_table">
					<thead>
					<tr>
						<th>
						<?php if($category_id){?>
							类型
						<?php }else{?>
							目录
						<?php }?>
						</th>
						<th>订单数量</th>
						<th>金币量</th>
						<th>总价</th>
						<?php foreach ($this->currency as $k=>$v){?>
						<th><?=htmlspecialchars($v)?></th>
						<?php }?>
					</tr>
					</thead>
					<tbody>
					<?php if(isset($lst)){
						$baseUrl = base_url().$thisModule.$thisController.'/index';
							foreach ($lst as $v){?>									
					<tr>
						<td>
					<?php
						if($category_id){
							echo htmlspecialchars($v->type_name);
						}else{
							echo '<a href="'.site_url($thisModule.$thisController.'/index/'.$game->code.'/'.$v->category_id.'/'.urlencode(base64_encode($v->category_name)).$params.'">'.htmlspecialchars($v->category_name)).'</a>';
						}
					?>
						</td>
						<td><?=$v->num?></td>
						<td><?=$v->gold_num?></td>
						<td><?=$this->currency->format($v->amount, DEFAULT_CURRENCY)?></td>
						<?php foreach ($this->currency as $ck=>$vk){?>
						<td><?=$this->currency->format(property_exists($v, $ck)?$v->{$ck}:0, $ck)?></td>
						<?php }?>
					</tr>
					<?php }}?>									
					
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- END PAGE CONTENT-->
<script src="<?=image_url('/static/js/report.js')?>"></script>
<script type="text/javascript">
$(document).ready(function(){
	report.init();
});
function setGame(game_id){
	if($("[name=game_id]").find('option[value='+game_id+']').length < 1){
		return;
	}
	$("[name=game_id]").find('option[value='+game_id+']').attr('selected', 'selected');
	$("#report_sales_form").submit();
}
</script>
