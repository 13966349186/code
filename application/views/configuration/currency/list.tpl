<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_NO_IOSS_NEW'=>'1'))?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script>
var running = false;

$(document).ready(function(){

	//焦点离开"保存"按钮是删除错误提示
	$("[btn='save']").blur( function(){
		var id = $(this).attr("sid");
		$("#popover_"+id).popover('destroy');
	});
	
	$("[btn='edit']").on('click', function(){
		if(running){return false;}
		var id = $(this).attr("sid");
		$("#span_"+id).hide();
		$("#edit_"+id).hide();
		$("#input_"+id).show();
		$("#save_"+id).show();
	});
	
	$("[btn='save']").on('click', function(){
		if(running){return false;}
		running = true;
		var id = $(this).attr("sid");
		var val = $.trim($("#input_"+id).val());
		$("#input_"+id).removeClass('text-danger');
		var ajData = "exchange_rate=" + encodeURIComponent(val);
		ajData += "&update_time=" + encodeURIComponent($("#update_time_"+id).val());
		$.ajax({
			url: "<?=site_url($thisModule.$thisController.'/edit')?>/" + id,
			data: ajData,
			type: "POST",
			dataType: "json",
			success: function (res) {
				running = false;
				if(res.code == 1){
					$("#span_"+id).html(val);
					$("#update_time_"+id).val(res.update_time);
					$("#input_"+id).val(res.value);
					$("#span_"+id).html(res.value);
					$("#span_"+id).show();
					$("#edit_"+id).show();
					$("#input_"+id).hide();
					$("#save_"+id).hide();
				}else{
					$("#input_"+id).addClass('text-danger');
					$("#popover_"+id).popover({
						animation:true,
						placement:'top',
						content:res.msg
					});
					$("#popover_"+id).popover('show');
					$("#popover_"+id).unbind('click');
				}
			},
			error: function (xhr, status) {
				running = false;
				alert("连接服务器失败！");
			}
		});
	});
	
});
</script>
<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<?=form_open('', Array('class'=>'form-inline', 'role'=>'form'))?>
				<?=search_form_input('name',filterValue('name'),'货币名称', 'class="form-control input-medium"');?>
				<button type="submit" class="btn green">搜索<i class="fa fa-search"></i></button>
			<?=form_close()?>
			<div class="portlet-body">
				 <div class="row">
				 	<div class="col-xs-12">
				 		<div class="table-scrollable">
					 		<table class="table table-hover table-bordered table-striped">
								<thead>
								<tr>
									<th>名称</th>
									<th width="20%">代码</th>
									<th width="20%">输出格式</th>
									<th width="20%">汇率(1美元)</th>
									<th width="10%">操作</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($currencys as $currency){?>
								<tr>
									<td><?=htmlspecialchars($currency->name)?></td>
									<td><?=htmlspecialchars($currency->code)?></td>
									<td><?=htmlspecialchars($currency->format)?></td>
									<td id="td_<?=$currency->id?>">
										<span id="span_<?=$currency->id?>"><?=htmlspecialchars($currency->exchange_rate)?></span>
										<div id="popover_<?=$currency->id?>"><input type="text" class="form-control" id="input_<?=$currency->id?>" value="<?=$currency->exchange_rate?>" style="display:none;" /></div>
										<input type="hidden" class="form-control" id="update_time_<?=$currency->id?>" value="<?=$currency->update_time?>" />
									</td>
									<td>
										<?php if($p->edit){?>
										<a href="javascript:void(0);" btn="edit" id="edit_<?=$currency->id?>" sid="<?=$currency->id?>" class="about_edit"><i class="fa fa-edit"></i> 编辑</a>
										<a href="javascript:void(0);" btn="save" id="save_<?=$currency->id?>" sid="<?=$currency->id?>" class="about_edit" style="display:none;"><i class="fa fa-save"></i> 保存</a>
										<?php } else{ echo '&nbsp;'; } ?>
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