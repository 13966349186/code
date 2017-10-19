<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>系统登录</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<script src="<?=image_url('/static/assets/global/plugins/jquery.min.js')?>" type="text/javascript"></script>
<link href="<?=image_url('/static/assets/global/css/google/googleFonts.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/global/plugins/font-awesome/css/font-awesome.min.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/global/plugins/simple-line-icons/simple-line-icons.min.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet')?>" type="text/css"/>
<link href="<?=image_url('/static/assets/global/plugins/uniform/css/uniform.default.css')?>" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?=image_url('/static/assets/admin/pages/css/login.css')?>" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="<?=image_url('/static/assets/global/css/components.css')?>" id="style_components" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/global/css/plugins.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/admin/layout/css/layout.css')?>" rel="stylesheet" type="text/css"/>
<link href="<?=image_url('/static/assets/admin/layout/css/themes/darkblue.css')?>" rel="stylesheet" type="text/css" id="style_color"/>
<link href="<?=image_url('/static/assets/admin/layout/css/custom.css')?>" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="<?=image_url('favicon.ico')?>"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGO -->
<div class="logo"><img src="<?=image_url('/static/assets/admin/layout/img/logo-big.png')?>" alt=""/></div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
	<!-- BEGIN LOGIN FORM -->
	<?=form_open('', Array('class'=>'login-form'))?>
		<h3 class="form-title">登录</h3>

	<?=validation_errors( '<div class="alert alert-danger">
	<button class="close" data-close="alert"></button>
	<span>', '</span>dd</div>')?>
		<div class="form-group<?=form_error('account')?' has-error':''?>">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9">账号</label>
			<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="账号" id="account" name="account"  value="<?=set_value('account', '') ?>"/>
		</div>
		<div class="form-group<?=form_error('password')?' has-error':''?>">
			<label class="control-label visible-ie8 visible-ie9">密码</label>
			<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="密码" id="password" name="password"/>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-success" id="btn-login" data-loading-text="登 录">登 录</button>
			<label class="rememberme check">
			<input type="checkbox" id="remember" name="remember" value="1" <?= set_checkbox('remember', '1') ?>/>记住用户名 </label>
		</div>
	</form>
	<!-- END LOGIN FORM -->
</div>
<!-- END LOGIN -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?=image_url('/static/assets/global/plugins/respond.min.js')?>"></script>
<script src="<?=image_url('/static/assets/global/plugins/excanvas.min.js')?>"></script> 
<![endif]-->
<script src="<?=image_url('/static/assets/global/plugins/jquery-migrate.min.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/plugins/bootstrap/js/bootstrap.min.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/plugins/jquery.blockui.min.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/plugins/jquery.cokie.min.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/global/plugins/uniform/jquery.uniform.min.js')?>" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?=image_url('/static/assets/global/plugins/jquery-validation/js/jquery.validate.min.js')?>" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?=image_url('/static/assets/global/scripts/metronic.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/admin/layout/scripts/layout.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/admin/layout/scripts/demo.js')?>" type="text/javascript"></script>
<script src="<?=image_url('/static/assets/admin/pages/scripts/login.js')?>" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {
	
	function get_cookie(name) {
		var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
		if(arr != null){
			return unescape(arr[2]);
		}
		return null; 
	}
	var isPostBack = <?= validation_errors() ? 'true' : 'false' ?>;
	if(!isPostBack){
		var acct = get_cookie('account');
		if(acct != null && acct != ''){
			$("#account").val(acct);
			$("#remember").attr('checked', 'true');
		}
	}
	
	$('#btn-login').click(function () {
	    var btn = $(this);
	    btn.button('loading')
	});

Metronic.init(); // init metronic core components
Layout.init(); // init current layout
});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>