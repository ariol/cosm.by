<div>
    <div id="logo">
        <img src="/images/logo.png" title="Chocolate Shoppe" alt="Chocolate Shoppe" class="img-responsive" />
    </div>
</div>
<?php if($order_product){?>
    <h2>Товары:</h2>
    <table class="table table-bordered table-striped table-condensed" id="tabledata">
        <tbody>
        <tr>
            <th>Наименование</th>
            <th>Артикул</th>
            <th>Кол-во</th>
            <th>Цена</th>
        </tr>
        <?php $full_price = 0; ?>
        <?php foreach ($order_product as $data) { ?>
            <?php $items = ORM::factory('Product')->where('id', '=', $data['product_id'])->find_all();?>
            <?php foreach ($items as $item) { ?>
                <tr>
                    <td>
                        <div><?php echo $item->name; ?></div>
                        <?php if($data['color']) { ?><div> <div class="color col-sm-4" style="background: <? echo $data['color'];?>; width: 30px; height: 30px; margin:5px;"></div></div><?php } ?>
                        <?php if($item['volume']) { ?><div>Объем: <?php echo $item['volume']?></div><?php } ?>
                    </td>
                    <td><?php echo $item->article; ?></td>
                    <td><?php echo $data['quantity']; ?></td>
                    <td><?php echo number_format($data['price'] * $data['quantity'], 0, ' ', ' '); ?>р.</td>
                    <?php $full_price += $data['price'] * $data['quantity']; ?>
                    <?php $full_price_not_discount += $data['price'] * $data['quantity']; ?>
                </tr>
            <?php } ?>
        <?php } ?>
        <tr>
            <td colspan="3">Итого: </td>
            <td id="full_price"><?php echo number_format($full_price, 0, ' ', ' '); ?> р.</td>
        </tr>
        <?php if(!$order_certificate and !$discount  and !$amount) { ?>
            <tr>
                <td colspan="3">Товары + <?php
                    switch($order->delivery) {
                        case '2':
                            echo "доставка курьером"; if($full_price_not_discount + $price_certificate < 600000) { $price_delivery = 30000; echo " 30 000 руб."; } else { $price_delivery = 0;  echo  " бесплатно" ;}
                            break;
                        case '3':
                            echo "доставка наложным платежем"; if($full_price_not_discount + $price_certificate < 1000000) { $price_delivery = 50000; echo " 50 000 руб.";}  else { $price_delivery = 0; echo " бесплатно" ;}
                            break;
                        case '4':
                            echo "бесплатная доставка курьером";
                            break;
                        case '5':
                            echo "бесплатная доставка наложным платежем";
                            break;
                    }
                    ?> </td>
                <td id="full_price"><?php echo number_format($full_price + $price_delivery, 0, ' ', ' '); ?> р.</td>
            </tr>
        <?php } ?>
        <?php if($discount) { ?>
            <tr>
                <td colspan="3">
                        <?php $full_price = $full_price - ($full_price / 100 * $discount);?>
                        Был использован купон на скидку в <?php echo $discount ?>%:
                </td>
            <td id="full_price"><?php echo number_format($full_price, 0, ' ', ' '); ?> р.</td>
                 <?php if(!$order_certificate  and !$amount) { ?>
            <tr>
                <td colspan="3">Товары + <?php
                    switch($order->delivery) {
                        case '2':
                            echo "доставка курьером"; if($full_price_not_discount + $price_certificate < 600000) { $price_delivery = 30000; echo " 30 000 руб."; } else { $price_delivery = 0;  echo  " бесплатно" ;}
                            break;
                        case '3':
                            echo "доставка наложным платежем"; if($full_price_not_discount + $price_certificate < 1000000) { $price_delivery = 50000; echo " 50 000 руб.";}  else { $price_delivery = 0; echo " бесплатно" ;}
                            break;
                        case '4':
                            echo "бесплатная доставка курьером";
                            break;
                        case '5':
                            echo "бесплатная доставка наложным платежем";
                            break;
                    }
                    ?> </td>
                <td id="full_price"><?php echo number_format($full_price + $price_delivery, 0, ' ', ' '); ?> р.</td>
            </tr>
        <?php } ?>
        </tr>
    <? } ?>
        <?php if($amount) { ?>
            <tr>
                <td colspan="4">
                    <?php $full_price = $full_price - $amount;
                    if($full_price < 0)$full_price = 0;?>
                    Был использован сертификат на сумму <?php echo $amount ?>руб.
                </td>
                <td id="full_price"><?php echo number_format($full_price, 0, ' ', ' '); ?> р.</td>
            </tr>
            <?php if(!$order_certificate) { ?>
                <tr>
                    <td colspan="3">Товары + <?php
                        switch($order->delivery) {
                            case '2':
                                echo "доставка курьером"; if($full_price_not_discount + $price_certificate < 600000) { $price_delivery = 30000; echo " 30 000 руб."; } else { $price_delivery = 0;  echo  " бесплатно" ;}
                                break;
                            case '3':
                                echo "доставка наложным платежем"; if($full_price_not_discount + $price_certificate < 1000000) { $price_delivery = 50000; echo " 50 000 руб.";}  else { $price_delivery = 0; echo " бесплатно" ;}
                                break;
                            case '4':
                                echo "бесплатная доставка курьером";
                                break;
                            case '5':
                                echo "бесплатная доставка наложным платежем";
                                break;
                        }
                        ?> </td>
                    <td id="full_price"><?php echo number_format($full_price + $price_delivery, 0, ' ', ' '); ?> р.</td>
                </tr>
            <?php } ?>
        <? } ?>
        </tbody>
    </table>
<?php } ?>
<?php if($order_certificate) { ?>
    <h2>Сертификаты:</h2>
    <table class="table table-bordered table-striped table-condensed" id="tabledata">
        <tr>
            <th>Наименование</th>
            <th>Код</th>
            <th>Стоимость</th>
        </tr>
        <?php foreach ($order_certificate as $certificate) { ?>
            <?php $certificate_items = ORM::factory('Certificate')->where('id', '=', $certificate['certificate_id'])->find_all();?>
            <?php foreach ($certificate_items as $item) { ?>
                <tr>
                    <td><?php echo $item->name; ?></td>
                    <td><?php echo $certificate['code']; ?></td>
                    <td><?php echo number_format($certificate['price'], 0, ' ', ' '); ?>р.</td>
                    <?php $price_certificate += $certificate['price'];?>
                </tr>
            <?php } ?>
        <?php } ?>
        <tr>
            <td colspan="2">Итого: </td>
            <td><?php echo number_format($price_certificate, 0, ' ', ' ');?></td>
        </tr>
        <tr>

            <td colspan="2"> <?php if($order_product) { ?> <?php if($full_price) { ?>Товар +   <?php } ?><?php } ?> сертификаты +  <?php
                switch($order->delivery) {
                    case '2':
                        echo "доставка курьером"; if($full_price_not_discount + $price_certificate < 600000) { $price_delivery = 30000; echo " 30 000 руб."; } else { $price_delivery = 0;  echo  " бесплатно" ;}
                        break;
                    case '3':
                        echo " доставка наложным платежем"; if($full_price_not_discount + $price_certificate < 1000000) { $price_delivery = 50000; echo " 50 000 руб.";}  else { $price_delivery = 0; echo " бесплатно" ;}
                        break;
                    case '4':
                        $price_delivery = 0;
                        echo "бесплатная доставка курьером";
                        break;
                    case '5':
                        $price_delivery = 0;
                        echo "бесплатная доставка наложным платежем";
                        break;
                }
                ?>:  </td>
            <td><?php echo number_format($full_price + $price_certificate + $price_delivery, 0, ' ', ' ');?></td>
        </tr>
    </table>
<?php } ?>

