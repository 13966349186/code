<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<?=form_open()?>
<input type="hidden" name="update_time" id="update_time" value="<?=isset($obj->update_time)?$obj->update_time:''?>" />
<input type="hidden" name="end" id="end" value="<?=count($lst)?>" />
<div class="portlet green box">
	<?=edit_form_caption(l($thisMethod).$thisControllerName)?>
	<div class="portlet-body form">
		<div class="form-horizontal form-body">
			<?=edit_form_input('code', set_value('code', $obj->code), '表名')?>
			<?=edit_form_input('name', set_value('name', $obj->name), '名称')?>
			<div class="table-scrollable">
					<table class="table table-hover table-bordered table-striped">
						<thead>
							<tr>
								<th width="12%">代码</th>
								<th width="12%">名称</th>
								<th width="12%">类型</th>
								<th>格式配置</th>
								<th width="10%">列表显示</th>
								<th width="8%"><div style="width:40px;">操作</div></th>
							</tr>
						</thead>
						<tbody id="dtb">
						<?php for($i=0;$i<count($lst);$i++){ $v = $lst[$i]; ?>
							<tr id="dtr<?=$i?>">
								<td<?php echo isset(${'col_name'.$i.'_err'})?' class="has-error"':'';?>>
									<input type="hidden" name="id<?=$i?>" value="<?=htmlspecialchars($v->id)?>" />
									<input class="form-control input-sm" type="text" name="col_name<?=$i?>" value="<?=htmlspecialchars($v->col_name)?>" />
									<?php if(isset(${'col_name'.$i.'_err'})){?><p class="help-block"><?=${'col_name'.$i.'_err'}?></p><?php }?>
								</td>
								<td<?php echo isset(${'disp_name'.$i.'_err'})?' class="has-error"':'';?>>
									<input type="text"  class="form-control input-sm" id="disp_name<?=$i?>" name="disp_name<?=$i?>" value="<?=htmlspecialchars($v->disp_name)?>" />
									<?php if(isset(${'disp_name'.$i.'_err'})){?><p class="help-block"><?=${'disp_name'.$i.'_err'}?></p><?php }?>
								</td>
								<td<?php echo isset(${'data_type'.$i.'_err'})?' class="has-error"':'';?>>
									<select name="data_type<?=$i?>" class="form-control input-sm">
									<?php foreach ($format_type_list as $sk => $sv){?>
									<option value="<?=htmlspecialchars($sk)?>" <?=$sk==$v->data_type?'selected=selected':''?>><?=htmlspecialchars($sv)?></option>
									<?php }?>
									</select>
									<?php if(isset(${'data_type'.$i.'_err'})){?><p class="help-block"><?=${'data_type'.$i.'_err'}?></p><?php }?>
								</td>
								<td<?php echo isset(${'data_format'.$i.'_err'})?' class="has-error"':'';?>>
									<textarea class="form-control" name="data_format<?=$i?>"><?=htmlspecialchars($v->data_format)?></textarea>
									<?php if(isset(${'data_format'.$i.'_err'})){?><p class="help-block"><?=${'data_format'.$i.'_err'}?></p><?php }?>
								</td>
								<td align="center"<?php echo isset(${'disp_on_list'.$i.'_err'})?' class="has-error"':'';?>>
									<input type="checkbox" class="form-control" name="disp_on_list<?=$i?>" value="1"<?=$v->disp_on_list=='1'?' checked=checked':''?> />
									<?php if(isset(${'disp_on_list'.$i.'_err'})){?><p class="help-block"><?=${'disp_on_list'.$i.'_err'}?></p><?php }?>
								</td>
								<td align="center"><a class="black" href="javascript:void($('#dtr<?=$i?>').remove());"><i class="fa fa-trash-o"></i> 删除</a></td>
							</tr>
						<?php }?>
						</tbody>
					</table>
			</div>
		</div>
		<div class="form-actions">
				<button class="btn blue-madison" onclick="addRow()" type="button">添加行</button>
				<input type="submit" class="btn green-haze pull-right" value="保存" />
		</div>
	</div> 
</div>
<?=form_close()?>
<script type="text/javascript">
function addRow(){
	var idx = $('#end').val();
	var str = '<tr id="dtr'+idx+'">';
	str += '<td><input type="hidden" name="id'+idx+'" value="" /><input type="hidden" name="update_time'+idx+'" value="" />';
	str += '<input type="text" class="form-control input-sm" name="col_name'+idx+'" value="" /></td>';
	str += '<td><input type="text" class="form-control input-sm" name="disp_name'+idx+'" value="" /></td>';
	str += '<td><select name="data_type'+idx+'" class="form-control input-sm">';
	<?php foreach ($format_type_list as $sk => $sv){?>
	str += '<option value="<?=htmlspecialchars($sk)?>"><?=htmlspecialchars($sv)?></option>';
	<?php }?>
	str += '</select></td>';
	str += '<td><textarea name="data_format'+idx+'" class="form-control"></textarea></td>';
	str += '<td align="center"><input type="checkbox" id="disp_on_list'+idx+'" name="disp_on_list'+idx+'" value="1" /></td>';
	str += '<td align="center"><a class="black" href="javascript:void($(\'#dtr'+idx+'\').remove());"><i class="fa fa-trash-o"></i> 删除</a></td>';
	str += "</tr>";
	$("#dtb").append(str);
	$('#end').val($('#end').val()*1+1);
	$('#dtr'+idx).find('input[type="checkbox"]').uniform();
//    $('#dtr'+idx).find('select').select2({
//        placeholder: "Select",
//        allowClear: true
//    });
}
</script>

<!-- END PAGE CONTENT-->
