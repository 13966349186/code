<?php $modalId = 'modalId'.time().rand(1, 10000);?>
<?php if(!isset($no_modal_head) || !$no_modal_head){?>
<div class="modal-header" id="<?=$modalId?>">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h4 class="modal-title"><b><?=isset($thisControllerName)?$thisControllerName:''?></b></h4>
</div>
<?php }?>
<?php $this->load->view($hero)?>
<script type="text/javascript">
$("#<?=$modalId?>").parent().find("form").ajaxForm({
	target:$("#<?=$modalId?>").parent()
});
refreshShow($("#<?=$modalId?>").parent());
ajaxLink($('#<?=$modalId?>').parent().find(".pagination").find('a'), "#<?=$modalId?>");
ajaxLink($('#<?=$modalId?>').parent().find("[data-ajax=1]"), "#<?=$modalId?>");
if($('#<?=$modalId?>').parent().parent('.modal-dialog').attr('shown_flg') != '1'){
	if(('modal_init' in window) && typeof(window.modal_init) == 'function'){
		window.modal_init();
		delete window.modal_init;
	}
}
</script>
<?php
$messages = $this->session->flashdata('flash_messages');
if($messages){
?>
<script>
$(document).ready(function(){
	<?php 	foreach ($messages as $v){ 	?>
	$.bootstrapGrowl('<?=$v['text'] ?>', {
		  ele: '.modal-content', 
		  type: '<?=$v['type'] ?>', // (null, 'info', 'danger', 'success')
		  offset: {from: 'top', amount: 10}, 
		  align: 'center', 
		  width: 500, // (integer, or 'auto')
		  delay: 8000, 
		  allow_dismiss: true,
		  stackup_spacing: 10
		});
	<?php }?>
});
</script>
<?php 	} 	?>