<?php if($coupon_order) { ?>
    <p>Купон на скидку в <?php echo $coupon_order ?>%. Срок действия <?php if($order->status == 4) { ?> до <?php echo $time_end?><?php } else { ?><?php echo $time_end?> суток <?php } ?></p>
    <p>Код купона: <?php echo $coupon_code?></p>
<?php } ?>
<h3>Дата заказа: <?php echo $order->created_at?></h3>
<h3>Данные заказчика:</h3>
<ul class="list-unstyled">
    <li>Имя: <?php echo $order->name; ?></li>
    <li>E-mail: <?php echo $order->email; ?></li>
    <li>Телефон: <?php echo $order->phone; ?></li>
    <li>Адрес: <?php echo $order->adress; ?></li>
    <?php if($order->delivery == 3){?>
        <li>Город: <?php echo $order->city; ?></li>
        <li>Индекс: <?php echo $order->index; ?></li>
    <?php } ?>
    <li>Доставка: <?php
        switch($order->delivery) {
            case '2':
                echo " курьером"; if($full_price_not_discount + $price_certificate < 600000) echo " 30 000 руб."; else echo " бесплатно" ;
                break;
            case '3':
                echo "Наложным платежем"; if($full_price_not_discount + $price_certificate < 1000000) echo " 50 000 руб."; else echo " бесплатно" ;
                break;
            case '4':
                echo "Бесплатная доставка курьером";
                break;
            case '5':
                echo "Бесплатная доставка наложным платежем";
                break;
        }
        ?>
    </li>
    <li>Примечание: <?php echo $order->comment; ?></li>
</ul>

