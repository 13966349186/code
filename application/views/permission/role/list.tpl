<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<div class="portlet-body">
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-hover table-bordered table-striped">
							<thead>
								<tr>
									<th width="16%"><?=l('id')?></th>
									<th><?=l('name')?></th>
									<th width="20%"><?=l('user_num')?></th>
									<th width="8%">操作</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach($roles as $role){?>
								<tr>
									<td><span><?=htmlspecialchars($role->id)?></span></td>
									<td>
									<?php if($p->edit){?>
										<a href="<?=site_url($thisModule.$thisController.'/edit/'.$role->id.'/'.$role->update_time)?>"><?=htmlspecialchars($role->name)?></a>
									<?php }else{?>
										<?=htmlspecialchars($role->name)?>
									<?php }?>
									</td>
									<td><?=htmlspecialchars($role->user_num)?></td>
									<td>
										<?php if($p->delete && $role->id != 1){?><a href="<?=site_url($thisModule.$thisController.'/delete/'.$role->id.'/'.$role->update_time)?>" class="black"><i class="fa fa-trash-o"></i> <?=l('delete')?></a><?php }else{echo '&nbsp;';}?>
									</td>
								</tr>
							<?php }?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12  text-right">
						<?=$pagination?>
					</div>
				</div>
			</div>
		</div>
		<!-- End: life time stats -->
	</div>
</div>

<!-- END PAGE CONTENT-->
