<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<?=form_open('', Array('class'=>'form-inline', 'role'=>'form'))?>
				<?=search_form_dropdown('model_id',array_merge(array(''=>'请选择数据类型...'), $models),filterValue('model_id'),'数据类型', 'class="form-control input-medium"');?>
				<?=search_form_dropdown('site_id',array(''=>'请选择网站...')+parse_options($this->sites),filterValue('site_id'),'网站', 'class="form-control input-medium"');?>
				<button type="submit" class="btn green about_search">搜索 <i class="fa fa-search"></i></button>
			<?=form_close()?>
			<div class="portlet-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="table-scrollable">
							<table class="table table-hover table-bordered table-striped">
								<thead>
									<tr>
										<th style="width:16%;">目录代码</th>
										<th style="width:20%;">目录名称</th>
										<th style="width:16%;">所属网站</th>
										<th style="width:15%;">数据类型</th>
										<th style="width:3%;"><div style="width:60px;">数量限制</div></th>
										<th style="width:16%;">父级目录</th>
										<th style="width:4%;"><div style="width:40px;">顺序</div></th>
										<th><div style="width:125px;">操作</div></th>
									</tr>
								</thead>
								<tbody>
								<?php foreach($list as $v){?>
									<tr>
										<td><?=htmlspecialchars($v->code)?></td>
										<td>
										<?php if($p->edit){?>
											<a href="<?=site_url($thisModule.$thisController.'/edit/'.$v->id)?>"><?=htmlspecialchars($v->name)?></a>
										<?php }else{?>
											<?=htmlspecialchars($v->name)?>
										<?php }?>
										</td>
										<td><?=htmlspecialchars($v->site_name)?></td>
										<td><?=htmlspecialchars($v->model_name)?></td>
										<td><?=($v->record_limit<0?"不限制":($v->record_limit<1?'-':$v->record_limit.''))?></td>
										<td><?=htmlspecialchars($v->parent_name)?></td>
										<td><?=htmlspecialchars($v->disp_sort)?></td>
										<td>
											<a href="<?=site_url($thisModule.$thisController.'/edit/'.$v->id)?>" class="about_edit btn <?=$p->edit?'':'disabled' ?>" ><i class="fa fa-edit"></i> 编辑</a>
											<a href="<?=site_url($thisModule.$thisController.'/delete/'.$v->id.'/'.$v->update_time)?>" class="about_edit btn  <?=$p->delete?'':'disabled' ?>"><i class="fa fa-trash-o"></i> <?=l('delete')?></a>
										</td>
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