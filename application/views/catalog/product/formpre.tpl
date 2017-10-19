<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<script type="text/javascript">
$(document).ready(function(){
	$("[name='game_id']").RelativeDropdown({
		childid: "[name='type_id']",
		url: "<?=site_url('common/Ajax/GetTypes')?>/{0}",
		empty_option: '请选择类型...',
		init_child_val: "<?=set_value('type_id')?>"
	});
});
</script>
<div class="row">
	<div class="col-xs-12 ">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box green ">
			<?=edit_form_caption('添加商品 - 步骤1')?>
			<div class="portlet-body form">
				<?=form_open('', Array('class'=>'form-horizontal', 'role'=>'form'))?>
					<div class="form-body">
						<?=edit_form_dropdown('game_id', Array(''=>'请选择游戏...')+parse_options($this->games), set_value('game_id', ''), '游戏')?>
						<?=edit_form_dropdown('type_id', Array(''=>'请选择类型...'), '', '类型')?>
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-xs-offset-3 col-xs-4">
								<?php if($p->add){?><button type="submit" class="btn green">下一步</button><?php }?>
								<a href="<?=site_url($thisModule.$thisController)?>" class="btn default">返回</a>
							</div>
						</div>
					</div>
				<?=form_close()?>
			</div>
		</div>
		<!-- END SAMPLE FORM PORTLET-->
		<!-- BEGIN SAMPLE FORM PORTLET-->
	</div>
</div>
<!-- END PAGE CONTENT-->
