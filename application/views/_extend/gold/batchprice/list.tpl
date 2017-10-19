<script type="text/javascript">
$(document).ready(function(){
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='type_id']",
		url: "<?=site_url('common/Ajax/GetTypes')?>/{0}",
		empty_option: '请选择类型...',
		init_child_val: "<?=$type_id?>"
	});
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='category_id']",
		url: "<?=site_url('common/Ajax/GetCategorys')?>/{0}",
		empty_option: '请选择目录...',
		init_child_val: "<?=$category_id?>"
	});
	$(".button-next").click(function(){
		var game_id = $("[name='game_id']").find("option:selected").val();
		var category_id = $("[name='category_id']").find("option:selected").val();
		var type_id = $("[name='type_id']").find("option:selected").val();
		if(game_id <= 0 || category_id <= 0 || type_id <= 0){
			alert("请选择筛选条件");
			return false;
		}
		window.location.href = "<?=site_url($thisModule.$thisController.'/edit')?>"+"/"+game_id+"/"+category_id+"/"+type_id;
	});
});
</script>

<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl', Array('_NO_IOSS_NEW'=>'1'))?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption('价格批量更新')?>
			<div class="portlet-body form">
			<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<div class="form-body">
						<?= edit_form_dropdown('game_id',array(''=>'请选择游戏...')+$this->games->toArray(), $game_id, '游戏');?>
						<?= edit_form_dropdown('category_id',array(''=>'请选择目录...'), $category_id ,'目录');?>
						<?= edit_form_dropdown('type_id',array(''=>'请选择类型...'), $type_id, '类型');?>	
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-xs-offset-3 col-xs-4">
								<button type="button"  class="btn blue button-next" >下一步 <i class="m-icon-swapright m-icon-white"></i></button>
							</div>
						</div>
					</div>
				<?=form_close()?>
			</div>
		</div>
	</div>
</div>
<!-- END PAGE CONTENT-->