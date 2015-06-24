<div id="main-container">
        <!-- Breadcrumb Starts -->
        <ol class="breadcrumb">
            <li><a href="/">Главная</a></li>
            <li>
               <a href="/<?php echo $category->url; ?>"><?php echo $category->name; ?></a>
            </li>
            <li>
               <?php echo $name?>
            </li>
        </ol>
        <!-- Breadcrumb Ends -->
        <!-- Product Info Starts -->
        <div class="row product-info full">
            <!-- Left Starts -->
            <div class="col-sm-4 images-block">
                <a href="<?php echo $main_image;?>">
                    <img src="<?php echo $main_image;?>" alt="Image" class="img-responsive thumbnail" />
                </a>
                <ul class="list-unstyled list-inline">
                    <?php if($more_images) { ?>
                    <?php $imgages = json_decode($more_images, true);?>
                 <?php foreach($imgages as $img){ ; ?>
                    <li>
                        <a href="<?php echo $img?>">
                            <img src="<?php echo $img; ?>" alt="Image" class="img-responsive thumbnail small_img" />
                        </a>
                    </li>
                    <?php } ?>
                    <?php } ?>
                </ul>
            </div>
            <!-- Left Ends -->
            <!-- Right Starts -->
            <div class="col-sm-8 product-details">
                <div class="panel-smart">
                    <!-- Product Name Starts -->
                    <h2><?php echo $name?></h2>
                    <!-- Product Name Ends -->
                    <hr />
                    <!-- Manufacturer Starts -->
                    <ul class="list-unstyled manufacturer">
                        <li>
                            <span>Бренд:</span> <?php echo $brand->name; ?>
                        </li>
                        <li><span>Артикул:</span><span id="article"> <?php echo $article?></span></li>
                        <?php if($volume){?>
                        <li><span>Объем:</span><span id="volume"> <?php echo $volume?></span></li>
                        <?php } ?>
                        <li>
                            <span>Наличие:</span> <strong class="label <?php if ($active) { ?> label-success"> В наличии<?php } else{ ?> label-danger"> Отсутствует<?php } ?></strong>
                        </li>
                    </ul>
                    <!-- Manufacturer Ends -->
                    <hr />
                    <!-- Price Starts -->
                    <?php $price_prod = ORM::factory('Product')->getPriceValue($id);?>
                    <div class="price">
                        <span class="price-head">Цена :</span>
                        <span class="price-new">   <?php echo number_format($price_prod, 0, '', ' ');?>  руб.</span>
                        <?php if($new_price){?><span class="price-old"><?php echo number_format($price, 0, '', ' ');?> руб.</span><?php } ?>
                      </div>
                    <!-- Price Ends -->
                    <hr />
                    <!-- Available Options Starts -->
                    <div class="options">
                        <h3>Дополнительные опции</h3>
                        <?php if($color){
                        $color = explode(",", $colors);
                        $i = 0;?>
                            <label for="select" class="control-label text-uppercase">Выберите цвет:</label>
                        <div class="col-xs-12">
                            <?php foreach($color as $item){?>
                                <a href="#"  class="choose_color"  data-color="<?php echo $item?>" data-id="<?php echo $id?>">
                                    <div data-color="<?php echo $item?>";  class="color col-xs-4" style="background: <? echo $item;?>; width: 30px; height: 30px; margin:5px;"></div>
                                </a>
                                <?php $i++; } ?>
                        </div>
                            <?php } ?>
                        <?php  if($parent_product == 0 AND $volume) { ?>
                        <div class="form-group">
                            <label for="select" class="control-label text-uppercase">Объем: <?php if (!$volume_prod) { ?><?php echo $volume?><?php } ?></label>
                            <?php  if($volume_prod) { ?>
                            <select name="select" id="select" class="form-control">
                                <option value="<?php echo $volume; ?>" data-id="<?php echo $id?>" data-price="<?php echo number_format($price_prod, 0, '', ' '); ?>" <?php if($new_price) { ?> data-old-price="<?php echo number_format($price, 0, '', ' ')?>" <?php } ?> data-article="<?php echo $article?>" selected><?php echo $volume?></option>
                                <?php  foreach ($volume_prod as $volume) {
                                 $price = ORM::factory('Product')->getPriceValue($volume->id);?>
                                <option value="<?php echo $volume->volume; ?>" data-id="<?php echo $volume->id ?>" data-price="<?php echo number_format($price, 0, ' ', ' ');?>" <?php if($volume->new_price) { ?> data-old-price="<?php echo number_format($volume->price, 0, '', ' ')?>" <?php } ?>  data-article="<?php echo $volume->article?>"><?php echo $volume->volume?></option>
                                <?php } ?>
                            </select>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <?php if($parent_product != 0) { ?>
                        <?php $volume_prod_child = ORM::factory('Product')->fetchProdChildVolume($parent_product)->as_array(); ?>
                        <div class="form-group">
                            <label for="select" class="control-label text-uppercase">Объем: </label>
                            <?php if($volume_prod_child) { ?>
                            <select name="select" id="select" class="form-control">
                                <option value="<?php echo $volume; ?>" data-id="<?php echo $id?>" data-price="<?php echo number_format($price_prod, 0, '', ' '); ?>" <?php if($new_price) { ?> data-old-price="<?php echo number_format($price, 0, '', ' ')?>" <?php } ?> data-article="<?php echo $article?>" selected><?php echo $volume?></option>
                                <?php  foreach ($volume_prod_child as $volume) {;?>
                                    <?php $price = ORM::factory('Product')->getPriceValue($volume->id);?>
                                <option value="<?php echo $volume->volume; ?>" data-id="<?php echo $volume->id ?>" data-price="<?php echo number_format($price, 0, ' ', ' ');?>" <?php if($volume->new_price) { ?> data-old-price="<?php echo $volume->price?>" <?php } ?> data-article="<?php echo $volume->article?>"><?php echo $volume->volume?></option>
                                <?php  echo $volume->volume; } ?>
                            </select>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label class="control-label text-uppercase" for="input-quantity">Количество:</label>
                            <input type="text" name="quantity" value="1" size="2" id="input-quantity" class="form-control" />
                        </div>
                        <div class="cart-button button-group">
                            <button type="button" title="Добавить в избранное" class="btn btn-wishlist add_wish" data-key="prod" data-id="<?php echo $id?>" data-price="<?php echo $price ?>" >
                                <i class="fa fa-heart"></i>
                            </button>
                            <button type="button" title="Добавить в корзину" class="btn btn-cart add_cart change_add" data-id="<?php echo $id?>" data-price="<?php echo $price ?>" data-color="">
                                Добавить в корзину
                                <i class="fa fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Available Options Ends -->
                </div>
            </div>
        <div class="tabs-panel panel-smart col-xs-12 ">
            <!-- Nav Tabs Starts -->
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab-description" data-toggle="tab">Описание</a>
                </li>
                <li><a href="#tab-review" data-toggle="tab">Отзывы</a></li>
            </ul>
            <!-- Nav Tabs Ends -->
            <!-- Tab Content Starts -->
            <div class="tab-content clearfix">
                <!-- Description Starts -->
                <div class="tab-pane active" id="tab-description">
                    <?php echo $content?>
                </div>
                <div class="tab-pane revews" id="tab-review">
                    <?php if($reviews_count == 0) { ?>
                        <h4>Отзывов о товаре нет</h4>
                        <h4>Ваш отзыв будет первым</h4>
                    <?php }?>
                    <form class="form-horizontal">
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-name">Имя</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" value="" id="input-name" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-review">Отзыв</label>
                            <div class="col-sm-10">
                                <textarea name="content" rows="5" id="input-review" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label ratings">Рейтинг</label>
                            <div class="col-sm-10">
                                Плохо&nbsp;
                                <input type="radio" name="rating" value="1" />
                                &nbsp;
                                <input type="radio" name="rating" value="2" />
                                &nbsp;
                                <input type="radio" name="rating" value="3" />
                                &nbsp;
                                <input type="radio" name="rating" value="4" />
                                &nbsp;
                                <input type="radio" name="rating" value="5" />
                                &nbsp;Хорошо
                            </div>
                        </div>
                        <div class="buttons">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" id="button-review" class="btn btn-main" data-type="add_review" data-id="<?php echo $id?>">
                                    Отправить
                                </button>
                            </div>
                        </div>
                    </form>
                    <h2>Отзывы</h2>
                    <div>
                        <?php if($reviews){
                            foreach ($reviews as $review){  ?>
                                <div>
                                    <?php echo $review->name;?>
                                </div>
                                <div>
                                    <?php echo $review->content;?>
                                </div>
                                <?php
                                switch ($review->rating) {
                                    case 1:
                                        echo "Оценка 1";
                                        break;
                                    case 2:
                                        echo "Оценка 2";
                                        break;
                                    case 3:
                                        echo "Оценка 3";
                                        break;
                                    case 4:
                                        echo "Оценка 4";
                                        break;
                                    case 5:
                                        echo "Оценка 5";
                                        break;
                                }?>
                            <? } ?>
                        <?php } ?>
                    </div>
                </div>
                <!-- Review Ends -->
            </div>
            <!-- Tab Content Ends -->
        </div>
        <!-- Tabs Ends -->
        <!-- Related Products Starts -->
            <section class="products-list">
            <div class="product-info-box">
            <h4 class="heading col-xs-12">Смотрите еще</h4>
            <div class="row">
                <?php $price = ORM::factory('Product')->getPriceValue();?>
                <?php foreach($related as $related_prod){
                    $price = ORM::factory('Product')->getPriceValue($related_prod->id);
                    ?>
                <div class="col-md-3 col-sm-6">
                    <div class="product-col">
                        <div class="image">
                            <a href="/<?php echo $category->url; ?>/<?php echo $related_prod->url; ?>">
                                <img src="<?php echo Lib_Image::resize_bg($related_prod->main_image, '$related', $related_prod->id, 250, 250); ?>"  alt="product" class="img-responsive" />
                            </a>
                        </div>
                        <div class="caption">
                            <br>
                            <div class="price">
                                <span class="price-new"><?php echo number_format($price, 0, ' ', ' ') ?>руб.</span>
                            </div>
                            <?php if($related_prod->new_price){ ?>
                                <div class="price">
                                    <span class="price-old"><?php echo number_format($related_prod->price, 0, ' ', ' '); ?>руб.</span>
                                </div>
                            <?php } ?>
                            <h4><a href="/<?php echo $category->url; ?>/<?php echo $related_prod->url; ?>"><?php echo $related_prod->name?></a></h4>
                            <div class="cart-button button-group">
                                <button type="button" class="btn btn-cart add_cart" data-id="<?php echo $related_prod->id; ?>" data-price="<?php echo $price ?>">
                                    <i class="fa fa-shopping-cart"></i><br>
                                    Добавить в корзину
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
            </section>
    </div>
    </div>
