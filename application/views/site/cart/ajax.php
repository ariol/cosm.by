<?php
$cart = Session::instance()->get('cart');
$cartitems = json_decode($cart['cart']);
$cart_certificate = Session::instance()->get('cart_certificate');
$certificate_items = json_decode($cart_certificate['cart_certificate']);
?>

<div class="empty_cart" >
    <?php $resprice = 0;
    if ($cartitems or $certificate_items) { ?>
    <li>
        <table class="table hcart">
            <?php foreach ($cartitems as $key => $item) {
            $prod = ORM::factory('Product')->fetchProdById($item->id);
                $category = ORM::factory('Category')->where('id', '=', $prod->category_id)->find();
                if($prod->parent_product) {
                    $new_url = ORM::factory('Product')->fetchProdNewUrl($prod->parent_product);
                    $url = $new_url->url;
                }else{
                    $url = $prod->url;
                }
                if($prod->new_price)
                    $price = $prod->new_price;
                else
                    $price = $prod->price;
            $resprice = $price * $item->quantity;
            $lastprice += $price * $item->quantity;
            ?>
            <tr data-prod-id="<?php echo $key; ?>">
                <td class="text-center">
                    <a href="/<?php echo $category->url; ?>/<?php echo $url; ?>">
                        <img src="<?php echo Lib_Image::resize_bg($prod->main_image, 'product', $prod->id, 128, 128); ?>" alt="<?php echo $prod->name ?>"  title="image" class="img-thumbnail img-responsive" />
                    </a>
                </td>
                <td class="text-left">
                    <a data-name="prod_name" href="/<?php echo $category->url; ?>/<?php echo $url; ?>">
                        <?php echo $prod->name ?>
                    </a>
                    <div>Объем: <?php echo $prod->volume?></div>
                </td>
                <td class="text-right">x<?php echo $item->quantity?></td>
                <td class="text-right"><?php echo $resprice?>руб.</td>
                <td class="text-center">
                    <div class="remove" data-id="<?php echo $key ?>">
                        <a href="#">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php } ?>
            <?if($certificate_items) { ?>
                <tr>
                    <td class="text-right">Сертификаты: </td>
                </tr>
            <?php } ?>
            <?php foreach ($certificate_items as $key => $certificate) {
                $crt = ORM::factory('Certificate')->fetchCertificateById($certificate->id);
                $price = $crt->price;
                $resprice = $price * $certificate->quantity;
                $lastprice += $price * $certificate->quantity;
                ?>
                <tr data-cert-id="<?php echo $key; ?>">
                    <td class="text-center">
                        <a href="/sertificate/<?php echo $crt->url; ?>">
                            <img src="<?php echo Lib_Image::resize_width($crt->image, 'certificate', $crt->id, 128, null); ?>" alt="<?php echo $crt->name ?>"  title="image" class="img-thumbnail img-responsive" />
                        </a>
                    </td>
                    <td class="text-left">
                        <a data-name="prod_name" href="/sertificate/<?php echo $crt->url; ?>">
                            <?php echo $crt->name ?>
                        </a>
                    </td>
                    <td class="text-right">x<?php echo $certificate->quantity ?></td>
                    <td class="text-right"><?php echo  $resprice?>руб.</td>
                    <td class="text-center">
                        <div class="remove_certificate" data-id="<?php echo $key ?>">
                            <a href="#">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </li>
    <li>
        <table class="table table-bordered total">
            <tbody>
                <td class="text-right"><strong>Общая стоимость: </strong></td>
                <td class="text-left"><span class="resprice"><?php echo number_format($lastprice, 0, '', ' '); ?>руб</td>
            </tr>
            </tbody>
        </table>
        <p class="text-right btn-block1">
            <a href="/cart">
                Оформить заказ
            </a>
        </p>
    </li>
    <?php } else { ?>
        Ваша корзина пуста
    <?php } ?>
</div>
<script>
    $(document).ready(function() {
        $('.remove').click(function(e) {
            e.stopPropagation();
            e.preventDefault();
            var id = $(this).attr('data-id');
            $.ajax({
                url : "/cart/delete",
                type : "POST",
                dataType : "json",
                data : {id : id},
                success : function(data) {
                    $('tr[data-prod-id="' +id + '"]').remove();
                    $('.resprice').text(data.price_view+'руб.');
                    $('#cart-total').text(data.quantity);
                    if(data.quantity == '0') {
                        $('.empty_cart').text('Ваша корзина пуста');
                    }
                }
            });
        });
        $('.remove_certificate').click(function(e) {
            e.stopPropagation();
            e.preventDefault();
            var id = $(this).attr('data-id');
            $.ajax({
                url : "/certificate/delete",
                type : "POST",
                dataType : "json",
                data : {id : id},
                success : function(data) {
                    $('tr[data-cert-id="' +id + '"]').remove();
                    $('.resprice').text(data.price_view+'руб.');
                    $('#cart-total').text(data.quantity);
                    if(data.quantity == '0') {
                        $('.empty_cart').text('Ваша корзина пуста');
                    }
                }
            });
        });
    })
</script>