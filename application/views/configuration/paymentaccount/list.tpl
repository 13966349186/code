<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<script>
$(function(){
	$(".deleteBtn").click(function(){
		$("#static").show();
		$("#confirmBtn").attr('href', $(this).attr('href'));
		return false;
	});

	$(".close").click(function(){
		$("#static").hide();
	});

	$("#cancel").click(function(){
		$("#static").hide();
	});
});
</script>

<div id="static" class="modal fade in" data-keyboard="false" data-backdrop="static" tabindex="-1" style="display: none; padding-right: 17px;" aria-hidden="false">
<div class="modal-backdrop fade in" ></div>
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button class="close" aria-hidden="true" data-dismiss="modal" type="button"></button>
<h4 class="modal-title">Confirmation</h4>
</div>
<div class="modal-body">
<p> 确认 <span class="font-red">删除</span> 这个支付账号吗? </p>
</div>
<div class="modal-footer">
<a class="btn green" data-dismiss="modal" type="button" id="confirmBtn" href="#">确定</a>
<button class="btn default" data-dismiss="modal" type="button" id="cancel">取消</button>
</div>
</div>
</div>
</div>


<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-xs-12">
		<!-- Begin: life time stats -->
		<div class="portlet">
			<?=form_open('', Array('class'=>'form-inline', 'role'=>'form'))?>
				<?=search_form_dropdown('method_id',array(''=>'支付方式...')+$paymentMethodNames,filterValue('method_id'),'支付方式');?>
				<?=search_form_dropdown('state',array(''=>'所有状态')+$this->MPaymentAccount->getState(), filterValue('state'),'状态','class="form-control input-small"') ?>
				<button type="submit" class="btn green about_search">搜索 <i class="fa fa-search"></i></button>
			<?=form_close()?>
			<div class="portlet-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="table-scrollable">
							<table class="table table-hover table-bordered table-striped">
								<thead>
								<tr>
									<th width="25%">支付方式CODE</th>
									<th width="25%">支付方式名称</th>
									<th width="28%">账号</th>
									<th width="8%">状态</th>
									<th width="14%">操作</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($paymentAccounts as $account){?>
									<tr>
										<td><?=$paymentMethodCodes[$account->method_id]?></td>
										<td><?=$paymentMethodNames[$account->method_id]?></td>
										<td><?=htmlspecialchars($account->account)?></td>
										<td><?=($account->state == MPaymentAccount::STATE_DISABLE ? '<span class="label label-danger status_icons label_status_icons">禁用</span>' : '<span class="label label-success status_icons label_status_icons">启用</span>')?></td>
										<td>
										<a href="<?=site_url($thisModule.$thisController.'/edit/'.$account->id)?>" class="about_edit btn <?=$p->edit?'':'disabled' ?>"><i class="fa fa-edit"></i> 编辑</a>
										<a class="about_edit btn deleteBtn<?=$p->edit?'':'disabled' ?>" data-toggle="modal"  href="<?=site_url($thisModule.$thisController.'/delete/' . $account->id  .'/' . $account->update_time)?>"><i class="fa fa-trash-o"  ></i> 删除</a>
										</td>
									</tr>
								<?php }?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12  text-right">
						<?=$pagination?>
					</div>
				</div>
			</div>
		</div>
		<!-- End: life time stats -->
	</div>
</div>

<!-- END PAGE CONTENT-->

