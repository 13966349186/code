<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<?=form_open('', Array('class'=>'form-inline', 'role'=>'form'))?>
				<?= search_form_input('domain',filterValue('domain'),'名称或域名');?>
				<?= search_form_dropdown('state',array(''=>'所有状态') + $this->MSite->getState(), filterValue('state'), '网站状态', 'class="form-control input-small"') ?>
				<button type="submit" class="btn green">搜索 <i class="fa fa-search"></i></button>
			<?=form_close()?>
			<div class="portlet-body">
				 <div class="row">
				 	<div class="col-xs-12">
				 		<div class="table-scrollable">
				 		<?php $cms_p = UserPower::getPermisionInfo('cms/data'); ?>
					 		<table class="table table-hover table-bordered table-striped">
								<thead>
								<tr>
									<th width="20%">网站名称</th>
									<th width="14%">网站标识</th>
									<th width="22%">域名</th>
									<th>网站内容</th>
									<th>支付方式</th>
									<th>网站参数</th>
									<th>基础信息</th>
									<th width="7%">状态</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($sites as $site){?>
								<tr>
									<td>
									<?php if($p->edit){?>
										<a href="<?=site_url($thisModule.$thisController.'/edit/'.$site->id)?>"><?=htmlspecialchars($site->name)?></a>
									<?php }else{?>
										<?=htmlspecialchars($site->name)?>
									<?php }?>
									</td>
									<td><?=htmlspecialchars($site->code)?></td>
									<td><?=htmlspecialchars($site->domain)?></td>
								    <td><a href="<?=site_url('cms/data/index/'.$site->id) ?>" class="about_edit btn  <?= $cms_p->read?'':'disabled' ?>" target="_blank"><i class="fa fa-file-word-o"></i> 内容管理</a></td>
							    	<td><a href="<?=site_url($thisModule.$thisController.'/editSitePaymethod/'.$site->id) ?>" class="about_edit btn  <?= ($p->read||$p->edit)?'':'disabled' ?>"><i class="fa fa-money"></i> 支付方式</a></td>
									<td><a href="<?=site_url($thisModule.$thisController.'/editParam/'.$site->id) ?>" class="about_edit btn  <?= ($p->read||$p->edit)?'':'disabled' ?>"><i class="fa fa-cog"></i> 参数配置</a></td>
									<td>
										<a href="<?=site_url($thisModule.$thisController.'/edit/'.$site->id)?>" class="about_edit btn <?=$p->edit?'':'disabled' ?>" ><i class="fa fa-edit"></i> 编辑</a>
									</td>
									<td><?=($site->state == MSite::STATE_DISABLE ? '<span class="label label-danger status_icons label_status_icons">禁用</span>' : '<span class="label label-success status_icons label_status_icons">启用</span>')?></td>
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