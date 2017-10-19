<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>IOSS后台管理<?=isset($thisControllerName)?' - '.$thisControllerName:''?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<script type="text/javascript">
	window.UEDITOR_HOME_URL = "<?=site_url()?>/plugin/ueditor/";
</script>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="<?=image_url('/static/assets/global/css/google/googleFonts.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/global/plugins/font-awesome/css/font-awesome.min.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/global/plugins/simple-line-icons/simple-line-icons.min.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/global/plugins/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/global/plugins/uniform/css/uniform.default.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css')?>" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?=image_url('/static/assets/global/plugins/icheck/skins/all.css')?>"/>
<link rel="stylesheet" type="text/css" href="<?=image_url('/static/assets/global/plugins/select2/select2.css')?>"/>
<link rel="stylesheet" type="text/css" href="<?=image_url('/static/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css')?>"/>
<link rel="stylesheet" type="text/css" href="<?=image_url('/static/assets/global/plugins/bootstrap-datepicker/css/datepicker.css')?>"/>
<link rel="stylesheet" type="text/css" href="<?=image_url('/static/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')?>"/>
<link rel="stylesheet" type="text/css" href="<?=image_url('/static/assets/global/plugins/jquery-tags-input/jquery.tagsinput.css')?>"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="<?=image_url('/static/assets/global/css/components.css')?>" id="style_components" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/global/css/plugins.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/admin/layout/css/layout.css')?>" rel="stylesheet" type="text/css"/>
<link id="style_color" href="<?=image_url('/static/assets/admin/layout/css/themes/darkblue.css')?>" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="<?=image_url('/static/assets/admin/layout/css/custom.css')?>">
<link rel="stylesheet" href="<?=image_url('/static/assets/admin/pages/css/timeline.css')?>">
<link rel="stylesheet" href="<?=image_url('/static/assets/global/css/global.css')?>">
<!-- END THEME STYLES -->
<link rel="stylesheet" type="text/css" href="<?=image_url('/static/assets/global/plugins/webuploader-0.1.5/js/webuploader.css')?>">
<link rel="stylesheet" type="text/css" href="<?=image_url('/static/assets/global/plugins/webuploader-0.1.5/js/demo.css')?>">

<script type="text/javascript" src="<?=image_url('/static/assets/global/plugins/jquery.min.js')?>"></script>
<script type="text/javascript" src="<?=image_url('/static/js/main.js')?>"></script>
<script src="<?=image_url('/static/js/ajaxfileupload.js')?>" type="text/javascript" ></script>
<link rel="shortcut icon" href="<?=image_url('favicon.ico')?>"/>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content ">
<!-- BEGIN HEADER -->
<?php $this->load->view('partials/head.tpl')?>
<!-- END HEADER -->
<div class="clearfix"></div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<?php $this->load->view('partials/left_menu.tpl')?>
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<?php $this->load->view($hero)?>
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 2015 &copy; 放轻松网络.
	</div>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?=image_url('/static/assets/global/plugins/respond.min.js')?>"></script>
<script src="<?=image_url('/static/assets/global/plugins/excanvas.min.js')?>"></script> 
<![endif]-->
<script src="<?=image_url('/static/assets/global/plugins/jquery-migrate.min.js')?>" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?=image_url('/static/assets/global/plugins/jquery-ui/jquery-ui.min.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/plugins/bootstrap/js/bootstrap.min.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/plugins/jquery.blockui.min.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/plugins/jquery.cokie.min.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/plugins/uniform/jquery.uniform.min.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js')?>" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?=image_url('/static/assets/global/plugins/select2/select2.min.js')?>"></script>
<script type="text/javascript" src="<?=image_url('/static/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js')?>"></script>
<script type="text/javascript" src="<?=image_url('/static/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js')?>"></script>
<script type="text/javascript" src="<?=image_url('/static/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js')?>"></script>
<script type="text/javascript" src="<?=image_url('/static/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')?>"></script>
<script type="text/javascript" src="<?=image_url('/static/assets/global/plugins/icheck/icheck.min.js')?>"></script>
<script type="text/javascript" src="<?=image_url('/static/assets/global/plugins/bootstrap-growl/jquery.bootstrap-growl.min.js')?>" ></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?=image_url('/static/assets/global/scripts/metronic.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/admin/layout/scripts/layout.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/scripts/datatable.js')?>" type="text/javascript" ></script>
<script src="<?=image_url('/static/js/datepicker.js')?>" type="text/javascript" ></script>
<script src="<?=image_url('/static/js/relative_select.min.js')?>" type="text/javascript" ></script>
<script src="<?=image_url('/static/js/jquery.form.js')?>" type="text/javascript" ></script>

<script type="text/javascript" src="<?=image_url('/static/assets/global/plugins/webuploader-0.1.5/js/webuploader.js')?>"></script>
<script type="text/javascript" src="<?=image_url('/static/assets/global/plugins/webuploader-0.1.5/js/demo.js')?>"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
 jQuery(document).ready(function() {
	Metronic.init(); 
	Layout.init(); 
	DatePicker.init();
 });
</script>
<?php $this->load->view('partials/message.tpl')?>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>