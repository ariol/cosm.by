<!DOCTYPE html>
<html lang="ru" style="height:100%;">
	<head>
		<meta charset="utf-8">
		<title><?=$code; ?> ошибка - Пристанище заблудившихся путников</title>
		<meta name="description" content="Заблудились? Попробуйте поискать еще.">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="/ariol/assets/ico/favicon.ico'; ?>">
		<?php echo HTML::style('/ariol/assets/css/bootstrap.min.css'); ?>
		<?php echo HTML::style('/ariol/assets/css/style.css?v3'); ?>
		<?php echo HTML::style('/ariol/assets/css/retina.min.css'); ?>
		<?php echo HTML::style('/ariol/assets/css/print.css', array('media' => 'print')); ?>
		<?php echo HTML::style('/ariol/assets/css/ariol.css'); ?>
		<?php echo HTML::script('https://html5shim.googlecode.com/svn/trunk/html5.js'); ?>
		<?php echo HTML::script('/ariol/assets/js/jquery-1.10.2.min.js'); ?>
		<?php echo HTML::script('/ariol/assets/js/jquery-migrate-1.2.1.min.js'); ?>
		<?php echo HTML::script('/ariol/assets/js/bootstrap.min.js'); ?>
		<?php echo HTML::script('/ariol/assets/js/core.min.js'); ?>
	</head>
	<body class="error">
		<div class="container">
			<div class="row">
				<div id="id_content" class="col-sm-12 full error">
					<div class="row box-error">
						<div class="col-lg-4 col-lg-offset-7 col-md-4 col-md-offset-7">
							<h1><?php echo $code; ?></h1>
							<?php if ($code >= 500) { ?>
								<h2>Что-то сломалось.</h2>
								<p>Ожидайте, в скором времени починимся.</p>
							<?php } else { ?>
								<h2>Страница не найдена</h2>
								<p>Волшебная строка поможет вам</p>
								<p>или переходите на <a href="/" style="color:#fff;text-decoration:underline!important">сайт</a> 
								<form action="/search">
									<div class="input-prepend input-group">
										<span class="input-group-addon clear"><i class="fa fa-search"></i></span>
										<input name="q" value="" id="prependedInput" class="form-control" size="16" type="text" placeholder="Волшебная строка">
										<span class="input-group-btn">
											<button class="btn btn-info" type="submit">Найти</button>
										</span>
									</div>
								</form>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>