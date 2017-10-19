<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="tabbable-line boxless">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?=site_url($this->_thisModule.$this->_thisController.'/edit/'.$type->id)?>">1.基本信息 </a>
			</li>
			<li>
				<a href="<?=site_url($this->_thisModule.$this->_thisController.'/product_attr/'.$type->id)?>">2.商品属性配置</a>
			</li>
			<li class="active">
				<a href="#tab_c" data-toggle="tab">3.发货信息配置 </a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="tab_c">
				<div class="row">
					<div class="col-xs-12">
						<div class="portlet green box">
							<?=edit_form_caption('Step3', '<i class="fa fa-cogs"></i>')?>
							<div class="portlet-body form">
								<?=form_open('','class="form-horizontal" role="form" ')?>
									<input type="hidden" name="end" id="end" value="<?=count($lst)?>" />
									<div class="form-body">
								 		<div class="table-scrollable">
									 		<table class="table table-hover table-bordered table-striped">
												<thead>
													<tr>
														<th width="15%">属性名</th>
														<th width="15%">显示名称</th>
														<th width="15%">类型</th>
														<th width="34%">格式配置</th>
														<th width="7%">排序</th>
														<th width="8%">是否索引</th>
														<th width="6%"><div style="width:40px;">操作</div></th>
													</tr>
												</thead>
												<tbody id="dtb">
												<?php for($i=0;$i<count($lst);$i++){ $v = $lst[$i]; ?>
													<tr id="dtr<?=$i?>">
														<td<?php echo isset(${'code'.$i.'_err'})?' class="has-error"':'';?>>
															<input type="hidden" name="id<?=$i?>" value="<?=htmlspecialchars($v->id)?>" />
															<input type="hidden" name="update_time<?=$i?>" value="<?=htmlspecialchars($v->update_time)?>" />
															<input class="form-control input-sm" type="text" name="code<?=$i?>" value="<?=htmlspecialchars($v->code)?>" />
															<?php if(isset(${'code'.$i.'_err'})){?><p class="help-block"><?=${'code'.$i.'_err'}?></p><?php }?>
														</td>
														<td<?php echo isset(${'name'.$i.'_err'})?' class="has-error"':'';?>>
															<input type="text" sort_flg=1 class="form-control input-sm" id="name<?=$i?>" name="name<?=$i?>" value="<?=htmlspecialchars($v->name)?>" />
															<?php if(isset(${'name'.$i.'_err'})){?><p class="help-block"><?=${'name'.$i.'_err'}?></p><?php }?>
														</td>
														<td<?php echo isset(${'data_config'.$i.'_err'})?' class="has-error"':'';?>>
															<select name="data_config<?=$i?>" class="form-control  input-sm" style="width:130px;">
															<?php foreach ($format_type_list as $sk => $sv){?>
															<option value="<?=htmlspecialchars($sk)?>" <?=$sk==$v->data_config?'selected=selected':''?>><?=htmlspecialchars($sv)?></option>
															<?php }?>
															</select>
															<?php if(isset(${'data_config'.$i.'_err'})){?><p class="help-block"><?=${'data_config'.$i.'_err'}?></p><?php }?>
														</td>
														<td<?php echo isset(${'data_format'.$i.'_err'})?' class="has-error"':'';?>>
															<textarea class="form-control input-sm" name="data_format<?=$i?>" rows="1"><?=htmlspecialchars($v->data_format)?></textarea>
															<?php if(isset(${'data_format'.$i.'_err'})){?><p class="help-block"><?=${'data_format'.$i.'_err'}?></p><?php }?>
														</td>
														<td<?php echo isset(${'sort'.$i.'_err'})?' class="has-error"':'';?>>
															<input type="text" sort_flg=1 class="form-control input-sm" name="sort<?=$i?>" value="<?=htmlspecialchars($v->sort)?>" />
															<?php if(isset(${'sort'.$i.'_err'})){?><p class="help-block"><?=${'sort'.$i.'_err'}?></p><?php }?>
														</td>
														<td<?php echo isset(${'index_flg'.$i.'_err'})?' class="has-error"':'';?>>
															<input type="text" class="form-control input-sm" name="index_flg<?=$i?>" value="<?=htmlspecialchars($v->index_flg)?>" />
															<?php if(isset(${'index_flg'.$i.'_err'})){?><p class="help-block"><?=${'index_flg'.$i.'_err'}?></p><?php }?>
														</td>
														<td><a name="btn<?=$i?>" class="black" href="javascript:void($('#dtr<?=$i?>').remove());"><i class="fa fa-trash-o"></i> 删除</a></td>
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
								<?=form_close()?>
							</div> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript">
function addRow(){
	var idx = $('#end').val();
	var str = '<tr id="dtr'+idx+'">';
	str += '<td><input type="hidden" name="id'+idx+'" value="" /><input type="hidden" name="update_time'+idx+'" value="" />';
	str += '<input type="text" class="form-control input-sm" name="code'+idx+'" value="" /></td>';
	str += '<td><input type="text" class="form-control input-sm" name="name'+idx+'" value="" /></td>';
	str += '<td><select name="data_config'+idx+'" class="form-control  input-sm" style="width:130px;">';
	<?php foreach ($format_type_list as $sk => $sv){?>
	str += '<option value="<?=htmlspecialchars($sk)?>"><?=htmlspecialchars($sv)?></option>';
	<?php }?>
	str += '</select></td>';
	str += '<td><textarea name="data_format'+idx+'" class="form-control input-sm" rows="1"></textarea></td>';
	var sort = $("input[sort_flg]").length+1;
	$("input[sort_flg]").each(function(){
		var tmp = $(this).val()*1;
		if(tmp >= sort){
			sort = tmp + 1;
		}
	});
	str += '<td><input type="text" sort_flg=1 class="form-control input-sm" id="sort'+idx+'" name="sort'+idx+'" value="'+sort+'"" /></td>';
	str += '<td><input type="text" class="form-control input-sm" id="index_flg'+idx+'" name="index_flg'+idx+'" value="0"" /></td>';
	str += '<td><a name="btn'+idx+'" class="black" href="javascript:void($(\'#dtr'+idx+'\').remove());"><i class="fa fa-trash-o"></i> 删除</a></td>';
	str += '</tr>';
	$("#dtb").append(str);
	$('#end').val($('#end').val()*1+1);
}
</script>

<!-- END PAGE CONTENT-->
