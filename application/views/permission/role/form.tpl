<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<script language="javascript">
$(document).ready(function(){
	$("[pow]").on('click', function(){
		var ck = $(this).is(":checked");
		var checkBoxList = $("input[name*='"+$(this).attr("pow").replace("[", "\\[").replace("]", "\\]")+"']");
		for(var i=0;i<checkBoxList.length;i++){
			checkBoxList[i].checked = ck;
			$(checkBoxList[i]).uniform();
		}
	});
});
</script>
<?=form_open('', Array('class'=>'form-horizontal'))?>
<input type="hidden" id="update_time" name="update_time" value="<?= set_value('update_time', $role->update_time)?>" />
<div class="portlet  box green">
	<?=edit_form_caption(l($thisMethod).'角色')?>
	<div class="portlet-body form">	
		<form action="#" class="form-horizontal">
			<div class="form-body">
				<?=edit_form_input('name', set_value('name', $role->name), '角色名')?>
				<?=edit_form_textarea('description', set_value('description', $role->description), '备注')?>
				<div class="form-group">
					<label class="col-xs-3 control-label">权限</label>
					<div class="col-xs-8">
						<div class="">
							<table class="table table-hover table-bordered table-striped">
							<thead>
							<tr>
								<th>页面</th>
								<th width="22%"><label class="checkbox-inline checkbox-pt checkbox-pl">
									<input type="checkbox" pow="[read]"></label></th>
								<th width="22%"><label class="checkbox-inline checkbox-pt checkbox-pl">
									<input type="checkbox" pow="[edit]"></label></th>
								<th width="22%"><label class="checkbox-inline checkbox-pt checkbox-pl">
									<input type="checkbox" pow="[add]"></label></th>
							</tr>
							</thead>
								<tbody>
								<?php
									$tmpDic = Array();
									$current_class= '';
									foreach ($ctrls as $ctrlItem){
										if(($ctrlItem['group'] & GROUP_ADMIN) != GROUP_ADMIN){
											continue;
										}
										if(array_key_exists($ctrlItem['id'], $tmpDic)){
											continue;
										}
										$tmpDic[$ctrlItem['id']] = $ctrlItem['id'];
										$num = 0;
										if(array_key_exists($ctrlItem['id'], $pows)){
											$num = $pows[$ctrlItem['id']]['power'];
										}
										$item = UserPower::decodePower($num);
										if($ctrlItem['class'] != $current_class){
											$current_class = $ctrlItem['class'];
											echo '<tr>	<td colspan="4"  style="font-weight: bolder;text-align:center">' . $current_class. '</td></tr>';
										}
										?>
									<tr>
										<td><?=$ctrlItem['id']."-".htmlspecialchars($ctrlItem['name'])?></td>
										<td>
											<label class="checkbox-inline checkbox-pt checkbox-pl">
											<input type="checkbox" name="power[<?=$ctrlItem['id']?>][read][]"<?=$item->read?' checked="checked"':''?>/>查看</label>
										</td>
										<td>
											<label class="checkbox-inline checkbox-pt checkbox-pl">
											<input type="checkbox" name="power[<?=$ctrlItem['id']?>][edit][]"<?=$item->edit?' checked="checked"':''?>/>修改</label>
										</td>
										<td>
											<label class="checkbox-inline checkbox-pt checkbox-pl">
											<input type="checkbox" name="power[<?=$ctrlItem['id']?>][add][]"<?=$item->add?' checked="checked"':''?>/>添加删除</label>
										</td>
									</tr>
								<?php }?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<?php $this->load->view('partials/submitButtons.tpl')?>
		</form>
	</div>
</div>
<?=form_close()?>
