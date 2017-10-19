<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_NO_IOSS_NEW'=>'1'))?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->

<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<?=form_open('', Array('class'=>'form-inline', 'role'=>'form'))?>
				<?=search_form_input('login_ip',filterValue('login_ip'),'IP', 'class="form-control input-medium"');?>
				<?=search_form_input('account',filterValue('account'),'账号', 'class="form-control input-medium"');?>
				
				<div class="form-group">
					<div class="input-group input-large date-picker input-daterange" data-date="" data-date-format="yyyy-mm-dd">
						<input type="text" class="form-control" name="login_begin" value="<?=filterValue('login_begin')?>" placeholder="开始时间">
						<span class="input-group-addon">
						to </span>
						<input type="text" class="form-control" name="login_end" value="<?=filterValue('login_end')?>" placeholder="结束时间">
					</div>
				</div>
				<button type="submit" class="btn green">搜索<i class="fa fa-search"></i></button>
			<?=form_close()?>
			<div class="portlet-body">
				 <div class="row">
				 	<div class="col-xs-12">
				 		<div class="table-scrollable">
					 		<table class="table table-hover table-bordered table-striped">
								<thead>
								<tr>
									<th width="25%">时间</th>
									<th width="25%">账号</th>
									<th width="20%">姓名</th>
									<th width="30%">IP地址</th>
									
								</tr>
								</thead>
								<tbody>
								<?php if($adminLogin){foreach ($adminLogin as $login){?>
								<tr>
									<td><?=htmlspecialchars(date('Y-m-d H:i:s',$login->login_time))?></td>
									<td><?=htmlspecialchars($login->account)?></td>
									<td><?=htmlspecialchars($login->name)?></td>
									<td><?=htmlspecialchars($login->login_ip)?></td>
									
									
								</tr>
								<?php }}?>	 
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
			</div>
		</div>
		<!-- End: life time stats -->
	</div>
</div>
<!-- END PAGE CONTENT-->