<?php $modalId = 'modalId'.time().rand(1, 10000);?>
<div class="portlet-body form" id="<?=$modalId?>">
	<div class='form-horizontal' role='form'>
		<div class="modal-body">
			<div class="form-body">
				<div class="row">
					<div class="col-xs-12" style="">
<?php
$messages = $this->session->flashdata('flash_messages');
foreach ($messages as $v){
?>
<div class="<?= ($v['type'] == 'success')?"alert alert-success":"Metronic-alerts alert alert-danger fade in" ?>">
	<i class="<?= ($v['type'] == 'success')?"fa fa-check":"fa-lg fa fa-warning"?>"></i>
	<?=$v['text']?>
</div>
<?php }?>
					</div>
			<div class="col-xs-12">
				<div class="form-group modal-footer-style pull-right">
					<button type="button" class="btn default" data-dismiss="modal">关闭</button>
				</div>
			</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$("#<?=$modalId?>").parent().find("form").ajaxForm({
	target:$("#<?=$modalId?>").parent()
});
refreshShow($("#<?=$modalId?>").parent());
</script>
