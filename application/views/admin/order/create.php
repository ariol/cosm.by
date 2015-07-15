<style>
    .autocomplete-suggestions { border: 1px solid #999; background: #FFF; cursor: default; overflow: auto; -webkit-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); -moz-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); }
    .autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
    .autocomplete-no-suggestion { padding: 2px 5px;}
    .autocomplete-selected { background: #F0F0F0; }
    .autocomplete-suggestions strong { font-weight: bold; color: #000; }
    .autocomplete-group { padding: 2px 5px; }
    .autocomplete-group strong { font-weight: bold; font-size: 16px; color: #000; display: block; border-bottom: 1px solid #000; }
</style>
<div class="row reload">
    <!-- Shipping & Shipment Block Starts -->
    <div class="col-sm-12">
        <!-- Shipment Information Block Starts -->
        <div class="panel panel-smart">
            <div class="panel-heading">
                <h2 class="panel-title">
                    Оформление заказа
                </h2>
            </div>
            <div id="selection"></div>
                <form class="form-horizontal" role="form">
                    <div class="panel-smart panel_hidden">
                        <div class="product_table hidden">
                        <table class="table table-bordered table-striped table-condensed" id="tabledata">
                            <tbody class="main">
                            <tr>
                                <th>Наименование</th>
                                <th>Артикул</th>
                                <th>Кол-во</th>
                                <th>Цена за еденицу</th>
                                <th>Цена</th>
                                <th>Удалить</th>
                            </tr>
                            </tbody>
                            <tr data>
                                <td colspan="4">Итого:</td>
                                <td class="result_price " data-result_price=""></td>
                                <td><a href="#" data-type="recount" data-result_price="">пересчитать</a></td>
                            </tr>
                            </table>
                            </div>
                        <div class="certificate_table hidden">
                            <h3>Сертификаты</h3>
                            <table class="table table-bordered table-striped table-condensed" id="tabledata_certificate">
                                <tbody class="main_certificate">
                                <tr>
                                    <th>Наименование</th>
                                    <th>Количество</th>
                                    <th>Цена за еденицу</th>
                                    <th>Стоимость</th>
                                    <th>Удалить</th>
                                </tr>
                                </tbody>
                                <tr>
                                    <td colspan="3">Итого: </td>
                                    <td class="result_certificate_price"> </td>
                                    <td><a href="#" data-type="recount_certificate" data-last_price="<?php echo $full_price?>">пересчитать</a></td>
                                </tr>
                                </table>
                            </div>
                        </div>
                    <div class="panel-body">
                        <div class="input_product"></div>
                        <div class="form-group">
                            <label for="inputFname" class="col-sm-3 control-label">Товар :</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="query" value="" placeholder="наименование товара"  id="autocomplete"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputFname" class="col-sm-3 control-label">Сертификат :</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="certificate" value="" placeholder="наименование сертификата" value="" id="certificate"/>
                            </div>
                        </div>
                        <div class="data_recount" id="cancel_coupon" data-to_amount=" " data-discount=" " data-code_coupons=" ">
                            <div class="form-group not_coupon">
                                <label for="inputFname" class="col-sm-3 control-label">Код купона :</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" name="code_coupon"  value="" placeholder="код купона"  id="autocomplete_coupon"/>
                                    <span class="result_coupon"></span>
                                    <button type="submit" data-type="use_code" class="btn btn-black coupon">
                                        Применить купон
                                    </button>
                                </div>
                            </div>
                            <div class="form-group have_coupon hidden">
                                <label for="inputFname" class="col-sm-3 control-label used_coupon">Был исползован купон на скидку в <?php echo $discount ?>% </label>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-black coupon" data-type="cancel_coupon">
                                        Отменить купон
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="data_recount" id="cancel_certificate" data-code_certificate=" " data-discount=" " data-type="cancel_certifiacte" data-to_amount=" ">
                            <div class="form-group not_certificate">
                                <label for="inputFname" class="col-sm-3 control-label">Код сертификата :</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" name="code_certificate" value="" placeholder="код сертификата"  id="autocomplete_certificate"/>
                                    <span class="result_certificate"></span>
                                    <button type="submit" data-type="use_certificate" class="btn btn-black coupon">
                                        Применить сертификат
                                    </button>
                                </div>
                            </div>
                            <div class="form-group have_certificate hidden">
                                <label for="inputFname" class="col-sm-3 control-label used_certificate">Был исползован сертификат на сумму <?php echo $to_amount ?>руб. </label>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-black coupon" data-type="cancel_certificate">
                                        Отменить сертификат
                                    </button>
                                </div>
                            </div>
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
                    <div class="form-group">
                        <label for="inputAddress" class="col-sm-3 control-label">Город :</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="inputAddress"  name="city" placeholder="город">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputAddress" class="col-sm-3 control-label">Индекс :</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="inputAddress"  name="index" placeholder="индекс">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputAddress" class="col-sm-3 control-label">Примечание:</label>
                        <div class="col-sm-9">
                            <textarea rows="5"  class="form-control" id="inputAddress" name="comment" placeholder="примечание"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <p class="col-sm-3 control-label">Доставка:</p>
                        <div class="col-sm-9">
                            <label><input type="radio" name="delivery" value="5" /> Бесплатная доставка курьером</label><br>
                            <label><input type="radio" name="delivery" value="4" /> Бесплатная доставка наложным платежем</label><br>
                            <label calss="delivery2"><input type="radio"  name="delivery" value="2" checked/> Доставка курьером</label><br>
                            <label><input type="radio" name="delivery" value="3"/> Наложным платежем</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button  type="submit" class="btn btn-black coupon" data-type="add_order">
                                Оформить заказ
                            </button>
                        </div>
                    </div>
                </form>
                <!-- Form Ends -->
            </div>
        </div>
        </div>
<script>
    var ignore_ids = [];
    var ignore_ids_certificate= [];

//    recount();
//    recount_certificate();

    $(window).unload(function(){
        $.ajax({
            url: "/ariol-admin/order/empty_cart"
        });
        $.ajax({
            url: "/cart/empty_cart"
        });
        $.ajax({
            url: "/ariol-admin/order/empty_certificate_cart"
        });
        $.ajax({
            url: "/certificate/empty_certificate_cart"
        });
    });

    $.ajax({
        url: "/ariol-admin/order/empty_cart"
    });
    $.ajax({
        url: "/cart/empty_cart"
    });
    $.ajax({
        url: "/ariol-admin/order/empty_certificate_cart"
    });
    $.ajax({
        url: "/certificate/empty_certificate_cart"
    });

    function recount() {
        $.each($('.data_recount'), function (index, value) {
            var price = $(this).find('.price').attr('data-price');
            var quantity = $(this).find('input[name="quantity"]').val();
            var id_product = $(this).attr('data-id');
            var discount = $(this).attr('data-discount');
            var to_amount = $(this).attr('data-to_amount');
            $.ajax({
                url: "/ariol-admin/order/recount",
                type: "POST",
                dataType: "JSON",
                data: {
                    quantity: quantity,
                    price: price,
                    id: id_product,
                    discount: discount,
                    to_amount: to_amount
                },
                success: function (result) {
                    $('tr[data-id="' + result.id + '"] .price_quantity').text(result.price_view);
                    $('.result_price').text(result.last_price_view);
                    $('.result_price').attr('data-last_result_price', result.last_price);
                }
            });
        });
    }

    function recount_certificate() {
        $.each($('.data_recount_certificate'), function (index, value) {
            var price = $(this).find('.price_certificate').attr('data-price_certificate');
            var quantity = $(this).find('input[name="quantity_certificate"]').val();
            var id_product = $(this).attr('data-id');
            $.ajax({
                url: "/ariol-admin/order/recount_certificate",
                type: "POST",
                dataType: "JSON",
                data: {
                    quantity: quantity,
                    price: price,
                    id: id_product
                },
                success: function (result) {
                    $('tr[data-id="' + result.id + '"] .price_certificate_quantity').text(result.price_view);
                    $('.result_certificate_price').text(result.last_price_view);
                }
            });
        });
    }

    $('#autocomplete').autocomplete({
        source: function(request, response){
            $.ajax({
                url : "/ariol-admin/order/autocomplete",
                type : "POST",
                dataType : "json",
                data:{
                    maxRows: 12, // показать первые 12 результатов
                    query: request.term, // поисковая фраза
                    ignore_ids: ignore_ids
                },
                success: function(data){
                    response($.map(data, function(item){
                        return {
                            plink: item.product_id,
                            label: item.product + ' Объем: '+ item.volume + ' Артикул: ' + item.article,
                            price: item.price,
                            price_view: item.price_view,
                            article: item.article,
                            color: item.color,
                            volume: item.volume
                        }
                    }));
                }
            });
        },
        select: function( event, ui ) {
            event.preventDefault();
            $('.product_table.hidden').removeClass('hidden');
            $('input[name="query"]').val("");
            $('#tabledata tbody.main').append("<tr data-id='' class='data_recount'><td class='name'></td> <td class='article'></td> <td class='quantity'></td> <td class='price' data-price=''></td>  <td class='price_quantity' data-price_quantity=''></td>  <td class='dell' ></td> </tr>");
            $('#tabledata tbody.main tr:last-child').attr('data-id', ui.item.plink);
            $('#tabledata tbody.main tr:last-child .name').text(ui.item.label);
            if(ui.item.color) {
                var arr_color = ui.item.color.split(', ');
                for (var color in arr_color) {
                    $('#tabledata tbody.main tr:last-child .name').append("<div>" +
                    "                                                           <a href='#' class='a_color'  data-color='" + arr_color[color] + "' data-id='" + ui.item.plink + "'><div  class='color col-sm-4' style='background:" + arr_color[color] + "; width: 30px; height: 30px; margin:5px;'></div>" +
                    "                                                           </a>" +
                    "                                                       </div>");
                }
            }
            $('#tabledata tbody.main tr:last-child .article').text(ui.item.article);
            $('#tabledata tbody.main tr:last-child .quantity').append("<input class='form-control' type='text' name='quantity' size='2' value='1'/>");
            $('#tabledata tbody.main tr:last-child .price').text(ui.item.price_view);
            $('#tabledata tbody.main tr:last-child .price').attr('data-price', ui.item.price);
            $('#tabledata tbody.main tr:last-child .dell').append("<a href='#' data-id='' class='remove'>удалить</a>");
            $('#tabledata tbody.main tr:last-child .remove').attr('data-id', ui.item.plink);
            var quantity = $('input[name="quantity"]').val();
            if (ui.item.plink) {
                ignore_ids.push(ui.item.plink);
            }
            $.ajax({
                url: "/cart/add",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: ui.item.plink,
                    price: ui.item.price,
                    quantity: quantity
                },
                success: function(data){
                    recount();
                }
            });
        },
        minLength: 2
    });

    $('#certificate').autocomplete({
        source: function(request, response){
            $.ajax({
                url : "/ariol-admin/order/certificate",
                type : "POST",
                dataType : "json",
                data:{
                    maxRows: 12, // показать первые 12 результатов
                    certificate: request.term, // поисковая фраза
                    ignore_ids_certificate: ignore_ids_certificate
                },
                success: function(data){
                    response($.map(data, function(item){
                        return {
                            plink: item.product_id,
                            label: item.product,
                            price: item.price,
                            price_view: item.price_view
                        }
                    }));
                }
            });
        },
        select: function( event, ui ) {
            event.preventDefault();
            $('.certificate_table.hidden').removeClass('hidden');
            $('input[name="certificate"]').val("");
            $('#tabledata_certificate tbody.main_certificate').append("<tr data-id='' class='data_recount_certificate'><td class='name_certificate'></td> <td class='quantity_certificate'></td> <td class='price_certificate' data-price_certificate=''></td>  <td class='price_certificate_quantity'></td>  <td class='dell_certificate' ></td> </tr>");
            $('#tabledata_certificate tbody.main_certificate tr:last-child').attr('data-id', ui.item.plink);
            $('#tabledata_certificate tbody.main_certificate tr:last-child .name_certificate').text(ui.item.label);
            $('#tabledata_certificate tbody.main_certificate tr:last-child .quantity_certificate').append("<input class='form-control' type='text' name='quantity_certificate' size='2'  value='1'/>");
            $('#tabledata_certificate tbody.main_certificate tr:last-child .price_certificate').text(ui.item.price_view);
            $('#tabledata_certificate tbody.main_certificate tr:last-child .price_certificate').attr('data-price_certificate', ui.item.price);
            $('#tabledata_certificate tbody.main_certificate tr:last-child .dell_certificate').append("<a href='#' data-id='' class='remove_certificate'>удалить</a>");
            $('#tabledata_certificate tbody.main_certificate tr:last-child .remove_certificate').attr('data-id', ui.item.plink);
            var quantity = $('input[name="quantity_certificate"]').val();
            if (ui.item.plink) {
                ignore_ids_certificate.push(ui.item.plink);
            }
            $.ajax({
                url: "/certificate/add",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: ui.item.plink,
                    price: ui.item.price
                }
            });
            recount_certificate();
            return false;
        },
        minLength: 2
    });

    $('#autocomplete_coupon').autocomplete({
        source: function(request, response){
            $.ajax({
                url : "/ariol-admin/order/autocomplete_coupon",
                type : "POST",
                dataType : "json",
                data:{
                    maxRows: 12, // показать первые 12 результатов
                    code_coupon: request.term // поисковая фраза
                },
                success: function(data){
                    response($.map(data, function(item){
                        return {
                            plink: item.product_id,
                            label: item.code_coupon,
                            discount: item.discount_coupon
                        }
                    }));
                }
            });
        },
        select: function( event, ui ) {
            event.preventDefault();
            $('input[name="code_coupon"]').val(ui.item.label);
            return false;
        },
        minLength: 2
    });

    $('#autocomplete_certificate').autocomplete({
        source: function(request, response){
            $.ajax({
                url : "/ariol-admin/order/autocomplete_certificate",
                type : "POST",
                dataType : "json",
                data:{
                    maxRows: 12, // показать первые 12 результатов
                    code_certificate: request.term // поисковая фраза
                },
                success: function(data){
                    response($.map(data, function(item){
                        return {
                            plink: item.product_id,
                            label: item.code_certificate,
                            to_amount: item.to_amount
                        }
                    }));
                }
            });
        },
        select: function( event, ui ) {
            event.preventDefault();
            $('input[name="code_certificate"]').val(ui.item.label);
            return false;
        },
        minLength: 2
    });

</script>