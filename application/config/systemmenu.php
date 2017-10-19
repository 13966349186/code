<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['menu_group_icon'] = Array(
	'权限管理'=>'icon-users'
	,'基础配置'=>'icon-settings'
	,'邮件管理'=>'icon-envelope-open'
	,'商品管理'=>' icon-bag'
	,'CMS管理'=>'fa fa-file-word-o'
	,'销售管理'=>'icon-basket'
	,'财务管理'=>'fa fa-money'
	,'金币'=>'icon-puzzle'
	,'报表'=>'fa fa-bar-chart-o'
	,'FIFA'=>'fa fa-folder-open-o'
); 
$config['menu_page']['mainpannel/logininfo']	= array('id'=>'1',	'name'=>'登录信息',		'class'=>'个人信息',		'group'=>GROUP_EVERYONE,	'hide'=>1, 'icon'=>'');
$config['menu_page']['mainpannel/password']		= array('id'=>'2',	'name'=>'修改密码',		'class'=>'个人信息',		'group'=>GROUP_EVERYONE,	'hide'=>1, 'icon'=>'');
$config['menu_page']['permission/admin']		= array('id'=>'101',	'name'=>'账号管理',		'class'=>'权限管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'icon-user');
$config['menu_page']['permission/role']			= array('id'=>'102',	'name'=>'角色管理',		'class'=>'权限管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-key');
$config['menu_page']['permission/adminlogin']	= array('id'=>'103',	'name'=>'登录日志',		'class'=>'权限管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-user');
$config['menu_page']['mainpannel/cache']			= array('id'=>'901', 	'name'=>'缓存清理',		'class'=>'缓存清理',		'group'=>GROUP_ADMIN,		'hide'=>1, 'icon'=>'fa fa-clipboard');

$config['menu_page']['configuration/site']		= array('id'=>'201',	'name'=>'网站管理',		'class'=>'基础配置',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-picture-o');
$config['menu_page']['configuration/currency']	= array('id'=>'202',	'name'=>'货币配置',		'class'=>'基础配置',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-money');
$config['menu_page']['configuration/paymentaccount']	= array('id'=>'203',	'name'=>'支付账号',		'class'=>'基础配置',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-credit-card');

$config['menu_page']['cms/data']				= array('id'=>'211',	'name'=>'文章内容',		'class'=>'基础配置',		'group'=>GROUP_ADMIN,		'hide'=>1, 'icon'=>'fa fa-file-text');
$config['menu_page']['cms/model']				= array('id'=>'212',	'name'=>'文章类型',		'class'=>'基础配置',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-clipboard');
$config['menu_page']['cms/category']			= array('id'=>'213',	'name'=>'文章目录',		'class'=>'基础配置',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-th-list');

$config['menu_page']['configuration/mailtemplate']		= array('id'=>'220',	'name'=>'邮件模板',		'class'=>'邮件管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-building');
$config['menu_page']['sales/maillog']			= array('id'=>'450',	'name'=>'邮件日志',		'class'=>'邮件管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-envelope-square');

$config['menu_page']['catalog/game']			= array('id'=>'301',	'name'=>'游戏/目录',		'class'=>'商品管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-gamepad');
$config['menu_page']['catalog/category']		= array('id'=>'302',	'name'=>'目录管理',		'class'=>'商品管理',		'group'=>GROUP_ADMIN,		'hide'=>1, 'icon'=>'fa fa-list');
$config['menu_page']['catalog/type']			= array('id'=>'303',	'name'=>'类型管理',		'class'=>'商品管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-th-large');
$config['menu_page']['catalog/product']			= array('id'=>'310',	'name'=>'商品管理',		'class'=>'商品管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'icon-bag');

$config['menu_page']['sales/order']				= array('id'=>'401',	'name'=>'订单',			'class'=>'销售管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'icon-basket');
$config['menu_page']['sales/orderpayment']		= array('id'=>'403',	'name'=>'订单支付',		'class'=>'销售管理',		'group'=>GROUP_ADMIN,		'hide'=>1, 'icon'=>'icon-basket');
$config['menu_page']['sales/orderverify']		= array('id'=>'404',	'name'=>'订单验证',		'class'=>'销售管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'icon-question');
$config['menu_page']['sales/orderrelative']		= array('id'=>'405',	'name'=>'关联订单',		'class'=>'销售管理',		'group'=>GROUP_ADMIN,		'hide'=>1, 'icon'=>'icon-basket');
$config['menu_page']['sales/delivery']			= array('id'=>'412',	'name'=>'订单发货',		'class'=>'销售管理',		'group'=>GROUP_ADMIN,		'hide'=>1, 'icon'=>'icon-basket');

$config['menu_page']['sales/salelist']			= array('id'=>'430',	'name'=>'订单商品',		'class'=>'销售管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'icon-handbag');
$config['menu_page']['sales/preorder']			= array('id'=>'440',	'name'=>'未生成订单',	'class'=>'销售管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'icon-ban');
$config['menu_page']['finance/orderpayment']	= array('id'=>'510',	'name'=>'交易流水',	'class'=>'财务管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'icon-list');
$config['menu_page']['finance/paypaltxn']		= array('id'=>'511',	'name'=>'PayPal流水',	'class'=>'财务管理',		'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'icon-list');

$config['menu_page']['report/finance']			= array('id'=>'601',	'name'=>'财务报表',			'class'=>'报表',			'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-bar-chart-o');
$config['menu_page']['report/sales']			= array('id'=>'602',	'name'=>'销售报表',			'class'=>'报表',			'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-bar-chart-o');
$config['menu_page']['report/orderverifyreport']			= array('id'=>'603',	'name'=>'验证统计',			'class'=>'报表',			'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-bar-chart-o');
$config['menu_page']['report/fifa']				= array('id'=>'630',	'name'=>'FIFA报表',		'class'=>'报表',			'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'fa fa-bar-chart-o');

$config['menu_page'][EXTEND_ROOT.'/gold/batchprice']	= array('id'=>'311',	'name'=>'金币批量定价',	'class'=>'金币',			'group'=>GROUP_ADMIN,		'hide'=>0, 'icon'=>'icon-puzzle');

