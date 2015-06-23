    <div id="main-container">
        <!-- Breadcrumb Starts -->
        <ol class="breadcrumb">
            <li><a href="/">Главная</a></li>
            <li class="active">Корзина</li>
        </ol>
        <!-- Breadcrumb Ends -->
        <!-- Main Heading Starts -->
        <h2 class="main-heading text-center">
            Корзина
        </h2>
        <!-- Main Heading Ends -->
        <!-- Shopping Cart Table Starts -->
        <div class="cartlayer">
            <div id="count_price">
        <div class="table-responsive shopping-cart-table">
            <?php $resprice = 0;
            if ($cartitems or $certificate) { ?>
            <?php if ($cartitems) { ?>
            <h3>Товары:</h3>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <td class="text-center">
                        Фото
                    </td>
                    <td class="text-center">
                        Наименование
                    </td>
                    <td class="text-center">
                        Количество
                    </td>
                    <td class="text-center">
                        Начальная стоимость
                    </td>
                    <td class="text-center">
                        Итоговая стоимость
                    </td>
                    <td class="text-center">
                        Пер-ть / Удалить
                    </td>
                </tr>
                </thead>
                <tbody>
    <?php
        foreach ($cartitems as $key => $item) {
        $prod = ORM::factory('Product')->fetchProdById($item->id);
        $url = $prod->url;
        $category = ORM::factory('Category')->where('id', '=', $prod->category_id)->find();
        if($prod->new_price)
            $price = $prod->new_price;
        else
            $price = $prod->price;
        $resprice = $price * $item->quantity;
        $lastprice += $resprice;
        $resprice = number_format($resprice, 0, '', ' ');
    ?>
                <tr data-id="<?php echo $key; ?>">
                    <td class="text-center">
                        <a href="/<?php echo $category->url; ?>/<?php echo $url; ?>">
                            <img src="<?php echo Lib_Image::resize_width($prod->main_image, 'product', $prod->id, 137, 183); ?>" alt="<?php echo $prod->name ?>" title="Product Name" class="img-thumbnail" />
                        </a>
                        <div>
                            <?php if($prod->color){
                                $color = explode(",", $prod->color);
                                $i = 0;
                                foreach($color as $items){?>
                                    <a href="#"  class="a_color <?php if($item->color == $items) {?> active<?php } ?>"  data-color="<?php echo $items?>" data-id="<?php echo $key; ?>">
                                        <div  class="color col-xs-4 " style="background: <? echo $items;?>; width: 30px; height: 30px; margin:5px;"></div>
                                    </a>
                                <?php $i++; } ?>
                           <?php }?>
                        </div>
                    </td>
                    <td class="text-center">
                        <a href="/<?php echo $category->url; ?>/<?php echo $url; ?>">
                            <?php echo $prod->name ?>
                        </a>
                        <?php if ($prod->volume) { ?>
                        <div>Объем: <?php echo $prod->volume?></div>
                        <?php } ?>
                    </td>
                    <td class="text-center">
                        <div class="input-group btn-block">
                            <input type="text" name="quantity" value="<?php echo $item->quantity ?>" size="1" class="form-control" />
                        </div>
                    </td>
                    <td class="text-center">
                        <?php echo number_format($price, 0, '', ' '); ?> руб.
                    </td>
                    <td class="text-center">
                        <span class="resprice" data-resprice="<?php echo $resprice;?>"><?php echo $resprice;?> руб.</span>
                    </td>
                    <td class="text-center">
                        <button name="recount"  type="submit" title="Пересчитать" class="btn btn-default tool-tip" data-id="<?php echo $key ?>" data-price="<?php echo $resprice ?>" data-prodprice="<?php echo $price ?>">
                            <i class="fa fa-refresh"></i>
                        </button>
                        <button data-type="remove" name="remove" type="button" title="Удалить" class="btn btn-default tool-tip remove" data-id="<?php echo $key ?>">
                            <i class="fa fa-times-circle"></i>
                        </button>
                    </td>
                </tr>
        <?php } ?>
                </tbody>
                <tfoot>
                <?php if ($cartitems and !$certificate) { ?>
                    <tr>
                        <td colspan="4" class="text-right">
                            <strong>Итого:</strong>
                        </td>
                        <td colspan="2" class="text-left price_total_product">
                            <span id="lastprice-product" data-lastprice_product="<?php echo $lastprice?>"></span> <span><?php echo number_format($lastprice, 0, '', ' '); ?> руб.</span>
                        </td>
                    </tr>
                <tr>
                    <td  colspan="4" class="text-right">
                        <strong class="delivery_type">Стоимость доставки курьером:</strong>
                    </td>
                    <td colspan="2" class="result_delivery"><?php if($lastprice  <= 600000) { ?>30 000 руб.<?php } else { ?>Бесплатно<?php }?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">
                        <strong>Итого к оплате:</strong>
                    <td colspan="2" class="last_result_delivery_price" data-last_result_delivery_price="<?php if($lastprice  <= 600000)   echo $lastprice + 30000; else  echo $lastprice ?>"><?php if($lastprice  <= 600000) {  echo number_format($lastprice + 30000, 0, '', ' '); ?> руб.<?php } else { echo number_format($lastprice, 0, '', ' '); ?> руб.<?php }?></td>
                    </td>
                </tr>
                <?php } else{ ?>
                    <tr>
                        <td colspan="4" class="text-right">
                            <strong>Итого:</strong>
                        </td>
                        <td colspan="2" class="text-left price_total_product">
                            <span id="lastprice-product" data-lastprice_product="<?php echo $lastprice; ?>"></span> <span><?php echo number_format($lastprice, 0, '', ' '); ?> руб.</span>
                        </td>
                    </tr>
                    <?php } ?>
                </tfoot>
            </table>
            <?php } ?>
    <?php if($certificate) { ?>
                <h3>Сертификаты:</h3>
    <table class="table table-bordered">
        <thead>
        <tr>
            <td class="text-center">
                Фото
            </td>
            <td class="text-center">
                Наименование
            </td>
            <td class="text-center">
                Количество
            </td>
            <td class="text-center">
                Цена за ед-цу
            </td>
            <td class="text-center">
                Цена
            </td>
            <td class="text-center">
                Пер-ть / Удалить
            </td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($certificate as $key => $certificates) {
            $crt = ORM::factory('Certificate')->fetchCertificateById($certificates->id);
            $certificare_price = $crt->price;
            $certificate_resprice = $certificare_price * $certificates->quantity;
            $certificate_lastprice += $certificate_resprice;
            $certificate_resprice = number_format($certificate_resprice, 0, '', ' ');
            ?>
            <tr data-id="<?php echo $key; ?>">
                <td class="text-center">
                    <a href="certificate_product/<?php echo $crt->url?>">
                        <img src="<?php echo Lib_Image::resize_width($crt->image, 'certificate', $crt->id, 147, null); ?>" alt="<?php echo $crt->name ?>" title="Product Name" class="img-thumbnail" />
                    </a>
                </td>
                <td class="text-center">
                    <a href="certificate_product/<?php echo $crt->url?>"><?php echo $crt->name ?></a>
                </td>
                <td class="text-center">
                    <div class="input-group btn-block">
                        <input type="text" name="quantity" value="<?php echo $certificates->quantity ?>" size="1" class="form-control" />
                    </div>
                </td>
                <td class="text-center">
                    <?php echo number_format($certificare_price, 0, '', ' '); ?> руб.
                </td>
                <td class="text-center">
                    <span class="resprice" data-resprice="<?php echo $certificate_resprice;?>"><?php echo $certificate_resprice;?> руб.</span>
                </td>
                <td class="text-center">
                    <button name="recount_certificate"  type="submit" title="Пересчитать" class="btn btn-default tool-tip" data-id="<?php echo $key ?>" data-price="<?php echo $certificate_resprice ?>" data-prodprice="<?php echo $certificate_price ?>">
                        <i class="fa fa-refresh"></i>
                    </button>
                    <button data-type="remove" name="remove_certificate" type="button" title="Удалить" class="btn btn-default tool-tip remove" data-id="<?php echo $key ?>">
                        <i class="fa fa-times-circle"></i>
                    </button>
                </td>
            </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <?php if (!$cartitems and $certificate) { ?>
                <tr>
                    <td colspan="4" class="text-right">
                        <strong>Итого:</strong>
                    </td>
                    <td colspan="2" class="text-left price_total_certificate" data-price_total_certificate="<?php echo $certificate_lastprice?>">
                        <?php echo number_format($certificate_lastprice, 0, '', ' '); ?> руб.
                    </td>
                </tr>
                <tr>
                    <td  colspan="4" class="text-right">
                        <strong class="delivery_type">Стоимость доставки курьером:</strong>
                    </td>
                    <td colspan="2" class="result_delivery"><?php if($certificate_lastprice  <= 600000) { ?>30 000 руб.<?php } else { ?>Бесплатно<?php }?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">
                        <strong>Итого к оплате:</strong>
                    <td colspan="2" class="last_result_delivery_price"><?php if($certificate_lastprice  <= 600000) {  echo number_format($certificate_lastprice + 30000, 0, '', ' '); ?> руб.<?php } else { echo number_format($certificate_lastprice, 0, '', ' '); ?> руб.<?php }?></td>
                    </td>
                </tr>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="text-right">
                            <strong>Итого:</strong>
                        </td>
                        <td colspan="2" class="text-left price_total_certificate" data-price_total_certificate="<?php echo $certificate_lastprice?>">
                            <?php echo number_format($certificate_lastprice, 0, '', ' '); ?> руб.
                        </td>
                    </tr>
                    <tr>
                        <td  colspan="4" class="text-right">
                            <strong class="delivery_type">Стоимость доставки курьером:</strong>
                        </td>
                        <td colspan="2" class="result_delivery"><?php if($certificate_lastprice + $lastprice <= 600000) { ?>30 000 руб.<?php } else { ?>Бесплатно<?php }?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right">
                            <strong>Итого к оплате:</strong>
                        <td colspan="2" class="last_result_delivery_price" data-last_result_delivery_price="<?php if($lastprice + $certificate_lastprice  <= 600000)   echo $lastprice + $certificate_lastprice + 30000; else  echo $lastprice + $certificate_lastprice ?>">
                            <?php if($certificate_lastprice + $lastprice <= 600000) { $res_delivery =  $certificate_lastprice + $lastprice + 30000; echo number_format($res_delivery, 0, '', ' '); ?> руб.<?php } else { $res_delivery =  $certificate_lastprice + $lastprice; echo number_format($res_delivery, 0, '', ' '); ?> руб.<?php }?>
                        </td>
                        </td>
                    </tr>
                <?php } ?>
                </tfoot>
            </table>
            <?php } ?>
        </div>
        <!-- Shopping Cart Table Ends -->
        <!-- Shipping Section Starts -->
        <section class="registration-area">
            <div class="row">
                <!-- Shipping & Shipment Block Starts -->
                <div class="col-sm-6">
                    <!-- Shipment Information Block Starts -->
                    <div class="panel panel-smart">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Оформление заказа
                            </h3>
                        </div>
                        <div class="panel-body">
                            <!-- Form Starts -->
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <p class="col-sm-3 control-label">Доставка:</p>
                                    <div class="col-sm-9">
                                        <label class="hide_city"><input type="radio" name="delivery" data-price="50000"  value="2" checked/> Доставка курьером (<?php if($lastprice + $certificate_lastprice <= 600000) { ?>30 000.руб<?php } else { ?>Бесплатно<?php }?>)</label><br>
                                        <label class="show_city"><input type="radio" name="delivery" data-price="50000" value="3"/> Наложным платежем (<?php if($lastprice + $certificate_lastprice <= 1000000) { ?>50 000.руб<?php } else { ?>Бесплатно<?php }?>)</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputFname" class="col-sm-3 control-label">Имя :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="inputFname" name="name" placeholder="имя">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail" class="col-sm-3 control-label">Email :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="inputEmail" name="email" placeholder="email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPhone" class="col-sm-3 control-label">Телефон :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="inputPhone" name="phone" placeholder="телефон">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputAddress" class="col-sm-3 control-label">Адрес :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="inputAddress" name="adress" placeholder="адрес">
                                    </div>
                                </div>
                                <div class="form-group city hidden">
                                    <label for="inputAddress" class="col-sm-3 control-label">Город :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="inputAddress" name="city" placeholder="адрес">
                                    </div>
                                </div>
                                <div class="form-group city hidden">
                                    <label for="inputAddress" class="col-sm-3 control-label">Индекс :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="inputAddress" name="index" placeholder="адрес">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <button type="submit" class="btn btn-black coupon" data-type="add_order">
                                            Оформить заказ
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!-- Form Ends -->
                        </div>
                    </div>
                    <!-- Shipment Information Block Ends -->
                </div>

                <!-- Shipping & Shipment Block Ends -->
                <!-- Discount & Conditions Blocks Starts -->
                <div class="col-sm-6">
                    <!-- Discount Coupon Block Starts -->
                    <div class="panel panel-smart">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Скидочный купон
                            </h3>
                        </div>
                        <div class="panel-body">
                            <!-- Form Starts -->
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <label for="inputCouponCode" class="col-sm-3 control-label">Код :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="inputCouponCode" name="coupon" placeholder="Введите код">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-2">
                                        <button <?php if(!$cartitems) { ?>disabled="disabled" <?php }?>data-type="use_code" data-price="<?php echo $lastprice?>" type="submit" class="btn btn-default use_coupon_disabled" >
                                            Применить купон
                                        </button>
                                    </div>
                                    <div class="col-sm-offset-3 col-sm-2 dell_coupon hidden">
                                        <button data-price="<?php echo $lastprice?>" data-type="remove_code" type="submit" class="btn btn-default" >
                                            Отменить
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!-- Form Ends -->
                        </div>
                    </div>
                    <!-- Conditions Panel Ends -->
                    <!-- Total Panel Starts -->
                    <div class="panel panel-smart hidden">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Скидка
                            </h3>
                        </div>
                        <div class="panel-body">
                            <dl class="dl-horizontal">
                                <dt>Скидка по купону :</dt>
                                <dd class="discount"></dd>
                                <dt>Перв-ная стоимость:</dt>
                                <dd class="price_start_coupon"> <?php echo number_format($lastprice, 0, '', ' '); ?> руб.</dd>
                            </dl>
                            <hr />
                            <dl class="dl-horizontal total">
                                <dt >Рез-ая стоимость :</dt>
                                <dd class="price_coupon code_coupon" data-code_coupon=""></dd>
                            </dl>
                            <hr />
                        </div>
                    </div>
                    <div class="panel panel-smart">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Подарочный сертификат
                            </h3>
                        </div>
                        <div class="panel-body">
                            <!-- Form Starts -->
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <label for="inputCouponCode" class="col-sm-3 control-label">Номер :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="inputCouponCode" name="certificate" placeholder="Введите номер сертификата">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-2">
                                        <button <?php if(!$cartitems) { ?>disabled="disabled" <?php }?> data-type="use_certificate" data-price="<?php echo $lastprice?>" type="submit" class="btn btn-default use_certificate_disabled" >
                                            Применить сертификат
                                        </button>
                                    </div>
                                    <div class="col-sm-offset-3 col-sm-2 dell_certificate hidden">
                                        <button data-type="remove_code" type="submit" class="btn btn-default" >
                                            Отменить
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!-- Form Ends -->
                        </div>
                    </div>
                    <!-- Conditions Panel Ends -->
                    <!-- Total Panel Starts -->
                    <div class="certificate panel_certificate hidden">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Сертификат принят
                            </h3>
                        </div>
                        <div class="panel-body">
                            <dl class="dl-horizontal">
                                <dt>Сумма сертификата : </dt>
                                <dd class="sum"></dd>
                                <dt>Перв-ная стоимость:</dt>
                                <dd class="price_start"><?php echo number_format($lastprice, 0, '', ' '); ?> руб.</dd>
                            </dl>
                            <hr />
                            <dl class="dl-horizontal total">
                                <dt >Рез-ая стоимость :</dt>
                                <dd class="price_certificate code_certificate" data-price="<?php echo $lastprice?>" data-code_certificate=""></dd>
                            </dl>
                            <hr />
                        </div>
                    </div>
                    <!-- Total Panel Ends -->
                </div>


                <!-- Discount & Conditions Blocks Ends -->
            </div>
            </div>

            <div style="display:none;">
                <div id="view_order_last">
                    <div>
                        <div id="logo">
                            <a href="/"><img src="/images/logo.png" title="Chocolate Shoppe" alt="Chocolate Shoppe" class="img-responsive" /></a>
                        </div>
                    </div>
                    <h3>Ваш заказ:</h3>
                    <?php $resprice = 0;
                    if ($cartitems or $certificate) { ?>
                        <?php if ($cartitems) { ?>
                            <h5>Товары:</h5>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <td class="text-center">
                                        Наименование
                                    </td>
                                    <td class="text-center">
                                        Количество
                                    </td>
                                    <td class="text-center">
                                        Стоимость
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($cartitems as $key => $item) {
                                    $prod = ORM::factory('Product')->fetchProdById($item->id);
                                    $url = $prod->url;
                                    $category = ORM::factory('Category')->where('id', '=', $prod->category_id)->find();
                                    if($prod->new_price)
                                        $price = $prod->new_price;
                                    else
                                        $price = $prod->price;
                                    ?>
                                    <tr data-id="<?php echo $key; ?>">
                                        <td class="text-center">
                                            <a href="/<?php echo $category->url; ?>/<?php echo $url; ?>">
                                                <?php echo $prod->name ?>
                                            </a>
                                            <?php if ($prod->volume) { ?>
                                                <div>Объем: <?php echo $prod->volume?></div>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="input-group btn-block">
                                                <?php echo $item->quantity ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="resprice" data-resprice="<?php echo $resprice;?>"><?php echo $item->quantity * $price;?> руб.</span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                <?php if ($cartitems and !$certificate) { ?>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <strong>Итого:</strong>
                                        </td>
                                        <td colspan="2" class="text-left price_total_product">
                                            <span id="lastprice-product" data-lastprice_product="<?php echo $lastprice?>"></span> <span><?php echo number_format($lastprice, 0, '', ' '); ?> руб.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <strong class="delivery_type">Стоимость доставки курьером:</strong>
                                        </td>
                                        <td class="result_delivery"><?php if($lastprice  <= 600000) { ?>30 000 руб.<?php } else { ?>Бесплатно<?php }?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <strong>Итого к оплате:</strong>
                                        <td class="last_result_delivery_price" data-last_result_delivery_price="<?php if($lastprice  <= 600000)   echo $lastprice + 30000; else  echo $lastprice ?>"><?php if($lastprice  <= 600000) {  echo number_format($lastprice + 30000, 0, '', ' '); ?> руб.<?php } else { echo number_format($lastprice, 0, '', ' '); ?> руб.<?php }?></td>
                                        </td>
                                    </tr>
                                <?php } else{ ?>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <strong>Итого:</strong>
                                        </td>
                                        <td class="text-left price_total_product">
                                            <span id="lastprice-product" data-lastprice_product="<?php echo $lastprice; ?>"></span> <span><?php echo number_format($lastprice, 0, '', ' '); ?> руб.</span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tfoot>
                            </table>
                        <?php } ?>
                        <?php if($certificate) { ?>
                            <h5>Сертификаты:</h5>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <td class="text-center">
                                        Наименование
                                    </td>
                                    <td class="text-center">
                                        Количество
                                    </td>
                                    <td class="text-center">
                                        Цена
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($certificate as $key => $certificate) {
                                    $crt = ORM::factory('Certificate')->fetchCertificateById($certificate->id);
                                    $certificare_price = $crt->price;
                                    ?>
                                    <tr data-id="<?php echo $key; ?>">
                                        <td class="text-center">
                                            <a href="certificate_product/<?php echo $crt->url?>"><?php echo $crt->name ?></a>
                                        </td>
                                        <td class="text-center">
                                            <div class="input-group btn-block">
                                                <?php echo $certificate->quantity ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="resprice" data-resprice="<?php echo $certificate_resprice;?>"><?php echo $crt->price * $certificate->quantity;?> руб.</span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                <?php if (!$cartitems and $certificate) { ?>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <strong>Итого:</strong>
                                        </td>
                                        <td class="text-left price_total_certificate" data-price_total_certificate="<?php echo $certificate_lastprice?>">
                                            <?php echo number_format($certificate_lastprice, 0, '', ' '); ?> руб.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  colspan="2" class="text-right">
                                            <strong class="delivery_type">Стоимость доставки курьером:</strong>
                                        </td>
                                        <td class="result_delivery"><?php if($certificate_lastprice  <= 600000) { ?>30 000 руб.<?php } else { ?>Бесплатно<?php }?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <strong>Итого к оплате:</strong>
                                        <td class="last_result_delivery_price"><?php if($certificate_lastprice  <= 600000) {  echo number_format($certificate_lastprice + 30000, 0, '', ' '); ?> руб.<?php } else { echo number_format($certificate_lastprice, 0, '', ' '); ?> руб.<?php }?></td>
                                        </td>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <strong>Итого:</strong>
                                        </td>
                                        <td class="text-left price_total_certificate" data-price_total_certificate="<?php echo $certificate_lastprice?>">
                                            <?php echo number_format($certificate_lastprice, 0, '', ' '); ?> руб.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  colspan="2" class="text-right">
                                            <strong class="delivery_type">Стоимость доставки курьером:</strong>
                                        </td>
                                        <td  class="result_delivery"><?php if($certificate_lastprice + $lastprice <= 600000) { ?>30 000 руб.<?php } else { ?>Бесплатно<?php }?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <strong>Итого к оплате:</strong>
                                        <td class="last_result_delivery_price" data-last_result_delivery_price="<?php if($lastprice + $certificate_lastprice  <= 600000)   echo $lastprice + $certificate_lastprice + 30000; else  echo $lastprice + $certificate_lastprice ?>">
                                            <?php if($certificate_lastprice + $lastprice <= 600000) { $res_delivery =  $certificate_lastprice + $lastprice + 30000; echo number_format($res_delivery, 0, '', ' '); ?> руб.<?php } else { $res_delivery =  $certificate_lastprice + $lastprice; echo number_format($res_delivery, 0, '', ' '); ?> руб.<?php }?>
                                        </td>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tfoot>
                            </table>
                        <?php } ?>
                    <?php } ?>
                    <div class="form-group">
                        <a href="/">
                            <div class="col-sm-offset-5 col-sm-4">
                                <button type="submit" class="btn btn-black coupon not_order">
                                    На главную
                                </button>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        <!-- Shipping Section Ends -->
            <?php } else {?>
                <h3>Ваша корзина пуста</h3>
            <?php } ?>
        </section>
    </div>
        </div>
<script>
    var price_total_delivery = parseInt("<?php echo $lastprice + $certificate_lastprice; ?>");
    var price_total_delivery_start = parseInt("<?php echo $lastprice + $certificate_lastprice; ?>");
    var price_delivery;
    $(document).ready(function(){
        $('button[name="cart"]').addClass('disabled');
    });
</script>