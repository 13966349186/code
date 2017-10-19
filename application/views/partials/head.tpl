<div class="page-header -i navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="javascript:void(0);">
			<img src="<?=image_url('/static/assets/admin/layout/img/logo.png')?>" alt="logo" class="logo-default"/>
			</a>
			<div class="menu-toggler sidebar-toggler "></div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:void(0);" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				<!-- BEGIN NOTIFICATION DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-user">
					<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<span class="username username-hide-on-mobile">
					<?=htmlspecialchars($_user->name)?> [<?=htmlspecialchars($_user->account)?>] </span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a href="<?=site_url('/mainpannel/logininfo')?>">
							<i class="icon-user"></i> 登录信息 </a>
						</li>
						<li>
							<a href="<?=site_url('/mainpannel/password')?>">
							<i class="icon-lock"></i> 修改密码 </a>
						</li>
					<?php $cacheP = UserPower::getPermisionInfo('mainpannel/cache');?>
					<?php if($cacheP->read){?>
						<li class="divider"></li>
						<li>
							<a href="<?=site_url('/mainpannel/cache')?>">
							<i class="fa fa-recycle"></i> 缓存 </a>
						</li>
					<?php }?>
						<li class="divider"></li>
						<li>
							<a href="<?=site_url('/auth/logout')?>">
							<i class="fa fa-power-off"></i> 退出 </a>
						</li>
					</ul>
				</li>
				<!-- END USER LOGIN DROPDOWN -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>