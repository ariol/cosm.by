<!-- Slider Starts -->
<div class="slider">
    <div id="main-carousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators Starts -->
        <ol class="carousel-indicators">
            <?php foreach ($slider as $slide) { ?>
                <li data-target="#main-carousel" data-slide-to="<?php echo $slide->position -= 1 ?>" <?php if($slide->position == 0){?>class="active"><?php } ?></li>
            <?php } ?>
        </ol>
        <div class="carousel-inner">
            <?php foreach ($slider as $slide) { ?>
            <div class="item<?php if($slide->position == 1){?> active<?php } ?>">
                <a href="<?php echo $slide->href; ?>">
                    <img src="<?php echo $slide->image; ?>" alt="Slider" class="img-responsive" />
                </a>
            </div>
            <?php } ?>
        </div>
        <!-- Wrapper For Slides Ends -->
		
		 <!-- Controls -->
		  <a class="left carousel-control" href="#main-carousel" data-slide="prev">
			<span class="glyphicon glyphicon-chevron-left"></span>
		  </a>
		  <a class="right carousel-control" href="#main-carousel" data-slide="next">
			<span class="glyphicon glyphicon-chevron-right"></span>
		  </a>
    </div>
</div>
<!-- Slider Ends -->
<!-- Main Container Starts -->
<div id="main-container">
    <!-- 2 Column Banners Starts -->
    <div class="col2-banners">
        <ul class="row list-unstyled">
            <?php foreach ($promo as $index => $banner) { ?>
                <?php if($banner->position == 1 or $banner->position == 2) { ?>
                <li class="col-sm-6">
                    <a href="<?php echo $banner->href; ?>">
                        <img src="<?php echo $banner->image; ?>" alt="banners" class="img-responsive" />
                    </a>
                </li>
                    <?php } ?>
            <?php } ?>
        </ul>
    </div>
    <!-- 2 Column Banners Ends -->
    <!-- Latest Products Starts -->
    <section class="products-list">
        <!-- Heading Starts -->
        <h2 class="product-head">Лидеры продаж</h2>
        <!-- Heading Ends -->
        <!-- Products Row Starts -->
        <div class="row">
            <!-- Product #1 Starts -->
            <?php foreach($prodForMain as $product) {
                $category = ORM::factory('Category')->where('id', '=', $product->category_id)->find();
                $price = ORM::factory('Product')->getPriceValue($product->id);
                ?>
                <input type="hidden" name="quantity" value="1"  />
                <div class="col-md-3 col-sm-6">
                    <div class="product-col">
                        <div class="image">
                            <a href="/<?php echo $category->url; ?>/<?php echo $product->url; ?>">
                            <img src="<?php echo Lib_Image::resize_bg($product->main_image, 'product', $product->id, 250, 250); ?>" alt="product" class="img-responsive" />
                            </a>
                        </div>
                        <div class="caption">
                            <div class="price">
                               <span class="price-new" ><?php echo number_format($price, 0, ' ', ' '); ?>.руб</span>
                            </div>
                            <?php if($product->new_price) { ?>
                                <div class="price">
                                    <span class="price-old"><?php echo number_format($product->price, 0, ' ', ' '); ?>руб.</span>
                                </div>
                            <?php } ?>
                            <h4><a href="/<?php echo $category->url; ?>/<?php echo $product->url; ?>"><?php echo $product->name?> </a></h4>
                            <div class="cart-button button-group">
                                <button class="btn btn-cart add_cart topLink" data-id="<?php echo $product->id; ?>" data-price="<?php echo $price ?>">
                                    <i class="fa fa-shopping-cart"></i><br>
                                    Добавить в корзину
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }  ?>
        </div>
    </section>
    <div class="col2-banners">
        <ul class="row list-unstyled">
            <?php foreach ($promo as $index => $banner) { ?>
                <?php if($banner->position == 3 or $banner->position == 4) {?>
                    <li class="<?php if($banner->position == 3){?>col-sm-3<?php } ?> <?if($banner->position == 4){?>col-sm-9<?php } ?>">
                        <a href="<?php echo $banner->href; ?>">
                            <img src="<?php echo $banner->image; ?>" alt="banners" class="img-responsive" />
                        </a>
                    </li>
                <?php } ?>
            <?php }?>
        </ul>
    </div>

    <div class="clearfix"><!-- MODULE Block cmsinfo -->
        <div id="cmsinfo_block">
            <div class="col-xs-12 col-sm-5"><ul>
                    <li><em class="icon-truck" id="icon-truck"></em>
                        <div class="type-text">
                            <h3>Доставка</h3>
                            <p><b>Курьерская доставка с 12:00 до 20:00 пн-пт только по г. Минску!</b></p>
                                <ul class="delivery_style_main">
                                    <li>Доставка осуществляется на следующий рабочий день после согласования заказа. Суббота и воскресенье - ВЫХОДНОЙ.</li>
                                    <li>Стоимость доставки в пределах МКАД: 30 000 рублей при заказе до 600 000 рублей. БЕСПЛАТНО - при заказе от 600 000 рублей. </li>
                                </ul>
                            <br>
                            <p><b>В другие города Беларуси доставка почтой – наложенным платежом.</b></p>
                                <ul class="delivery_style_main">
                                    <li>Стоимость доставки составляет 50 000 рублей.</li>
                                </ul>
                        </div>
                    </li>
                    <li><em class="icon-phone" id="icon-phone"></em>
                        <div class="type-text">
                            <h3>Контакты</h3>
                            <p>мтс: +37529-525-15-15</p>
                            <p>velcom: +37529-667-62-33</p>
                            e-mail: info@cosm.by</div>
                    </li>
                    <li><em class="icon-credit-card" id="icon-credit-card"></em>
                        <div class="type-text">
                            <h3>Оплата</h3>
                            <p>Наличные, наложенный платеж.</p>
                        </div>
                    </li>
                </ul></div>
            <div class="col-xs-12 col-sm-7"><h3>Интернет-магазин профессиональной косметики COSM.by</h3>
                <p>
                    В Интернет-магазине представлена профессиональная косметика, применимая для домашнего ухода. Весь товар поставляется, сертифицируется и хранится официальными дистрибьютерами в Республике Беларусь:
                </p>
                <p>
                    Кристина/Christina (Израиль), Эриксон/ Ericson Laboratoire (Франция), Генозис/Genosys (Корея), Дермахил/ Dermaheal (Корея), Иноэстэтикс/Innoaesthetics (Испания) - ЧУП "Косметика и медицина"
                </p>
                <p>
                    Анна Лотан/Anna Lotan (Израиль), Фрэш Лук/Freesh Look (Израиль), Ксаниталия/Xanitalia (Италия), Леонор Грей/ Leonor Grey (Франция), Зюда/SUDA (Германия), OPI (США) - ООО "Профессиональная косметика"
                </p>
                <p>
                    Premium/Премиум (Россия) - Минский филиал Московского Института Красоты
                </p>
                <p>
                    Для правильного подбора косметических средств рекомендуем проконсультироваться у своего косметолога.
                </p>
            </div>
        </div>
        <!-- /MODULE Block cmsinfo -->
    </div>
</div>



