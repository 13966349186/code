<?php 
if(!isset($_BREADCRUMB)){
	//默认面包屑：  模块 》控制器 》方法
	$_BREADCRUMB[] = array(
			'name'=>$thisControllerName,
			'url'=>($thisControllerPath)?site_url($thisControllerPath):''
	);
	if(isset($breadName) && $breadName){
		$_BREADCRUMB[] = array(	'name'=>$breadName);
	}elseif($thisMethod != 'index'){
		$_BREADCRUMB[] = array(	'name'=>l($thisMethod));
	}
}
?>
<div class="page-bar">
	<ul class="page-breadcrumb">
	<li><i class="fa fa-home"></i> <span><?=(isset($ctrlObj) && $ctrlObj)?$ctrlObj->class:'IOSS' ?></span></li>
	<?php foreach ($_BREADCRUMB as $b){?>
	<li>
		<i class="fa fa-angle-right"></i>
		<?php 
		if(isset($b['url']) && !empty($b['url'])){
			echo "<a href='{$b['url']}'>{$b['name']}</a>";
		}else{
			echo "<span>{$b['name']}</span>";
		} ?>
	</li>
	<?php }?>
	</ul>
	<div class="page-toolbar">
		<div class="btn-group pull-right">
	<?php 
		if($thisMethod == 'index' && !isset($_NO_IOSS_NEW) && !isset($_BTN_GROUP)){
			$_BTN_GROUP = array('url'=>site_url($thisModule.$thisController.'/add'), 'class'=>'btn blue' . ($p->add ? '':' disabled'), 'name'=>'新建');
		}
		if(isset($_BTN_GROUP) && is_array($_BTN_GROUP)){
	?>
			<a href="<?=element('url', $_BTN_GROUP, '')?>" type="button" class="<?=element('class', $_BTN_GROUP, 'btn default') ?>" ><?= element('i', $_BTN_GROUP, '') ?> <?=element('name', $_BTN_GROUP, '$nbsp;')?></a>
	<?php 
		}elseif(isset($_BTN_GROUP) && is_string($_BTN_GROUP)){
			echo $_BTN_GROUP;
		}
	?>
		</div>
	</div>
</div>