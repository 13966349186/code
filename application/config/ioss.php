<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$config['profiler_key'] = 'show';
//每页显示记录数
$config['per_page'] = '20';

$config['image_url'] = '/';
$config['image_url_ssl'] = '/';

$config['not_safe_file_tail'] = Array("php", "exe", "so", "a", "sh");
//跨域上传图片地址 ===========>>>>>该上传地址处的php文件中需要对当前站点添加域名承认
$config['upload_server'] = 'http://192.168.199.86/webuploader-0.1.5/svr/fileupload.php';
//跨域上传图片后回显地址
$config['upload_show'] = 'http://192.168.199.86/webuploader-0.1.5/svr/';

//文件上传目录
$config['upload_path'] = 'upload';
$config['tmp_path'] = 'upload/tmp';  //临时文件保存目录 (必须是当前站点下的相对目录)
$config['upload_default_path'] = 'upload/default';
$config['order_type'] = Array(
	'0'=>'网站购买',
	'1'=>'ebay',
	'2'=>'赠送',
);

$config['hold_reason'] = Array(
	'110'=>'无法发货',
    '120'=>'封号赔偿',
	'200'=>'投诉处理',
    '900'=>'其他'
);

//商品的数据模型定义
$config['model']['gold'] =  'gold';
$config['model']['item'] =  'item';