function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}
$.fn.equalizeHeights = function() {
    var maxHeight = this.map(function(i,e) {
        return $(e).height();
    }).get();

    return this.height( Math.max.apply(this, maxHeight) );
};
function numFormat(n, d, s) { // number format function
    if (arguments.length == 2) { s = " "; }
    if (arguments.length == 1) { s = " "; d = "."; }
    n = n.toString();
    a = n.split(d);
    x = a[0];
    y = a[1];
    z = "";
    if (typeof(x) != "undefined") {
        for (i=x.length-1;i>=0;i--)
            z += x.charAt(i);
        z = z.replace(/(\d{3})/g, "$1" + s);
        if (z.slice(-s.length) == s)
            z = z.slice(0, -s.length);
        x = "";
        for (i=z.length-1;i>=0;i--)
            x += z.charAt(i);
        if (typeof(y) != "undefined" && y.length > 0)
            x += d + y;
    }
    return x;
}
$(document).ready(function() {
	$('.scrollbar-light').scrollbar();
    $('.filter-group input').on('change', function() {
        $('#filters').submit();
    });
    $('#brand_lines input').on('change', function() {
        $('#brand_lines').submit();
    });
    $( '.a_color' ).click(function(e) {
        var id = $(this).attr('data-id');
        var color = $(this).attr('data-color');
        e.preventDefault();
        $.ajax({
            url: '/cart/add',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id,
                color: color
            },
            success: function (result) {
                $('tr[data-id="'+result.id+'"] .a_color.active').removeClass('active');
                $('tr[data-id="'+result.id+'"] a[data-color="'+result.color+'"]').addClass('active');
            }
        });
    });

    $('#select').on('change', function(){
        var option = $('#select option:selected');
        var volume = option.val();
        var id = option.attr('data-id');
        var price = option.attr('data-price');
        var article = option.attr('data-article');
        $('.add_cart').attr('data-id', id);
        $('#volume').text(volume);
        $('.price-new').text(price + ' руб.');
        $('#article').text(article);
    });

    $('.choose_color').click(function(e){
        e.preventDefault();
        var color =  $(this).attr('data-color');
        var id =  $(this).attr('data-id');
        $('.choose_color.active').removeClass('active');
        $('a[data-color="'+color+'"]').addClass('active');
    });

    $('.add_cart').click(function () {
        var id = $(this).attr('data-id');
        var price = $(this).attr('data-price');
        var quantity = $('input[name="quantity"]').val();
        var color = $('.a_color.active').attr('data-color');
        var choose_color = $('.choose_color.active').attr('data-color');
        $.ajax({
            url: '/cart/add',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id,
                price: price,
                quantity: quantity,
                color: color,
                choose_color: choose_color
            },
            success: function (result) {
                $('#cart-total').text(result.quantity);
                $('#cart-total').attr('data-count', result.quantity);
            }
        });
    });

    $('.add_cart_sertificate').click(function () {
        var id = $(this).attr('data-id');
        var price = $(this).attr('data-price');
        var quantity = $('input[name="quantity"]').val();
        $.ajax({
            url: '/certificate/add',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id,
                price: price,
                quantity: quantity
            },
            success: function (result) {
                $('#cart-total').text(result.quantity);
                $('#cart-total').attr('data-count', result.quantity);
            }
        });
    });

    $('.add_wish').click(function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var price = $(this).attr('data-price');
        var key = $(this).attr('data-key');
        $.ajax({
            url: '/like/add',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id,
                price: price,
                key: key
            },
            success: function (result) {
                $('#like-total').text(result.summlikes);
                $('#like-total').attr('data-like', result.summlikes);
                alert("Товар дабавлен в избранное");
            }
        });
    });

    $('button[name="recount"]').click(function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var quantity = $('tr[data-id="' +id + '"] input[name="quantity"]').val();
        var prodprice = $(this).attr('data-prodprice');
        $.ajax({
            url: '/cart/recount',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id,
                quantity: quantity,
                prodprice: prodprice
            },
            success: function (result) {
                $('#cart-total').text(result.quantity);
                $('#cart-total').attr('data-count', result.quantity);
                $('tr[data-id="' +id + '"] input[name="quantity"]').val(result.quantity_prod);
                window.location.reload(true);
            }
        });
    });
    $('button[name="recount_certificate"]').click(function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var price = $(this).attr('data-price');
        var quantity = $('tr[data-id="' +id + '"] input[name="quantity"]').val();
        var prodprice = $(this).attr('data-prodprice');
        $.ajax({
            url: '/certificate/recount',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id,
                price: price,
                quantity: quantity,
                prodprice: prodprice
            },
            success: function (result) {
                $('#cart-total').text(result.quantity);
                $('#cart-total').attr('data-count', result.quantity);
                $('tr[data-id="' +id + '"] input[name="quantity"]').val(result.quantity_prod);
                window.location.reload(true);
            }
        });
    });

    $('button[name="remove_wish"]').click(function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $.ajax({
            url : "/like/delete",
            type : "POST",
            dataType : "json",
            data : {id : id},
            success : function(data) {
                $("tr[data-type=" +id + "]").remove();
                window.location.reload();
            }
        });
    });

    $('button[name=remove]').click(function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $.ajax({
            url : "/cart/delete",
            type : "POST",
            dataType : "json",
            data : {id : id},
            success : function(data) {
               window.location.reload();
            }
        });
    });
    $('button[name=remove_certificate]').click(function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $.ajax({
            url : "/certificate/delete",
            type : "POST",
            dataType : "json",
            data : {id : id},
            success : function(data) {
               window.location.reload();
            }
        });
    });

    $(".search input").keyup(function(e){
        $('button[name="search_button"]').addClass('loading');
        var q = $(".search input").val();
        var ajax = null;
        function search_func(value)
        {
            if (ajax != null) ajax.abort();
            ajax = $.ajax({
                url: "/search/ajax",
                data: {q: q, order: 'price',dest: 'asc'},
                type: "post",
                dataType : "json",
                success: function(result){
                    $("#result").html(result.view);
                    var load = true;
                }
            });
        }
        search_func(q);
        return false;
    });

    $('#open_cart').click(function () {
        $.ajax({
            url: "/cart/ajax",
            type: "post",
            dataType: "json",
            success: function (result) {
                $('.result').html(result.view);
            }

        });
    });
