<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Вход</title>
		<meta name="description" content="Ariol CMS v.2">
		<meta name="author" content="Ariol">
		<meta name="keyword" content="Ariol, CMS, v2">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="/ariol/assets/css/bootstrap.min.css" rel="stylesheet">
		<link href="/ariol/assets/css/admin.min.css" rel="stylesheet">
		<link href="/ariol/assets/css/retina.min.css" rel="stylesheet">
		<link href="/ariol/assets/css/print.css" rel="stylesheet" type="text/css" media="print"/>
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<script src="/ariol/assets/js/respond.min.js"></script>
		<![endif]-->
		<link rel="shortcut icon" href="/ariol/assets/ico/favicon.png">
	</head>
	<body>
		<header class="navbar">
			<div class="container">
				<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".sidebar-nav.nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand col-md-2 col-sm-1 col-xs-2" style="height:100%!important;" href="/ariol-admin"><span>Ariol</span></a>
				<div class="nav-no-collapse header-nav">
				</div>
			</div>
		</header>
		<div class="container">
			<div class="row">
				<div id="content" class="col-sm-12 full" style="padding-top: 0!important; min-height: 0px!important;">
					<div class="row">
						<div class="login-box">
							<div class="header">
								ВХОД В АДМИНИСТРАТОРСКУЮ ЗОНУ
							</div>
							<form class="form-horizontal login" id="login-form" method="post">
								<fieldset class="col-sm-12">
									<div class="form-group">
										<div class="controls row">
											<div class="input-group col-sm-12">
												<input type="text" name="email" class="form-control" id="email" placeholder="E-mail"/>
												<span class="input-group-addon"><i class="fa fa-user"></i></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="controls row">
											<div class="input-group col-sm-12">
												<input type="password" class="form-control" id="password" name="password" placeholder="Пароль"/>
												<span class="input-group-addon"><i class="fa fa-key"></i></span>
											</div>
										</div>
									</div>
									<div class="confirm">
										<input type="checkbox" id="remember" name="remember"/>
										<label for="remember">Запомнить меня</label>
									</div>
									<div class="row">
										<input type="hidden" value="<?php echo $return; ?>"/>
										<button type="submit" class="btn btn-lg btn-primary col-xs-12">Войти</button>
									</div>
								</fieldset>
							</form>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="/ariol/assets/js/jquery-1.11.1.min.js"></script>
		<script src="/ariol/assets/js/jquery-migrate-1.2.1.min.js"></script>
		<script type="text/javascript" src="/ariol/assets/noty/packaged/jquery.noty.packaged.min.js"></script>
	    <script type="text/javascript" src="/ariol/assets/noty/themes/default.js"></script>
		<script src="/ariol/assets/js/bootstrap.min.js"></script>
		<script src="/ariol/assets/js/jquery.icheck.min.js"></script>
		<script src="/ariol/assets/js/custom.min.js"></script>
		<script src="/ariol/assets/js/core.min.js"></script>
		<script src="/ariol/assets/js/jquery.validate.min.js"></script>
		<script src="/ariol/assets/js/additional-methods.min.js"></script>
		<script src="/ariol/assets/js/pages/login.js"></script>
		<div class="clearfix"></div>
		<footer>
			<p>
				<span style="text-align:left;float:left">&copy; <?php echo date('Y'); ?> | ariol-cms | v2.0 | Ariol</span>
				<span class="hidden-phone" style="text-align:right;float:right">Powered by: Ariol</span>
			</p>
		</footer>
	</body>
</html>