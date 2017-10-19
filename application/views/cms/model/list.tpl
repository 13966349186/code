<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?> 
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript">
function alertDel(id, update_time){
	if(!confirm("确定要删除吗？")){
		return;
	}
	window.location.href = "<?=site_url($thisModule.$thisController)?>/delete/" + id + "/" + update_time;
}
</script>
<div class="row">
	<div class="col-md-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<div class="portlet-body">
				<div class="row">
					<div class="col-md-12">
						<table class="table table-hover table-bordered table-striped">
							<thead>
								<tr>
									<th><?=l('id')?></th>
									<th style="width:30%;">文章代码</th>
									<th style="width:40%;">名称</th>
									<th style="width:15%;">操作</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach ($list as $v){?> 
								<tr>
									<td><?=htmlspecialchars($v->id)?></td>
									<td><?=htmlspecialchars($v->code)?></td>
									<td><?=htmlspecialchars($v->name)?></td>
									<td>
										<a href="<?=site_url($thisModule.$thisController.'/edit/'.$v->id)?>" class="about_edit btn <?=$p->edit?'':'disabled' ?>" ><i class="fa fa-edit"></i> 编辑</a>
										<a href="javascript:void(alertDel('<?=$v->id?>', '<?=$v->update_time?>'));" class="about_edit btn <?=$p->delete?'':'disabled' ?>"><i class="fa fa-trash-o"></i> <?=l('delete')?></a> 
									</td>
								</tr>
							<?php }?> 
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12  text-right">
						<?=$pagination?> 
					</div>
				</div>
			</div>
		</div>
		<!-- End: life time stats -->
	</div>
</div>

<!-- END PAGE CONTENT-->
