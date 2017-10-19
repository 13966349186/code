<?php 
$paymentStatesCls = $this->config->item('pay_states_css');
?>
<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_NO_IOSS_NEW'=>'1'))?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<?=form_open('', Array('class'=>'portlet-body'))?> 
				<div class="form-group">
					<div class="form-inline" role="form">
						<?=search_form_dropdown('site_id', array(''=>'选择网站...')+parse_options($this->sites), filterValue('site_id'),'网站');?>
						<div class="form-group">
							<input type="text" class="form-control input-medium" value="<?=filterValue('no')?>" name="no" placeholder="请输入订单号">
						</div>
						<div class="form-group">
						<div class="input-group input-large date-picker input-daterange" data-date="" data-date-format="yyyy-mm-dd">
							<input type="text" class="form-control" name="create_begin" value="<?=filterValue('create_begin')?>" placeholder="起始时间">
							<span class="input-group-addon">to</span>
							<input type="text" class="form-control" name="create_end" value="<?=filterValue('create_end')?>" placeholder="结束时间">
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="btn green">搜索 <i class="fa fa-search"></i>	</button>
					</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="table-scrollable">
							<?php $open_p = UserPower::getPermisionInfo($this->_thisModule.'orderedit'); ?>
							<table class="table table-hover table-bordered table-striped about_table">
								<thead>
									<tr>
										<th width="16%">订单号</th>
										<th width="11%">网站</th>
										<th>邮箱</th>
										<th width="15%">游戏</th>
										<th width="11%">创建时间</th>
										<th width="8%">订单金额</th>
										<th width="8%">支付方式</th>
										<th width="6%">支付状态</th>
										<th width="8%">操作</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach ($lst as $v){?> 
									<tr>
										<td>
											<?=htmlspecialchars($v->no)?>
										</td>
										<td><?=htmlspecialchars($v->site_name)?></td>
										<td><?=htmlspecialchars($v->user_email)?></td>
										<td><?=htmlspecialchars($v->game_name .'('.$this->types[$v->product_type].')')?></td>
										<td><?=date('Y-m-d H:i', $v->create_time)?></td>
										<td><?=$this->currency->format($v->amount, $v->currency)?></td>
										<td><?=htmlspecialchars($this->paymentmethod[$v->payment_method] )?></td>
										<td><span class="<?=element($v->payment_state, $paymentStatesCls, '')?>"><?=$this->MOrder->getPayState($v->payment_state)?></span></td>
										<td><a href="<?=site_url($thisModule. $thisController .'/open/'.$v->id .'/' .$v->update_time)?>"  class="btn about_edit open-order  <?=$p->edit?'':'disabled' ?>"><i class="fa fa-edit"></i> 生成订单</a></td>
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
<script src="<?=image_url('/static/assets/global/plugins/bootbox/bootbox.min.js')?>" type="text/javascript"></script>
<script>
$(document).ready(function(){
	$("a.open-order").on('click',function(){
		var href = $(this).attr('href');
		 bootbox.confirm("确定生成订单?", function(result) {
		 	if(result) location.href = href;
        }); 
        return false;
	});
});
</script>