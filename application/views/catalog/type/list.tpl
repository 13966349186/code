<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<?=form_open('', Array('class'=>'form-inline', 'role'=>'form'))?>
				<?=search_form_dropdown('game_id',array(''=>'请选择游戏...')+ parse_options($this->games), filterValue('game_id'),'游戏');?>
				<?=search_form_dropdown('model',array(''=>'请选择数据模型...') + $models,filterValue('model'),'数据模型');?>
				<?=search_form_dropdown('state', array(''=>'所有状态')+$this->MType->states, filterValue('state'),'状态','class="form-control input-small"') ?>
				<button type="submit" class="btn green about_search">搜索 <i class="fa fa-search"></i></button>
			<?=form_close()?>
			<div class="portlet-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="table-scrollable">
							<table class="table table-hover table-bordered table-striped">
								<thead>
									<tr>
										<th style="width:25%;">所属游戏</th>
										<th >类型标识</th>
										<th style="width:25%;">名称</th>
										<th style="width:15%;">数据模型</th>
										<th style="width:8%;">状态</th>
										<th style="width:6%;">操作</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach ($lst as $v){?>
									<tr>
										<td><?=$this->games[$v->game_id]?></td>
										<td>
										<?php if($p->edit){?>
											<a href="<?=site_url($thisModule.$thisController.'/edit/'.$v->id)?>"><?=htmlspecialchars($v->code)?></a>
										<?php }else{?>
											<?=htmlspecialchars($v->code)?>
										<?php }?>
										</td>
										<td><?=htmlspecialchars($v->name)?></td>
										<td><?=htmlspecialchars($models[$v->model])?></td>
										<td><?=$v->state==MType::STATE_ENABLE?'<span class="label label-success status_icons label_status_icons">启用</span>':'<span class="label label-danger status_icons label_status_icons">禁用</span>'?></td>
										<td><a href="<?=site_url($thisModule.$thisController.'/edit/'.$v->id)?>" class="about_edit btn <?=$p->edit?'':'disabled' ?>" ><i class="fa fa-edit"></i> 编辑</a></td>
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
