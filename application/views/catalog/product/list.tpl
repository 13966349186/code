<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_BTN_GROUP'=>array('url'=>site_url($thisModule.$thisController.'/addpre'), 'class'=>'btn blue' . ($p->add ? '':' disabled'), 'name'=>'新建') ))?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript">
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
		alert("请选择要操作的商品！");
		return false;
	}
	if(!confirm("确定要 "+$("#user_op").find("option:selected").html()+" 选中的商品么？")){
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
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='type_id']",
		url: "<?=site_url('common/Ajax/GetTypes')?>/{0}",
		empty_option: '请选择类型...',
		init_child_val: "<?=filterValue('type_id')?>"
	});
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='category_id']",
		url: "<?=site_url('common/Ajax/GetCategorys')?>/{0}",
		empty_option: '请选择目录...',
		init_child_val: "<?=filterValue('category_id')?>"
	});
	$("#reset").click(function(){
		$("select").val('');
		$("input[type='text']").val('');
	});
	
});
</script>
<div class="row">
	<div class="col-xs-12">
		<div class="portlet">
			<?=form_open('')?>
			<div class="portlet-title portlet-title-noborder">
				<div class="form-group">
					<div class="form-inline"  role='form'>
							<?=search_form_dropdown('game_id',array(''=>'请选择游戏...')+parse_options($this->games),filterValue('game_id'),'游戏');?>
							<?=search_form_dropdown('type_id',array(''=>'请选择类型...'),filterValue('type_id'),'类型');?>
							<?=search_form_dropdown('category_id',array(''=>'请选择目录...'),filterValue('category_id'),'目录');?>
							<div class="form-group"><button type="button" class="btn default" id="reset">重置条件</button></div>
							<div class="form-group"><button type="submit" class="btn green about_search">搜索 <i class="fa fa-search"></i></button></div>
					 </div>
				</div>
				<div class="form-group ">
					<div class="form-inline">
							<?=search_form_input('name',filterValue('name'),'商品名称...'); ?>
							<?=search_form_dropdown('state',array(''=>'所有状态') + $this->MProduct->states ,filterValue('state'),'状态');?>
							<div class="form-group"><lable><input type="checkbox"  id="sort" name="sort" value="1" class="icheck" data-checkbox="icheckbox_square-grey"  <?=set_checkbox('sort', '1', filterValue('sort')?true:false)?> >使用排序</lable></div>
							</div>
				</div>
			</div>
			<?=form_close()?>
			<div class="portlet-body">
				<?=form_open('/'.$thisModule.$thisController.'/batch', Array('id'=>'cat_mng_frm'))?>
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<div class="table-group-actions">
							<select name="user_op" id="user_op" class="table-group-action-input form-control input-inline input-small ">
								<option value="">批量操作...</option>
								<?php
								foreach ($actions as $k=>$v){
									echo "<option value='$k'>$v</option>";
								} 
								?>
							</select>
							<a href="javascript:void(0);" class="btn btn-success table-group-action-submit" onclick="op_check();"><i class="fa fa-check"></i> 应用</a>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="table-scrollable">
							<table class="table table-hover table-bordered table-striped">
								<thead>
									<tr>
										<th width="3%"><input type="checkbox" id="checkall" name="checkall" /></th>
										<th style="width:14%;">游戏</th>
										<th style="width:12%;">类型</th>
										<th >目录</th>
										<th >名称</th>
										<th style="width:10%;">价格</th>
										<th style="width:6%;">排序</th>
										<th style="width:6%;">状态</th>
										<th style="width:6%;">操作</th>
									</tr>
								</thead>
								<tbody>
								<?php $productStatesCls = $this->config->item('product_state_css');?>
								<?php foreach ($lst as $v){?>
									<tr>
										<td><input type="checkbox" name="id_time[<?=$v->id?>]" value="<?=$v->update_time?>" /></td>
										<td><?=htmlspecialchars($this->games[$v->game_id])?></td>
										<td><?=htmlspecialchars($types[$v->type_id])?></td>
										<td><?=htmlspecialchars($v->category_name)?></td>
										<td>	<?=htmlspecialchars($v->name)?></td>
										<td><?=$this->currency->format($v->price, DEFAULT_CURRENCY)?></td>
										<td><?=htmlspecialchars($v->sort)?></td>
										<td><span class="<?=element($v->state, $productStatesCls, '')?>"><?=element($v->state, $this->MProduct->states,'')?></span></td>
										<td><a href="<?=site_url(ioss_extend_route($thisController.'/edit/', $types->getModel($v->type_id)). '/' .$v->id)?>" class="about_edit btn <?=$p->edit?'':'disabled' ?>" ><i class="fa fa-edit"></i> <?=l('edit')?></a></td>
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
	</div>
</div>
<!-- END PAGE CONTENT-->