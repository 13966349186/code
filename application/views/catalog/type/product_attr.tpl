<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="portlet-body">
	<div class="tabbable-line boxless">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?=site_url($this->_thisModule.$this->_thisController.'/edit/'.$type->id)?>">1.基本信息 </a>
			</li>
			<li class="active">
				<a href="#tab_c" data-toggle="tab">2.商品属性配置</a>
			</li>
			<li>
				<a href="<?=site_url($this->_thisModule.$this->_thisController.'/order_attr/'.$type->id)?>">3.发货信息配置 </a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="tab_c">
				<div class="row">
					<div class="col-xs-12">
						<div class="portlet green box">
							<?=edit_form_caption('Step2', '<i class="fa fa-cogs"></i>')?>
							<div class="portlet-body form">
								<?=form_open('','class="form-horizontal"')?>
									<input type="hidden" name="post_flg" value="" /><!-- 为 -->
									<div class="form-body">
										<?=edit_form_dropdown('game_id', $this->games, $type->game_id, '所属游戏', 'disabled="disabled"')?>
										<?=edit_form_dropdown('model', $models, $type->model, '数据模型', 'disabled="disabled"')?>
										<?=edit_form_input('code', $type->code, '类型标识', 'disabled="disabled"')?>
									<?php foreach($defs as $v){ ?> 
										<?php echo cms_form_input($v->data_config, $v->code, $v->name, $v->data_format, $obj->{$v->code});?> 
									<?php 	}?> 
									</div>
									<div class="form-actions">
										<div class="row">
											<div class="col-xs-offset-3 col-xs-4">
												<?php if($p->edit){?><button type="submit" class="btn green">保存并继续</button><?php }?>
											</div>
										</div>
									</div>
								<?=form_close()?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- END PAGE CONTENT-->
