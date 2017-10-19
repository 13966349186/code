<?php
$flash_err = $this->session->flashdata('error');
$flash_success = $this->session->flashdata('success');
if(!isset($error)){
	$error = $flash_err;
}
if(!isset($success)){
	$success = $flash_success;
}
?>
<?php if((isset($error) && $error)){ ?>
<p class="text-danger"><?=$error?></p>
<?php }else if((isset($success) && $success)){ ?>
<p class="text-success"><?=$success?></p>
<?php } ?>