function suces_order(){
    $.magnificPopup.open({
        items: {
            src: '#view_order_last'
        },
        type: 'inline'
    });
}

    $('button[data-type=add_order]').click(function(e) {
        e.preventDefault();
        var error = 0;
        var error_color = 0;
        var name = $('input[name="name"]').val();
        var email = $('input[name="email"]').val();
        var phone = $('input[name="phone"]').val();
        var adress = $('input[name="adress"]').val();
        var city = $('input[name="city"]').val();
        var index = $('input[name="index"]').val();
        var coupon = $('.code_coupon').attr('data-code_coupon');
        var delivery = $('input[name="delivery"]:checked').val();
        var certificate = $('.code_certificate').attr('data-code_certificate');
        if(!name || !phone || !adress){
            alert('Заполните обязательные поля');
            return false;
        }
        if(!isValidEmailAddress(email)){
            alert('Проверте праильность email');
            return false;
        }
        $.each($('tr .a_color'), function (index, value) {
            var id = $(this).attr('data-id');
            if($('a[data-id="'+id+'"]').hasClass('active') == false){
                $('a[data-id="'+id+'"]').parent().parent().find('img').css('border-color','red');
                error ++;
                error_color++;
                var scrollTop = $('tr[data-id="'+id+'"]').offset().top;
                $(document).scrollTop(scrollTop);
            }else{
                $('a[data-id="'+id+'"]').parent().parent().find('img').css('border-color','#ddd');
            }
        });
        if(delivery == 3){
            if(!city || !index){
                alert('Заполните обязательные поля');
                return false;
            }
        }
        if( error_color > 0) {
            alert("Выберите цвет");
            return false;
        }
        if(error > 0) {
            return false;
        }
        $.ajax({
            url : "/cart/order",
            dataType : "json",
            type : "post",
            data : {
                name : name,
                email : email,
                phone : phone,
                adress : adress,
                delivery:delivery,
                coupon: coupon,
                certificate: certificate,
                city: city,
                index: index
            },
            success : function(result) {
                alert('Ваш заказ принят');
                suces_order();
                $('#cart-total').text('0');
                $('.cartlayer').html('<p>Спасибо, ваш заказ принят! В ближайшее время с вами свяжется наш менеджер.</p> <h3>Ваша корзина пуста</h3>');
            }
        });
    });

    $('button[data-type=add_review]').click(function(e) {
        e.preventDefault();
        var prod_id = $(this).attr('data-id');
        var name = $('input[name="name"]').val();
        var content = $('textarea[name="content"]').val();
        var rating = $('input[name="rating"]:checked').val();
        if (!name, !content) {
            alert('Заполните поля');
            return false;
        }
        $.ajax({
            url : "/reviews/add",
            dataType : "json",
            type : "post",
            data : {prod_id: prod_id, name : name, content : content, rating : rating},
            success : function(jsondata) {
                $('.revews').html('<h3>Спасибо за оставленный отзыв!</h3>');

            }
        });
    });

    //Скролинг вверх страницы
    $(function() {
        $(window).scroll(function() {
            if($(this).scrollTop() != 0) {
                $('.add_cart').fadeIn();
            } else {
                $('.add_cart').fadeOut();
            }
        });
        $('.add_cart').click(function() {
            $('body,html').animate({scrollTop:0},800);
        });
    });

    //Скролинг вверх страницы из сертификатов
    $(function() {
        $(window).scroll(function() {
            if($(this).scrollTop() != 0) {
                $('.add_cart_sertificate').fadeIn();
            } else {
                $('.add_cart_sertificate').fadeOut();
            }
        });
        $('.add_cart_sertificate').click(function() {
            $('body,html').animate({scrollTop:0},800);
        });
    });

    $('button[data-type=use_code]').click(function(e) {
        e.preventDefault();
        var coupon = $('input[name="coupon"]').val();
        var price = $('#lastprice-product').attr('data-lastprice_product');
        var total_price = $('.last_result_delivery_price').attr('data-last_result_delivery_price');
        var price_certificate = $('.price_total_certificate').attr('data-price_total_certificate');
        if (!coupon) {
            alert('Введите купон');
            return false;
        }
        $.ajax({
            url : "/cart/coupon",
            type : "POST",
            dataType : "json",
            data : {coupon: coupon, price: price, total_price: total_price, price_total_delivery: price_total_delivery, price_certificate: price_certificate },
            success : function(result) {
                if(!result.discount)
                    alert("Неверный номер купона");
                else {
                    alert("Купон на скидку в " + result.discount + "% принят");
                    $('#lastprice-product').attr('data-lastprice_product', result.price);
                    $('.code_coupon').attr('data-code_coupon', result.code);
                    $('.panel.panel-smart.hidden').removeClass('hidden');
                    $('.discount').text(result.discount + "%");
                    $('.price_coupon').text(result.price_view +" руб.");
                    $('.price_start_coupon').text(result.price_start +" руб.");
                    $('.price_total_product span:nth-child(2)').text(result.price_view +" руб." + " (скидка " + result.discount + "%)");
                    $('.last_result_delivery_price').text(result.total_price_view +" руб.");
                    $('.last_result_delivery_price').attr('data-last_result_delivery_price', result.total_price);
                    $('.use_coupon_disabled').attr('disabled', 'disabled');
                    $('.dell_coupon.hidden').removeClass('hidden');
                    price_total_delivery = result.total_price;
                    price_total_delivery_start = result.total_price_start;
                    price_delivery = result.price_delivery;
                }
            }
        });
    });

    $('button[data-type=remove_code]').click(function(e) {
        e.preventDefault();
        window.location.reload();
    });

    $('button[data-type=use_certificate]').click(function(e) {
        e.preventDefault();
        var certificate = $('input[name="certificate"]').val();
        var price = $('#lastprice-product').attr('data-lastprice_product');
        var total_price = $('.last_result_delivery_price').attr('data-last_result_delivery_price');
        var price_certificate = $('.price_total_certificate').attr('data-price_total_certificate');
        if (!certificate) {
            alert('Введите номер сертификата');
            return false;
        }
        $.ajax({
            url : "/cart/certificate",
            type : "POST",
            dataType : "json",
            data : {certificate: certificate, price: price, total_price: total_price, price_certificate: price_certificate, price_delivery: price_delivery },
            success : function(result) {
                if(result.sum <= 0)
                    alert("Неверный номер сертификата");
                else {
                    alert("Сертификат на сумму в " + result.sum + "руб. принят");
                    $('#lastprice-product').attr('data-lastprice_product', result.price);
                    $('.code_certificate').attr('data-code_certificate', result.code);
                    $('.certificate.panel_certificate.hidden').removeClass('hidden');
                    $('.sum').text(result.sum + ' руб.');
                    $('.price_start').text(result.price_start_view + "руб.");
                    $('.price_certificate').text(result.price_view + " руб.");
                    $('.price_total_product span:nth-child(2)').text(result.price_view +" руб." + " (применен сертификат на сумму " + result.sum +" руб." + ")");
                    $('.last_result_delivery_price').text(result.total_price_view +" руб.")
                    $('.last_result_delivery_price').attr('data-last_result_delivery_price', result.total_price)
                    $('.use_certificate_disabled').attr('disabled', 'disabled');
                    $('.dell_certificate.hidden').removeClass('hidden')
                    price_total_delivery = result.total_price;
                    price_total_delivery_start = result.total_price_start;
                    price_delivery = result.price_delivery;
                }
            }
        });
    });
    $(document).click( function(event){
        if($(event.target).closest(".search").length== 0) {
            $(".search").addClass('hide-res');
        }
        event.stopPropagation();
    });
    $(".search input").keyup(function(e){
        $('.search.hide-res').removeClass('hide-res');
    });
    $('.products-list').each(function(i,elem) {
        $(this).find('.caption').equalizeHeights();
    });
    $('.add_cart.change_add').click(function(e){
        $(".add_cart.change_add").wrap("<a href='/cart'></a>");
        $(".add_cart.change_add").text("Перейти в корзину");
    });

    $("input[name='delivery']").change(function(e) {
        var value = $(this).val();
            if( price_total_delivery_start > price_total_delivery) {
                if (value == 3) {
                    $(".form-group.city.hidden").removeClass("hidden");
                    if (price_total_delivery_start < 1000000 && price_total_delivery_start < 600000) {
                        var total_price = price_total_delivery - price_delivery + 50000;
                        $('.last_result_delivery_price').text(numFormat(total_price) + ' руб.');
                        $('.delivery_type').text("Стоимость доставки наложным платежем:");
                        $('.result_delivery').text(numFormat(50000) + ' руб.');
                        $('.last_result_delivery_price').attr('data-last_result_delivery_price', total_price);
                    }
                    if (price_total_delivery_start < 1000000 && price_total_delivery_start >= 600000) {
                        var total_price = price_total_delivery - price_delivery + 50000;
                        $('.last_result_delivery_price').text(numFormat(total_price) + ' руб.');
                        $('.delivery_type').text("Стоимость доставки наложным платежем:");
                        $('.result_delivery').text(numFormat(50000) + ' руб.');
                        $('.last_result_delivery_price').attr('data-last_result_delivery_price', total_price);
                    }
                }
                if (value == 2) {
                    $(".form-group.city").addClass("hidden");
                    if (price_total_delivery_start < 600000 && price_total_delivery_start < 1000000) {
                        var total_price = price_total_delivery - price_delivery + 30000 ;
                        $('.last_result_delivery_price').text(numFormat(total_price) + ' руб.');
                        $('.delivery_type').text("Стоимость доставки курьером:");
                        $('.result_delivery').text(numFormat(30000) + ' руб.');
                        $('.last_result_delivery_price').attr('data-last_result_delivery_price', total_price);
                    }
                    if (price_total_delivery_start >= 600000 && price_total_delivery_start < 1000000) {
                        var total_price = price_total_delivery - price_delivery;
                        $('.last_result_delivery_price').text(numFormat(total_price) + ' руб.');
                        $('.delivery_type').text("Стоимость доставки курьером:");
                        $('.result_delivery').text("Бесплатно");
                        $('.last_result_delivery_price').attr('data-last_result_delivery_price', total_price);
                    }
                }
            } else {
                if (value == 3) {
                    $(".form-group.city.hidden").removeClass("hidden");
                    if (price_total_delivery >= 1000000) {
                        var total_price = price_total_delivery;
                        $('.last_result_delivery_price').text(numFormat(total_price) + ' руб.');
                        $('.delivery_type').text("Стоимость доставки наложным платежем:");
                        $('.result_delivery').text("Бесплатно");
                        $('.last_result_delivery_price').attr('data-last_result_delivery_price', total_price);
                    }else{
                        var total_price = price_total_delivery + 50000;
                        $('.last_result_delivery_price').text(numFormat(total_price) + ' руб.');
                        $('.delivery_type').text("Стоимость доставки наложным платежем:");
                        $('.result_delivery').text(numFormat(50000) + ' руб.');
                        $('.last_result_delivery_price').attr('data-last_result_delivery_price', total_price);
                    }
                }
                if (value == 2) {
                    $(".form-group.city").addClass("hidden");
                    if (price_total_delivery < 600000) {
                        var total_price = price_total_delivery + 30000;
                        $('.last_result_delivery_price').text(numFormat(total_price) + ' руб.');
                        $('.delivery_type').text("Стоимость доставки курьером:");
                        $('.result_delivery').text(numFormat(30000) + ' руб.');
                        $('.last_result_delivery_price').attr('data-last_result_delivery_price', total_price);
                    }
                    else{
                        var total_price = price_total_delivery;
                        $('.last_result_delivery_price').text(numFormat(total_price) + ' руб.');
                        $('.delivery_type').text("Стоимость доставки курьером:");
                        $('.result_delivery').text("Бесплатно");
                        $('.last_result_delivery_price').attr('data-last_result_delivery_price', total_price);
                    }
                }
            }
    });
    $(".not_order").click(function(e) {
        $.magnificPopup.close();
    });

    });
