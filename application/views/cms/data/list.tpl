<!-- BEGIN PAGE HEADER-->
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<!-- span>网站【<?=$site->name?>】的CMS内容</span -->
		<?php if(isset($category) && $category){?>
			<span><a href="<?=site_url($thisModule.$thisController.'/index/'.$site->id)?>"><?=htmlspecialchars($site->name)?></a></span>
		<?php }else{?>
			<span><?=htmlspecialchars($site->name)?></span>
		<?php }?>
		<?php foreach ($parents as $v){?>
			<i class="fa fa-angle-right"></i> <span><a href="<?=site_url($thisModule.$thisController.'/index/'.$site->id.'/'.$v->id)?>">目录[<?=htmlspecialchars($v->name)?>]</a></span>
		<?php }?>
		<?php if(isset($category) && $category){?>
			<i class="fa fa-angle-right"></i> <span><?=htmlspecialchars($category->name)?></span>
		<?php }?>
		</li>
	</ul>
	<div class="page-toolbar">
		<div class="btn-group pull-right">
			<?php if($p->add && isset($category) && $category && ($category->record_limit != 0)){?><a href="<?=site_url($thisModule.$thisController.'/add/'.$site->id.'/'.$category->id)?>" class="btn blue" style="padding:8px 14px">新建</a><?php }?>
		</div>
	</div>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript">
function defConfirm(url){
	if(!confirm("确定要删除吗？")){
		return false;
	}
	window.location.href = url;
	return true;
}
</script>
<div class="row">
	<div class="col-md-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
		<?php if(isset($category) && $category){?>
			<?=form_open('', Array('class'=>'form-inline', 'role'=>'form'))?>
				<?=search_form_input('title',filterValue('title'),'标题', 'class="form-control input-medium"');?>
				<?=search_form_dropdown('status',Array(''=>'选择状态...', '1'=>'正常', '0'=>'禁用'),filterValue('status'),'状态', 'class="form-control input-medium"');?>
				<button type="submit" class="btn green about_search">搜索 <i class="fa fa-search"></i></button>
			<?=form_close()?>
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-12">
						<div class="table-scrollable">
							<table class="table table-hover table-bordered table-striped">
								<thead>
								<tr>
									<th>标题</th>				
									<?php foreach ($model_detail as $m){
										if($m->disp_on_list != 1){
											continue;
										}
										?>
										<th><?=htmlspecialchars($m->disp_name)?></th>
									<?php }?>
									<th width="120px">标识</th>
									<th width="90px">作者</th>
									<th width="15%"><div style="width:130px;">发布时间</div></th>
									<th style="width:7%;"><div style="width:50px;">操作</div></th>
								</tr>
								</thead>
								<tbody>
								<?php  foreach ($nodeList as $node){	?>
									<tr<?php echo $node->status != 1 ? ' style="text-decoration:line-through;color:red;"':''; ?>>
										<td>
										<?php if($p->edit){?><a href="<?=site_url($thisModule.$thisController.'/edit/'.$site->id.'/'.$category->id.'/'.$node->id)?>"><?php }?>
										<span><?=htmlspecialchars($node->title)?></span>
										<?php if($p->edit){?></a><?php }?>
										</td>
										<?php 
										foreach ($model_detail as $m){
											if($m->disp_on_list != 1){
												continue;
											}
											echo '<td>' . cms_list_display($m->data_type, $m->col_name, $m->data_format, $node->{$m->col_name}) . '</td>';
										}
										?>
										<td><?=htmlspecialchars($node->code)?></td>
										<td><?=htmlspecialchars($node->author)?></td>
										<td><?=date('Y-m-d H:i:s',$node->publish_time)?></td>
										<td>
										<?php if($p->delete){?>
										<a class="black" href="javascript:void(defConfirm('<?=site_url($thisModule.$thisController.'/delete/'.$site->id.'/'.$node->id.'/'.$node->update_time) ?>'));"><i class="fa fa-trash-o"></i> <?=l('delete')?></a>
										<?php }?>
										</td>
									</tr>
								<?php }?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 text-right">
						<?=$pagination?>
					</div>
				</div>
			</div>
		<?php }?>
		</div>
		<!-- End: life time stats -->
	</div>
</div>

<!-- END PAGE CONTENT-->
