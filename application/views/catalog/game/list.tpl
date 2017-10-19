<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<div class="portlet">
			<?=form_open('', Array('class'=>'form-inline', 'role'=>'form'))?>
				<?=search_form_input('name',filterValue('name'),'游戏名称');?>
				<?=search_form_dropdown('state', array(''=>'所有状态') + $this->MGame->getState(),filterValue('state'),'状态', 'class="form-control input-small"') ?>
				<div class="btn-group" data-toggle="buttons">
				<label class="btn btn-default  <?=filterValue('sort') === '1'?'active':'' ?>"><input type="checkbox" name="sort" class="toggle" value="1"  <?= filterValue('sort')==='1'?'checked="checked"':'' ?>><i class="fa fa-sort-amount-asc"></i> 使用排序</label>
				</div>
				<button type="submit" class="btn green">搜索 <i class="fa fa-search"></i></button>
			<?=form_close()?>
			<div class="portlet-body">
				<div class="row">
				 	<div class="col-xs-12">
				 		<div class="table-scrollable">
					 		<table class="table table-hover table-bordered table-striped">
								<thead>
								<tr>
									<th width="40%">游戏名</th>
									<th width="">标识</th>
									<th width="10%">排序</th>
									<th width="10%">状态</th>
									<th width="8%">操作</th>
								</tr>
								</thead>
								<tbody>
								<?php $cat_p = UserPower::getPermisionInfo($thisModule.'category'); ?>
								<?php foreach ($games as $game){?>
								<tr>
									<td>
									<?php if($cat_p->read){?>
										<a href="<?=site_url($thisModule.'category/index/?game_id='.$game->id)?>"><?=htmlspecialchars($game->name)?></a>
									<?php }else{?>
										<?=htmlspecialchars($game->name)?>
									<?php }?>
									</td>
									<td><?=htmlspecialchars($game->code)?></td>
									<td><?=htmlspecialchars($game->sort)?></td>
									<td><?=($game->state == MGame::STATE_DISABLE ? '<span class="label label-danger status_icons label_status_icons">禁用</span>' : '<span class="label label-success status_icons label_status_icons">启用</span>')?></td>
									<td><a href="<?=site_url($thisModule.$thisController.'/edit/'.$game->id)?>" class="about_edit btn <?=$p->edit?'':'disabled' ?>" ><i class="fa fa-edit"></i> 编辑</a></td>
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
	</div>
</div>
<!-- END PAGE CONTENT-->