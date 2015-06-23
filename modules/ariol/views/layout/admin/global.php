<!DOCTYPE html>
<html>
    <head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo Navigation::instance()->title()?></title>
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link href="/ariol/assets/css/bootstrap.min.css" rel="stylesheet">
	    <link href="/ariol/assets/css/style.css" rel="stylesheet">
	    <link href="/ariol/assets/css/retina.min.css" rel="stylesheet">
	    <link href="/ariol/assets/css/print.css" rel="stylesheet" type="text/css" media="print"/>
	    <link href="/ariol/assets/css/ariol.css" rel="stylesheet" type="text/css" />
	    <link href="/ariol/assets/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
	    <link type="text/css" href="/ariol/assets/fancybox/jquery.fancybox.css" rel="stylesheet" />
	    <link type="text/css" href="/ariol/assets/css/blueimp-gallery.min.css" rel="stylesheet" />
	    <link type="text/css" href="/ariol/assets/jupload/css/jquery.fileupload.css" rel="stylesheet" />
	    <!--[if lt IE 9]>
	    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	    <script src="/ariol/assets/js/respond.min.js"></script>
	    <![endif]-->
	    <link rel="shortcut icon" href="/ariol/assets/ico/favicon.png">
	    <script src="/ariol/assets/js/jquery-1.11.1.min.js"></script>
        <script src="/ariol/assets/js/jquery-migrate-1.2.1.min.js"></script>
        <script type="text/javascript" src="/ariol/assets/js/jquery.autocomplete.js"></script>
        <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
        <script src="/ariol/assets/js/jquery.ui.touch-punch.min.js"></script>
	    <script src="/ariol/assets/js/bootstrap.min.js"></script>
	    <script src="/ariol/assets/js/jquery.sparkline.min.js"></script>
		<script src="/ariol/assets/js/jquery.chosen.min.js"></script>
	    <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/ariol/assets/js/excanvas.min.js"></script><![endif]-->
	    <script src="/ariol/assets/js/jquery.autosize.min.js"></script>
	    <script src="/ariol/assets/js/jquery.placeholder.min.js"></script>
	    <script src="/ariol/assets/js/moment.min.js"></script>
	    <script src="/ariol/assets/js/daterangepicker.min.js"></script>
	    <script src="/ariol/assets/js/ru.js"></script>
	    <script src="/ariol/assets/js/jquery.easy-pie-chart.min.js"></script>
	    <script src="/ariol/assets/js/jquery.dataTables.min.js"></script>
	    <script type="text/javascript" src="/ariol/assets/js/extasy.field.array.js"></script>
	    <script src="/ariol/assets/js/dataTables.bootstrap.js"></script>
	    <script src="/ariol/assets/js/custom.min.js"></script>
	    <script src="/ariol/assets/js/jquery.icheck.min.js"></script>
	    <script src="/ariol/assets/js/core.min.js"></script>
	    <script src="/ariol/assets/js/sortable.js"></script>
	    <script src="/ariol/assets/js/jquery.validate.min.js"></script>
	    <script src="/ariol/assets/js/additional-methods.min.js"></script>
	    <script src="/ariol/assets/js/bootstrap-switch.min.js"></script>
	    <script type="text/javascript" src="/ariol/assets/fancybox/jquery.fancybox.js"></script>
	    <script type="text/javascript" src="/ariol/assets/js/tmpl.min.js"></script>
	    <script type="text/javascript" src="/ariol/assets/js/load-image.min.js"></script>

	    <script type="text/javascript" src="/ariol/assets/js/canvas-to-blob.min.js"></script>
	    <script type="text/javascript" src="/ariol/assets/js/jquery.blueimp-gallery.min.js"></script>
	    <script type="text/javascript" src="/ariol/assets/jupload/js/vendor/jquery.ui.widget.js"></script>
	    <script type="text/javascript" src="/ariol/assets/jupload/js/jquery.iframe-transport.js"></script>
	    <script type="text/javascript" src="/ariol/assets/jupload/js/jquery.fileupload.js"></script>
	    <script type="text/javascript" src="/ariol/assets/jupload/js/jquery.fileupload-process.js"></script>
	    <script type="text/javascript" src="/ariol/assets/jupload/js/jquery.fileupload-image.js"></script>
	    <script type="text/javascript" src="/ariol/assets/jupload/js/jquery.fileupload-audio.js"></script>
	    <script type="text/javascript" src="/ariol/assets/jupload/js/jquery.fileupload-video.js"></script>
	    <script type="text/javascript" src="/ariol/assets/jupload/js/jquery.fileupload-validate.js"></script>
	    <script type="text/javascript" src="/ariol/assets/jupload/js/jquery.fileupload-ui.js"></script>
	    <script type="text/javascript" src="/ariol/assets/noty/packaged/jquery.noty.packaged.min.js"></script>
	    <script type="text/javascript" src="/ariol/assets/noty/themes/default.js"></script>
		 <script type="text/javascript" src="/ariol/assets/js/pages/form-elements.js"></script>
		 <script type="text/javascript" src="/application/views/admin/order/order_script.js"></script>
		 <script type="text/javascript" src="/application/views/admin/order/jquery.printPage.js"></script>

	    <?php if (isset($unSortableFields)) { ?>
	    <?php foreach ($unSortableFields as $unSortableField) { ?>
		    <script>initSortable("<?php echo $unSortableField; ?>");</script>
	    <?php } ?>
	    <?php } ?>
	    <script src="/ariol/assets/js/ariol.js"></script>
	    <script id="template-upload" type="text/x-tmpl">
			{% for (var i=0, file; file=o.files[i]; i++) { %}
				<tr class="template-upload fade">
					<td style="width: 100px;">
						<span class="preview"></span>
					</td>
					<td>
						<p class="name">{%=file.name%}</p>
						<strong class="error text-danger"></strong>
					</td>
					<td>
						<p class="size">Обработка</p>
						<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
					</td>
					<td>
						{% if (!i && !o.options.autoUpload) { %}
							<button class="btn btn-primary start">
								<i class="glyphicon glyphicon-upload"></i>
								<span>Начать загрузку</span>
							</button>
						{% } %}
						{% if (!i) { %}
							<button class="btn btn-warning cancel">
								<i class="glyphicon glyphicon-ban-circle"></i>
								<span>Отменить загрузку</span>
							</button>
						{% } %}
					</td>
				</tr>
			{% } %}
		</script>
	    <script id="template-download" type="text/x-tmpl">
			{% for (var i=0, file; file=o.files[i]; i++) { %}
				<tr class="template-download fade">
					<td style="width: 100px;">
						<span class="preview">
							{% if (file.thumbnailUrl) { %}
								<a target="_blank" class="fancybox" href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
							{% } %}
						</span>
					</td>
					<td>
						<p class="name">
							{% if (file.url) { %}
								<a target="_blank" href="{%=file.url%}" title="{%=file.name%}">{%=file.name%}</a>
							{% } else { %}
								<span>{%=file.name%}</span>
							{% } %}
						</p>
						{% if (file.error) { %}
							<div><span class="label label-danger">Ошибка</span> {%=file.error%}</div>
						{% } %}
					</td>
					<td>
						<span class="size">{%=o.formatFileSize(file.size)%}</span>
					</td>
					<td>
						{% if (file.deleteUrl) { %}
							<button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
								<i class="glyphicon glyphicon-trash"></i>
								<span>Удалить</span>
							</button>
						{% } else { %}
							<button class="btn btn-warning cancel">
								<i class="glyphicon glyphicon-ban-circle"></i>
								<span>Отменить</span>
							</button>
						{% } %}
					</td>
				</tr>
		{% } %}
		</script>
    </head>
    <body>
		<header class="navbar">
			<div class="container">
				<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".sidebar-nav.nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a id="main-menu-toggle" class="hidden-xs open"><i class="fa fa-bars"></i></a>
				<a class="navbar-brand col-md-2 col-sm-1 col-xs-2" href="<?='/'.Extasy_Url::url_to_route(Kohana::$config->load('extasy.redirect_from_index'))?>"><span>Ariol</span></a>
				<div class="nav-no-collapse header-nav">
					<?php if (Auth::instance()->logged_in('admin')) { ?>
						<ul class="nav navbar-nav pull-right">
							<li style="padding: 10px;">
								Товаров на сайте: <?php echo ORM::factory('Product')->where('active', '=', 1)->count_all(); ?>
							</li>
							<li class="dropdown">
								<a class="btn account dropdown-toggle" data-toggle="dropdown">
									<div class="user">
										<i class="fa fa-wrench"></i> СИСТЕМНОЕ МЕНЮ
									</div>
								</a>
								<?php echo Menu::instance('topmenu', 'topmenu'); ?>
							</li>
						</ul>
					<?php } ?>
				</div>
			</div>
		</header>
		<div class="container">
			<div class="row">
				<?php $gridCols = 12; ?>
				<?php if (Auth::instance()->logged_in('admin')) { ?>
					<?php $gridCols = 10; ?>
					<div id="sidebar-left" class="col-lg-2 col-sm-1 ">
						<?php echo Menu::instance('menu', 'menu'); ?>
					</div>
				<?php } ?>
				<div id="id_content" <?php if (!Auth::instance()->logged_in('admin')) { ?>style="width:100%"<?php } ?> class="col-lg-<?php echo $gridCols; ?> col-sm-11 ">
					<?php if(!preg_match('%print_page%', $_SERVER['REQUEST_URI'])) { ?><?php echo Navigation::instance()->crumbs()?><?php } ?>
					<div class="box">
						<div class="box-header" data-original-title="">
							<h2><i class="fa fa-table"></i><?=Navigation::instance()->title()?></h2>
						</div>
						<div class="box-content">
							<?php echo $content; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<footer>
			<p>
				<span style="text-align:left;float:left">&copy; <?php echo date('Y'); ?> | <?php echo Application::NAME?> | v<?php echo Application::VERSION?> | Ariol</span>
				<span class="hidden-phone" style="text-align:right;float:right">Powered by: Ariol</span>
			</p>
		</footer>
    </body>
</html>
