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
                            <?php if($product->new_price){ ?>
                                <div class="price">
                                    <span class="price-old"><?php echo number_format($product->price, 0, ' ', ' '); ?>руб.</span>
                                </div>
                            <?php } ?>
                            <h4><a href="/<?php echo $product->url; ?>"><?php echo $product->name?> </a></h4>
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
            <div class="col-xs-12 col-sm-6"><ul>
                    <li><em class="icon-truck" id="icon-truck"></em>
                        <div class="type-text">
                            <h3>Доставка</h3>
                            <p>При заказе до 16:00 доставка осуществляется на следующий день&nbsp;с 12:00 до 18:00 с понедельника по пятницу . По Беларуси доставка производится посредством почты.</p>
                        </div>
                    </li>
                    <li><em class="icon-phone" id="icon-phone"></em>
                        <div class="type-text">
                            <h3>Контакты</h3>
                            <p>мтс: +37529-525-15-15</p>
                            <p>velcom: +37529-667-62-33</p>
                            e-mail: annalotanby@yandex.ru</div>
                    </li>
                    <li><em class="icon-credit-card" id="icon-credit-card"></em>
                        <div class="type-text">
                            <h3>Оплата</h3>
                            <p>Наличные, наложенный платеж.</p>
                        </div>
                    </li>
                </ul></div>
            <div class="col-xs-12 col-sm-6"><h3>Интернет-магазин профессиональной косметики COSM.by</h3>
                <p>В Интернет-магазине представлена профессиональная косметика Кристина/Christina (Израиль), Эриксон/Ericson (Франция), Premium/Премиум (Россия) для домашнего ухода. Весь товар сертифицирован. Косметические препараты закупаются у официальных дистрибьютеров. Кристина, Эриксон - ЧУП "Косметика и медицина" (kosmed.by). Премиум - Минский филиал Московского Института Красоты (cosmetika.ru).</p>
                <p>Для правильного подбора косметических средств рекомендуем проконсультироваться у своего косметолога.</p></div>
        </div>
        <!-- /MODULE Block cmsinfo -->
    </div>
</div>



