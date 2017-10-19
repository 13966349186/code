<?php
$this_url =  $thisModule.$thisController.'/' . $thisMethod . '/';
$bread[] = array('name'=>'游戏管理','url'=>site_url($thisModule.'game'));
if($game_id){
	$bread[] = array('name'=>$this->games[$game_id], 'url'=>site_url($this_url .  '?game_id=' . $game_id) );
}
foreach($path as $v){
	$bread[] = array('name'=>htmlspecialchars($v->name),'url'=>site_url($this_url . '?game_id=' . $game_id .'&category_id=' . $v->id ) );
}
$btn = array('name'=>'创建目录', 
		'class'=>'btn blue' . ($p->add?'':' disabled'), 
		'url'=>site_url($thisModule.$thisController.'/add/' . $game_id . '/' . $category_id));
?>
<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', array('_BREADCRUMB'=>$bread, '_BTN_GROUP'=>$btn))?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript">
function op_check(){
	var selObj = $("#user_op")[0];
	if(selObj.selectedIndex < 1){
		alert("请选择操作！");
		return false;
	}
	if($("input[name^='id_time']:checked").length == 0){
		alert("请选择要操作的目录！");
		return false;
	}
	if(!confirm("确定要 "+$("#user_op").find("option:selected").html()+" 选中的目录么？")){
		return false;
	}
	$("#cat_mng_frm").submit();
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
			<div class="portlet-body">
			<?=form_open('/'.$thisModule.$thisController.'/batch', Array('id'=>'cat_mng_frm'))?>
				<div class="row">
					<div class="col-sm-12 col-xs-12">
					<?php if($actions){?>
						<div class="table-group-actions ">
							<?=form_dropdown('user_op',array(''=>'批量操作...') + $actions,'','id="user_op" class="table-group-action-input form-control input-inline input-small"') ?>
							<a href="javascript:void(0);" class="btn btn-success table-group-action-submit" onclick="op_check();"><i class="fa fa-check"></i> 应用</a>
						</div>
						<?php }?>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="table-scrollable">
							<table class="table table-hover table-bordered table-striped">
								<thead>
								<tr>
								<?php if($p->edit){?>
									<th width="1%">
										<div style="width:26px;"><input type="checkbox" id="checkall" name="checkall" /></div>
									</th>
								<?php }?>
									<th style="width:28%;">游戏</th>
									<th style="width:28%;">目录名</th>
									<th style="width:28%;">标识</th>
									<th style="width:7%;">状态</th>
									<th style="width:9%;">操作</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($lst as $v){?>						
									<tr>
										<?php if($p->edit){?><td><input type="checkbox" name="id_time[<?=$v->id?>]" value="<?=$v->update_time?>" /></td><?php }?>
										<td><?=htmlspecialchars($this->games[$v->game_id])?></td>
										<td>
											<a href="<?=site_url($this_url . '?game_id=' . $v->game_id .'&category_id=' . $v->id ) ?>"><?=htmlspecialchars($v->name)?></a>
										</td>
										<td>
											<?=htmlspecialchars($v->code)?>
										</td>
										<td><?=($v->state == MCategory::STATE_DISABLE ? '<span class="label label-danger status_icons label_status_icons">禁用</span>' : '<span class="label label-success status_icons label_status_icons">启用</span>')?></td>
										<td><a href="<?=site_url($thisModule.$thisController.'/edit/'.$v->id)?>" class="about_edit btn <?=$p->edit?'':'disabled' ?>" ><i class="fa fa-edit"></i> 编辑</a></td>
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