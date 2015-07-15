<p>В интернет-магазине https://<?=$_SERVER['SERVER_NAME']?> <?=date("d F Y");?> в <?=date("H:i:s");?> был изменен заказ на имя <?=$name;?>
<?php if ($email) { ?><p><strong>Email: </strong><?=$email;?></p><?php } ?>
<p><strong>Телефон: </strong><?=$phone;?></p>
<p><strong>Адрес: </strong><?=$adress;?></p>
<?php if($city) { ?><p><strong>Город: </strong><?php echo $city;?></p><?php } ?>
<?php if($index) { ?><p><strong>Индекс: </strong><?php echo $index;?></p><?php } ?>
<?php if($cart) { ?>
<?php foreach($cart as $item) {
    if($item->quantity > 0){
    $prod = ORM::factory('Product')->fetchProdById($item->id);?>
        $prod = ORM::factory('Product')->fetchProdById($item->id);?>
        <p><a target="_blank" href="http://<?=$_SERVER['SERVER_NAME']?><?php echo $prod->getSiteUrl(); ?>"><?=$prod->name;?></a></p>
        <p>Количество - <?=$item->quantity?></p>
        <p>Цена - <?=number_format($item->price, 0, '', ' ')?> руб</p>
        <p>_________________________</p>
        <?php $full_price_product += $item->price * $item->quantity; ?>
        <?php $full_price_not_discount += $item->price * $item->quantity; ?>
    <?php } ?>
<?php } ?>
<?php } ?>
<?php if($cert) { ?>
    <?php foreach($cert as $item) {
        $certificate = ORM::factory('Certificate')->fetchCertificateById($item->id);?>
        <p><a target="_blank" href="http://<?=$_SERVER['SERVER_NAME']?>/<?php echo $certificate->url; ?>"><?=$certificate->name;?></a></p>
        <p>Количество - <?=$item->quantity?></p>
        <p>Цена - <?=number_format($item->price, 0, '', ' ')?> руб</p>
        <p>_________________________</p>
        <?php $full_price_certificate += $item->price * $item->quantity;?>
    <?php } ?>
<?php } ?>
<?php if($code){ ?>
    <p>Был использован купон: <?php echo $code?></p>
<?php } ?>
<?php if($code_certificate){ ?>
    <p>Был использован сертификат: <?php echo $code_certificate?></p>
<?php } ?>
<p>_________________________</p>

<?php if ($coupon_discount) {
    $full_price_product =  $full_price_product - ($full_price_product / 100 * $coupon_discount);
} ?>

<?php if ($to_amount) {
    $full_price_product =  $full_price_product - $to_amount;
} ?>

Итого: <?php if($cart) echo "товары + "?><?php if($certificate_mail) echo "сертификаты + " ?>
<?php
switch($delivery) {
    case '2':
        echo "доставка курьером "; if($full_price_not_discount + $full_price_certificate < 600000) { $price_delivery = 30000; echo " 30 000 руб. "; } else { $price_delivery = 0;  echo  " бесплатно" ;}
        break;
    case '3':
        echo "доставка наложным платежем "; if($full_price_not_discount + $full_price_certificate < 1000000) { $price_delivery = 50000; echo " 50 000 руб. ";}  else { $price_delivery = 0; echo " бесплатно" ;}
        break;
    case '4':
        echo "бесплатная доставка курьером ";
        break;
    case '5':
        echo "бесплатная доставка наложным платежем ";
        break;
}
?>
= <?php echo $full_price_product + $full_price_certificate + $price_delivery?> руб.
