<!-- Header Section Ends -->
<!-- Main Container Starts -->
<div id="main-container">
    <!-- Breadcrumb Starts -->
    <ol class="breadcrumb">
        <li><a href="/">Главная</a></li>
        <li><a href="/certificate">Сертификаты</a></li>
        <li><?php echo $name?></li>
    </ol>
    <!-- Breadcrumb Ends -->
    <!-- Product Info Starts -->
    <div class="row product-info full">
        <!-- Left Starts -->
        <div class="col-sm-4 images-block">
                <img src="<?php echo $image;?>" alt="Image" class="img-responsive thumbnail" />
        </div>
        <!-- Left Ends -->
        <!-- Right Starts -->
        <div class="col-sm-8 product-details">
            <div class="panel-smart">
                <!-- Product Name Starts -->
                <h2><?php echo $name?></h2>
                <hr />
                <!-- Price Starts -->
                <div class="price">
                    <span class="price-head">Цена :</span>
                    <span class="price-new">   <?php if(!$new_price){ echo number_format($price, 0, '', ' ');}  else { echo number_format($new_price, 0, '', ' ');} ?>  руб.</span>
                    <?php if($new_price){?><span class="price-old"><?php echo number_format($price, 0, '', ' ');?> руб.</span><?php } ?>
                </div>
                <hr />
                <div class="options">
                    <div class="form-group">
                        <p><?php echo $short_content;?></p>
                    </div>
                    <div class="form-group">
                        <label class="control-label text-uppercase" for="input-quantity">Количество:</label>
                        <input type="text" name="quantity" value="1" size="2" id="input-quantity" class="form-control" />
                    </div>
                    <div class="cart-button button-group">
                        <button type="button" title="Добавить в избранное" class="btn btn-wishlist add_wish" data-key="cert" data-id="<?php echo $id?>" data-price="<?php echo $price ?>" >
                            <i class="fa fa-heart"></i>
                        </button>
                        <button type="button" title="Добавить в корзину" class="btn btn-cart add_cart_sertificate" data-id="<?php echo $id?>" data-price="<?php echo $price ?> data-color='">
                            Добавить в карзину
                            <i class="fa fa-shopping-cart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Info Ends -->
    <!-- Tabs Starts -->
    <div class="tabs-panel panel-smart">
        <!-- Nav Tabs Starts -->
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab-description" data-toggle="tab">Описание</a>
            </li>
        </ul>
        <div class="tab-content clearfix">
            <!-- Description Starts -->
            <div class="tab-pane active" id="tab-description">
                <?php echo $content?>
            </div>
        </div>
    </div>
</div>

