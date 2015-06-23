<div id="main-container">
    <!-- Breadcrumb Starts -->
    <ol class="breadcrumb">
        <li><a href="/">Главная</a></li>
        <li>
            Сертификаты
        </li>
    </ol>
<section class="products-list">
        <!-- Heading Starts -->
        <h2 class="product-head">Сертификаты</h2>
        <!-- Heading Ends -->
        <!-- Products Row Starts -->
        <div class="row">
            <!-- Product #1 Starts -->
            <?php foreach($certificate as $product) {?>
                <input type="hidden" name="quantity" value="1"/>
                <div class="col-md-3 col-sm-6">
                    <div class="product-col">
                        <div class="image">
                            <a href="certificate_product/<?php echo $product->url?>">
                                <img src="<?php echo Lib_Image::resize_bg($product->image, 'certificate', $product->id, 250, 250); ?>" alt="product" class="img-responsive" />
                            </a>
                        </div>
                        <div class="caption">
                            <div class="price">
                                <span class="price-new" ><?php echo number_format($product->price, 0, ' ', ' '); ?>руб.</span>
                            </div>
                            <h4><a href="certificate_product/<?php echo $product->url?> "> <?php echo $product->name?> </a></h4>
                            <div class="description">
                                <?php echo $product->short_content?>
                            </div>
                            <div class="description">
                                Срок действия: <?php echo $product->validity?> дней
                            </div>
                            <div class="cart-button button-group">
                                <button class="btn btn-cart add_cart_sertificate topLink" data-id="<?php echo $product->id; ?>" data-price="<?php echo $product->price ?>">
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
    </div>


