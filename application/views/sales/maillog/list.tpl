<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_NO_IOSS_NEW'=>1)); ?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript">
$(document).ready(function(){
	$("#reset").click(function(){
		$("select").val('');
		$("input[type='text']").val('');
	});
});
</script>
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<?=form_open('', Array('class'=>'portlet-body'))?> 
			<div class="form-group">
				<div class="form-inline" role="form">
					<?=search_form_dropdown('site_id', array(''=>'选择网站...')+parse_options($this->sites), filterValue('site_id'),'网站');?>
					<?=search_form_dropdown('tmp_code', array(''=>'选择模板...')+$tmplates, filterValue('tmp_code'),'模板');?>
					<?=search_form_dropdown('send_state', array(''=>'选择状态...')+Array('0'=>'失败', '1'=>'成功'), filterValue('send_state'),'状态');?>
				</div>
			</div>
			<div class="form-inline" role="form">
				<div class="form-group">
					<input type="text" class="form-control input-large	form-filter"
						value="<?=filterValue('email_to')?>" name="email_to" placeholder="收件人邮箱">
					<div class="input-group input-large date-picker input-daterange"
						data-date="" data-date-format="yyyy-mm-dd">
						<input type="text" class="form-control" name="create_begin"
							value="<?=filterValue('create_begin')?>" placeholder="起始时间"> <span
							class="input-group-addon"> to </span> <input type="text"
							class="form-control" name="create_end"
							value="<?=filterValue('create_end')?>" placeholder="结束时间">
					</div>
					<button type="button" class="btn default" id="reset">重置条件</button>
					<button type="submit" class="btn green">搜索 <i class="fa fa-search"></i></button>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="table-scrollable">
						<table class="table table-hover table-bordered">
							<thead>
								<tr>
									<th width="16%">发送时间</th>
									<th width="12%">网站</th>
									<th width="9%">模板</th>
									<th width="22%">收件人</th>
									<th width="42%">标题</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($lst as $v){?> 
									<tr>
									<td>
										<?=date('Y-m-d H:i:s', $v->create_time)?>
										<?php if($v->send_state == 1){?>
											<i class="fa fa-check-circle text-success" title="成功" style="cursor: default;"></i>
										<?php }else{?>
											<i class="fa fa-times-circle text-danger" title="失败" style="cursor: default;"></i>
										<?php }?>
									</td>
									<td><?=htmlspecialchars($this->sites[$v->site_id])?></td>
									<td><?=element($v->tmp_code, $tmplates, $v->tmp_code)?></td>
									<td><?=htmlspecialchars($v->email_to)?></td>
									<td><a href="<?=site_url($thisModule.$thisController.'/view/'.$v->id)?>"><?=htmlspecialchars($v->email_subject)?></a></td>
								</tr>
								<?php }?> 
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 text-right">
						<?=$pagination?>
					</div>
			</div>
			<?=form_close()?>
		</div>
		<!-- End: life time stats -->
	</div>
</div>
<!-- END PAGE CONTENT-->
