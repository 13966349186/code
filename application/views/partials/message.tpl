<?php
$messages = $this->session->flashdata('flash_messages');
?>
<?php if($messages){?>
<script>
$(document).ready(function(){
	<?php 	foreach ($messages as $v){ 	?>
	$.bootstrapGrowl('<?=$v['text'] ?>', {
		  ele: 'body', 
		  type: '<?=$v['type'] ?>', // (null, 'info', 'danger', 'success')
		  offset: {from: 'top', amount: 10}, 
		  align: 'center', 
		  width: 500, // (integer, or 'auto')
		  delay: 4000, 
		  allow_dismiss: true,
		  stackup_spacing: 10
		});
	<?php }?>
});
</script>
<?php 	} 	?>

