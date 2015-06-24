<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo $s_title; ?></title>
    <!-- Bootstrap Core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Web Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,700,400&subset=cyrillic,latin' rel='stylesheet' type='text/css'>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400" rel="stylesheet" type="text/css">
    <link href="/css/magnific-popup.css" rel="stylesheet">
    <!-- CSS Files -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link href="/css/jquery.scrollbar.css" rel="stylesheet">
    <link href="/css/style.css?v1" rel="stylesheet">
    <link href="/css/responsive.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="/js/ie8-responsive-file-warning.js"></script>
    <![endif]-->
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="shortcut icon" href="favicon.ico">

    <script src="/js/jquery-1.11.1.min.js"></script>
    <script src="/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="/js/jquery.scrollbar.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/bootstrap-hover-dropdown.min.js"></script>
    <script src="/js/jquery.magnific-popup.min.js"></script>
    <script src="http://vk.com/js/api/openapi.js" type="text/javascript"></script>
    <script src="/js/custom.js"></script>
    <script src="/js/script.js?v1"></script>



</head>

<body>
<!-- Container Starts -->
<div id="wrapper" class="container">
    <!-- Header Section Starts -->
    <header id="header-area" <?php if($_SERVER['REQUEST_URI'] == "/"){?>class="home"<?php }?>>
        <!-- Header Top Starts -->
        <div class="header-top">
            <div class="row">
                <!-- Currency & Languages Starts -->
                <div class="col-sm-6 col-xs-12">
                    <div class="header-links">
                        <ul class="nav navbar-nav pull-left">
                            <li><a href="/page/dostavka-oplata"><span>Оплата и доставка</span></a></li>
                            <li><a href="/page/usloviya-obslujivaniya"><span>Условия обслуживания</span></a></li>
                            <li><a href="/page/publichnaya-oferta">Публичная оферта</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="header-links">
                        <ul class="nav navbar-nav pull-right">
                            <li>
                                <a href="/page/contacts">
										<span>
											Контакты
										</span>
                                </a>
                            </li>
                            <li>
                                <a href="/like">
										<span>
											Избранное (<span id="like-total"><?php echo $summlikes;?></span>)
										</span>
                                </a>
                            </li>
                            <li>
                                <a href="/cart">
										<span>
											Корзина
										</span>
                                    <i class="fa fa-shopping-cart"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Header Links Ends -->
            </div>
        </div>
        <!-- Header Top Ends -->
        <!-- Main Header Starts -->
        <div class="main-header">
            <div class="row">
                <!-- Logo Starts -->
                <div class="col-sm-5 col-md-6">
                    <div id="logo">
                        <a href="/"><img src="/images/logo.png" title="Chocolate Shoppe" alt="Chocolate Shoppe" class="img-responsive" /></a>
                    </div>
                </div>
                <!-- Logo Starts -->
                <!-- Shopping Cart Starts -->
                <div class="col-sm-3">
                    <div id="cart" class="btn-group btn-block animate">
                        <button name="cart" id="open_cart" type="button" data-toggle="dropdown" class="btn btn-block btn-lg dropdown-toggle">
                            <i class="fa fa-shopping-cart"></i>
                            <span id="cart-total" data-count="<?php echo $result_quantity?>"><?php echo $result_quantity?></span>
                        </button>
                        <ul class="dropdown-menu pull-right result" id="result_busket"></ul>
                    </div>
                </div>
     <!-- Shopping Cart Ends -->
                <!-- Search Starts -->
                <div class="col-sm-4 col-md-3">
                    <div id="search">
                        <div class="input-group">
                            <form class="search" action="/search">
                                <input type="text" name="q" autocomplete="off" value="<?php echo Arr::get($_GET, 'q'); ?>" class="form-control input-lg" placeholder="Поиск по каталогу">
                                <a rel="nofollow" href="javascript:void(0)" onclick="$('form').submit();" title="Найдись!" class="action-search"></a>
                                <div id="result"></div>
                            </form>
<!--                            <div id="result"></div>-->
                            <span class="input-group-btn search action-search">
								<button class="btn btn-lg search_button" type="button" title="Поиск" value="javascript:void(0)" onclick="$('form').submit();"  class="action-search">
                                    <i class="fa fa-search"></i>
                                </button>
							  </span>
                        </div>
                    </div>
                </div>
                <!-- Search Ends -->
            </div>
        </div>
        <!-- Main Header Ends -->
        <!-- Main Menu Starts -->
        <?php echo View::factory('site/menu/catalog');?>
        <!-- Main Menu Ends -->
        <div id="growl"></div>
    </header>
