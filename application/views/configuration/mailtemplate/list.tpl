<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_NO_IOSS_NEW'=>1))?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<div class="portlet-body">
				 <div class="row">
				 	<div class="col-xs-12">
				 		<div class="table-scrollable">
					 		<table class="table table-hover table-bordered table-striped">
								<thead>
								<tr>
									<th width="15%">模板名称</th>
									<th width="15%">模板标识</th>
									<th>标题</th>
									<th width="10%">状态</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($lst as $v){?>
								<tr>
									<td>
									<?php if($p->edit){?>
										<a href="<?=site_url($thisModule.$thisController.'/edit/'.$v->id)?>"><?=htmlspecialchars($v->name)?></a>
									<?php }else{?>
										<?=htmlspecialchars($v->name)?>
									<?php }?>
									</td>
									<td><?=htmlspecialchars($v->code)?></td>
									<td><?=htmlspecialchars($v->subject)?></td>
									<td><?=($v->state == MMailTemplate::STATE_DISABLED ? '<span class="label label-danger status_icons label_status_icons">禁用</span>' : '<span class="label label-success status_icons label_status_icons">启用</span>')?></td>
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
			</div>
		</div>
		<!-- End: life time stats -->
	</div>
</div>
<!-- END PAGE CONTENT-->