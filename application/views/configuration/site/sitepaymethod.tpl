<!-- BEGIN PAGE HEADER-->
<?php $this->load->view('partials/breadcrumb.tpl')?>
<!-- END PAGE HEADER-->
<?=form_open('', Array('class'=>'form-horizontal'))?>
<div class="row">
<div class="col-xs-12 ">
<div class="portlet box green ">
	<?=edit_form_caption('网站【'.htmlspecialchars($site->name).'】的支付方式配置')?>
	<div class="portlet-body">
		<div class="table-responsive">
			<table class="table table-hover table-bordered table-striped">
			<thead>
			<tr>
				<th>支付方式</th>
				<th>支付账号</th>
				<th width="1%"><div style="width:100px;">支付方式排序</div></th>
				<th width="1%"><div style="width:80px;">操作</div></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($paymentmethod as $k=>$v){?>
			<tr>
				<td><?=$v?></td>
				<td<?=form_error('account_id'.$k)?' class="has-error"':''?>>
					<select name="account_id<?=$k?>">
						<option value=""></option>
					<?php if(array_key_exists($k, $accountDropList)){?>
						<?php foreach ($accountDropList[$k] as $acct){?>
						<option value="<?=$acct->id?>" <?=set_select('account_id'.$k, $acct->id, $acct->site_payment_id?true:false)?>><?=htmlspecialchars($acct->account)?></option>
						<?php }?>
					<?php }?>
					</select>
					<?=form_error('account_id'.$k, '<p class="help-block">', '</p>')?>
				</td>
				<td<?=form_error('sort'.$k)?' class="has-error"':''?>>
					<input type="text" class="form-control input-sm" name="sort<?=$k?>" value="<?=set_value('sort'.$k, array_key_exists($k, $defaultVals)?$defaultVals[$k]->sort:'')?>" />
					<?=form_error('sort'.$k, '<p class="help-block">', '</p>')?>
				</td>
				<td>
					<label class="checkbox-inline checkbox-pt checkbox-pl">
						<input type="checkbox" data-id="<?=$k?>" name="add<?=$k?>" value="1" <?=set_checkbox('add'.$k, '1', array_key_exists($k, $defaultVals))?>/>
						添加
					</label>
				</td>
			</tr>
			<?php }?>
			</tbody>
			</table>
		</div>
		<div class="form-actions">
			<div class="row">
				<div class="col-xs-offset-3 col-xs-4">
					<button type="submit" class="btn green <?=$p->edit?'':'disabled'?>">保存</button>
					<a href="<?=site_url($thisModule.$thisController)?>" class="btn default">返回</a>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<?=form_close()?>
<script>
function refreshItem(id){
	if(!$("[name=add"+id+"]").is(':checked')){
		$("[name=account_id"+id+"]").val('');
		$("[name=sort"+id+"]").val('');
		$("[name=account_id"+id+"]").attr('disabled', true);
		$("[name=sort"+id+"]").attr('readonly', true);
	}else{
		$("[name=account_id"+id+"]").attr('disabled', false);
		$("[name=sort"+id+"]").attr('readonly', false);
	}
}
$(document).ready(function(){
	$('[data-id]').change(function(){
		refreshItem($(this).attr('data-id'));
	});
	$('[data-id]').each(function(){
		refreshItem($(this).attr('data-id'));
	});
});
</script>
