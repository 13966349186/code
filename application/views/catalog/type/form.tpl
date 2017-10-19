<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="portlet-body">
	<div class="tabbable-line boxless">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#tab_c" data-toggle="tab">1.基本信息 </a>
			</li>
			<li<?=$thisMethod == 'edit'?'':' class="disabled"'?>>
				<a href="<?=$thisMethod == 'edit'?site_url($this->_thisModule.$this->_thisController.'/product_attr/'.$vo->id):'javascript:void(0);'?>">2.商品属性配置</a>
			</li>
			<li<?=$thisMethod == 'edit'?'':' class="disabled"'?>>
				<a href="<?=$thisMethod == 'edit'?site_url($this->_thisModule.$this->_thisController.'/order_attr/'.$vo->id):'javascript:void(0);'?>">3.发货信息配置 </a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="tab_c">
				<div class="row">
					<div class="col-xs-12 ">
						<!-- BEGIN SAMPLE FORM PORTLET-->
						<div class="portlet box green">
							<?=edit_form_caption('Step1', '<i class="fa fa-cogs"></i>')?>
							<div class="portlet-body form">
								<?=form_open('','class="form-horizontal"')?>
									<input type="hidden" id="update_time" name="update_time" value="<?=set_value('update_time', $vo->update_time)?>" />
									<div class="form-body">
										<?=edit_form_dropdown('game_id', Array(''=>'请选择游戏...')+parse_options($this->games), set_value('game_id', $vo->game_id), '所属游戏', $thisMethod == 'edit'?'disabled="disabled"':'')?>
										<?=edit_form_dropdown('model', Array(''=>'选择数据模型...')+$models, set_value('model', $vo->model), '数据模型', $thisMethod == 'edit'?'disabled="disabled"':'')?>
										<?=edit_form_input('code', set_value('code', $vo->code), '类型标识', $vo->id>0?'disabled="disabled"':'')?>
										<?=edit_form_input('name', set_value('name', $vo->name), '名称')?>
										<?=edit_form_textarea('note', set_value('note', $vo->note), '备注')?>
										<?=edit_form_radio_list('state', $states, set_value('state', ($thisMethod == 'edit'?$vo->state:'1')), '状态')?>
									</div>
									<div class="form-actions">
										<div class="row">
											<div class="col-xs-offset-3 col-xs-4">
												<button type="submit" class="btn green">保存并继续</button>
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
			</div>
		</div>
	</div>
</div>
<!-- END PAGE CONTENT-->
