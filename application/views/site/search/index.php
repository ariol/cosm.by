
<div id="main-container">
            <!-- Breadcrumb Starts -->
            <ol class="breadcrumb">
                <li><a href="/">Главная</a></li>
                <li>Поиск - «<?php echo Arr::get($_GET, 'q'); ?>»</li>
            </ol>
            <div class="product-filter">
                <div class="row">
                    <div class="col-md-8">
                        <h2 class="main-heading2">
                            Результаты поиска «<?php echo Arr::get($_GET, 'q'); ?>» <small>Найдено результатов - <?php echo $countall; ?></small>
                        </h2>
                    </div>
                    <div class="sort_search">
                        <div class="col-md-2 text-right">
                            <label class="control-label">Сортировать по:</label>
                        </div>
                        <div class="col-md-2 text-right">
                            <div class="sort_list">
                                <?php echo Sortable::get_button('price', 'цене', null, true); ?>
                                <?php echo Sortable::get_button('name', 'названию', null, true); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product Filter Ends -->
            <!-- Product Grid Display Starts -->
            <section class="products-list">
            <div class="row">
				<?php $i = 0; ?>
                <?php if($items) { ?>
                <?php foreach ($items as $index => $prod) { ?>
					<?php if ($i && !($i % 3)) { ?>
						<div class="clearfix"></div>
					<?php } ?>
					<?php $i++; ?>
                    <?php  $category = ORM::factory('Category')->where('id', '=', $prod->category_id)->find(); ?>
                    <?php   $price = ORM::factory('Product')->getPriceValue($prod->id); ?>
                    <input type="hidden" name="quantity" value="1"  />
                    <div class="col-md-4 col-sm-6">
                        <div class="product-col">
                            <div class="image">
                                <a href="/<?php echo $category->url; ?>/<?php echo $prod->url; ?>">
                                    <img src="<?php echo Lib_Image::resize_bg($prod->main_image, 'product', $prod->id, 250, 250); ?>"alt=" <?=$prod->name?> - <?=$prod->article?>" class="img-responsive" />
                                </a>
                            </div>
                            <div class="caption">
                                <div class="price">
                                    <span class="price-new" ><?php echo number_format($price, 0, ' ', ' '); ?>.руб</span>
                                </div>
                                <?php if($prod->new_price){ ?>
                                    <div class="price">
                                        <span class="price-old"><?php echo number_format($prod->price, 0, ' ', ' '); ?>руб.</span>
                                    </div>
                                <?php } ?>
                                <h4><a href="/<?php echo $category->url; ?>/<?php echo $prod->url; ?>"><?php echo $prod->name; ?> </a></h4>
                                <div class="cart-button button-group">
                                    <button type="button" class="btn btn-cart add_cart" data-id="<?php echo $prod->id; ?>" data-price="<?php echo $prod->price ?>">
                                        <i class="fa fa-shopping-cart"></i><br>
                                        Добавить в корзину
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
                </section>
            <div class="row">
                <div class="paginator">
                        <?php echo $pagination; ?>
                </div>
            </div>
            <?} else { ?>
            <div class="alert alert-danger search">По вашему запросу ничего не найдено</div>
            <?php } ?>
        </div>