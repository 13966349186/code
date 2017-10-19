<ul class="nav navbar-nav">
<?php if($leftMenu){?>
	<?php foreach ($leftMenu as $node){?>
	<li class="dropdown">
		<a href="<?=count($node->pages)>0?'javascript:void(0);':site_url($node->ctrl)?>" class="dropdown-toggle<?=$node->sel?' active':''?>" data-toggle="dropdown" data-hover="dropdown"><?=$node->name?><span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
		<?php foreach ($node->pages as $pageId => $page){?>
			<li<?=$page->sel?' class="active"':''?>><a href="<?=site_url($page->ctrl)?>"><?=$page->name?></a>
			</li>
		<?php }?>
		</ul>
	</li>

	<?php }?>
<?php }?>
</ul>
