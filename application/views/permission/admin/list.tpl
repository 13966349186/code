<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script language="javascript">
function op_check(){
	var selObj = $("#user_op")[0];
	if(selObj.selectedIndex < 1){
		alert("请选择操作！");
		return false;
	}
	var allUnChecked = true;
	var checkBoxList = $("input[name^='id_time']");
	for(var i=0;i<checkBoxList.length;i++){
		if(checkBoxList[i].checked){
			allUnChecked = false;
			break;
		}
	}
	if(allUnChecked){
		alert("请选择要操作的用户！");
		return false;
	}
	if(!confirm("确定要 "+$("#user_op").find("option:selected").html()+" 选中的用户么？")){
		return false;
	}
	$("#user_mng_frm").submit();
}
$(document).ready(function(){
	$("#checkall").on('click', function(){
		var ck = $(this).is(":checked");
		var checkBoxList = $("input[name^='id_time']");
		for(var i=0;i<checkBoxList.length;i++){
			checkBoxList[i].checked = ck;
			$(checkBoxList[i]).uniform();
		}
	});
});
</script>
<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<div class="portlet-title portlet-title-noborder">
				<?=form_open('', Array('class'=>'form-inline', 'role'=>'form'))?>
					<?=search_form_dropdown('role_id', array(''=>'请选择角色...')+$roles, filterValue('role_id'),'角色');?>
					<?=search_form_input('name',filterValue('name'),'用户名');?>
					<?=search_form_dropdown('forbidden', array(''=>'所有状态')+$this->MAdmin->getState(), filterValue('forbidden'), '用户状态' ,'class="form-control input-small" '); ?>
					<button type="submit" class="btn green about_search">搜索 <i class="fa fa-search"></i></button>
				<?=form_close()?>
			</div>
			<div class="portlet-body">
				<?=form_open('/'.$thisModule.$thisController.'/delete', Array('id'=>'user_mng_frm'))?>
			<?php if($p->edit){?>
				<div class="row">
					<div class="col-xs-12">
						<div class="table-group-actions">
							<select name="user_op" id="user_op" class="table-group-action-input form-control input-inline input-small input-sm">
								<option value="">批量操作...</option>
								<option value="delete">删除</option>
								<option value="forbid">禁用</option>
								<option value="unforbid">启用</option>
							</select>
							<a href="javascript:void(0);" class="btn btn-sm btn-success table-group-action-submit" onclick="op_check();"><i class="fa fa-check"></i> 应用</a>
						</div>
					</div>
				</div>
			<?php }?>
				<div class="row">
					<div class="col-xs-12">
						<div class="table-scrollable">
							<table class="table table-hover table-bordered table-striped">
								<thead>
								<tr>
									<th width="1%">
										<div style="width:26px;"><?php if($p->edit){?><input type="checkbox" id="checkall" name="checkall" /><?php }else{?>&nbsp;<?php }?></div>
									</th>
									<th width="32%">账号</th>
									<th width="32%">用户名</th>
									<th width="32%">角色</th>
									<th width="1%"><div style="width:130px;">最后登录时间</div></th>
									<th width="1%"><div style="width:60px;">状态</div></th>
								</tr>
								</thead>
								<tbody>
								<?php foreach($admins as $admin){?>
									<tr>
										<td><?php if($p->edit){?><input type="checkbox" name="id_time[<?=$admin->id?>]" value="<?=$admin->update_time?>" /><?php }else{?>&nbsp;<?php }?></td>
										<td style="word-break:break-all;">
										<?php if($p->edit){?>
											<a href="<?=site_url($thisModule.$thisController.'/edit/'.$admin->id)?>"><?=htmlspecialchars($admin->account)?></a>
										<?php }else{?>
											<?=htmlspecialchars($admin->account)?>
										<?php }?>
										</td>
										<td><?=htmlspecialchars($admin->name)?></td>
										<td><?=htmlspecialchars(element($admin->role_id, $roles, $admin->role_id))?></td>
										<td><?= $admin->last_login_time < 1 ? "从未登录" : date('Y-m-d H:i:s', $admin->last_login_time)?></td>
										<td><?=($admin->forbidden == 1 ? '<span class="label label-danger status_icons label_status_icons">禁用</span>' : '<span class="label label-success status_icons label_status_icons">启用</span>')?></td>
										<!-- td><?php if($p->edit){?><a href="<?=site_url($thisModule.$thisController.'/edit/'.$admin->id)?>" class="about_edit">编辑</a><?php } else{ echo '&nbsp;'; } ?></td -->
									</tr>
								<?php }?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?=form_close()?>
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