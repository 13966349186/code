<div class="page-sidebar-wrapper">
	<div class="page-sidebar navbar-collapse collapse">
		<!-- BEGIN SIDEBAR MENU -->
		<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
		<?php
		$first_flg = true;
		foreach ($leftMenu as $node){
		?>
		<?php if(count($node->pages)>0){?>
			<li class="<?=$node->sel?' active open':''?><?=$first_flg?' start':''?>">
				<a href="<?=count($node->pages)>0?'javascript:void(0);':site_url($node->ctrl)?>">
				<i class="<?=$node->icon?>"></i>
				<span class="title"><?=$node->name?></span>
			<?php if($node->sel){?><span class="selected"></span><?php }?>
				<span class="arrow<?=$node->sel?' open':''?>"></span>
				</a>
				<ul class="sub-menu">
			<?php foreach ($node->pages as $pageId => $page){?>
					<li<?=$page->sel?' class="active"':''?>>
						<a href="<?=site_url($page->ctrl)?>">
						<i class="<?=$page->icon?>"></i>
						<?=$page->name?>
						</a>
					</li>
			<?php }?>
				</ul>
			</li>
		<?php }else{?>
			<li class="tooltips<?=$node->sel?' active open':''?><?=$first_flg?' start':''?>">
				<a href="<?=site_url($node->ctrl)?>">
					<i class="<?=$node->icon?>"></i>
					<span class="title"><?=$node->name?></span>
				</a>
			</li>		
		<?php }?>
	<?php
			$first_flg = false;
		}
	?>
		</ul>
		<!-- END SIDEBAR MENU -->
	</div>
</div>
