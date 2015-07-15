$(document).ready(function() {
    $( '.a_color' ).live('click', function(e) {
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

    $('button[data-type=use_code]').click(function(e) {
        e.preventDefault();
        var coupon = $('input[name="code_coupon"]').val();
        if (!coupon) {
            alert('Введите купон');
            return false;
        }
        $.ajax({
            url : "/cart/coupon",
            type : "POST",
            dataType : "json",
            data : {coupon: coupon},
            success : function(result) {
                if(!result.discount)
                    alert("Неверный номер купона");
                else {
                    alert("Купон на скидку в " + result.discount + "% принят");
                    $('.data_recount').attr('data-discount', result.discount);
                    $('.data_recount').attr('data-code_coupons', result.code);
                    $('.form-group.not_coupon').addClass('hidden');
                    $('.form-group.have_coupon.hidden').removeClass('hidden');
                    $('.used_coupon').text('Был исползован купон на скидку в '+ result.discount + '%' );
                }
                recount();
            }
        });
    });

    $('button[data-type=use_certificate]').click(function(e) {
        e.preventDefault();
        var certificate = $('input[name="code_certificate"]').val();
        if (!certificate) {
            alert('Введите номер сертификата');
            return false;
        }
        $.ajax({
            url : "/cart/certificate",
            type : "POST",
            dataType : "json",
            data : {certificate: certificate},
            success : function(result) {
                if(result.sum <= 0)
                    alert("Неверный номер сертификата");
                else {
                    alert("Сертификат на сумму в " + result.sum + "руб. принят");
                    $('.data_recount').attr('data-to_amount', result.to_amount);
                    $('.data_recount').attr('data-code_certificate', result.code);
                    $('.form-group.not_certificate').addClass('hidden');
                    $('.form-group.have_certificate.hidden').removeClass('hidden');
                    $('.used_certificate').text('Был исползован сертификат на сумму '+ result.sum + 'руб.' );
                }
                recount();
            }
        });
    });

    $( "a.remove" ).live( "click", function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        delete ignore_ids[ignore_ids.indexOf(id)];
        $.ajax({
            url : "/cart/delete",
            type : "POST",
            dataType : "json",
            data : {id : id},
            success: function (result) {
                $('tr[data-id="'+result.id +'"]').remove();
                var i = $('#tabledata tbody tr').length;
                if(i == 2){
                    $('.product_table').addClass('hidden');
                }
                recount();
            }
        });
    });
    $( "a.remove_certificate" ).live( "click", function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        delete ignore_ids_certificate[ignore_ids_certificate.indexOf(id)];
        $.ajax({
            url : "/certificate/delete",
            type : "POST",
            dataType : "json",
            data : {id : id},
            success: function (result) {
                $('tr[data-id="'+result.id +'"]').remove();
                console.log(recount_certificate());
                var i = $('#tabledata_certificate tbody tr').length;
                if(i == 3){
                    $('.certificate_table').addClass('last');
                }
                if(i == 2){
                    $('.certificate_table').addClass('hidden');
                }
                recount_certificate();
            }
        });
    });
    $( 'button[data-type="cancel_coupon"]' ).live( "click", function(e) {
        e.preventDefault();
        var code_coupon = $('#cancel_coupon').attr('data-code_coupons');
        $.ajax({
            url : "/ariol-admin/order/cancel",
            type : "POST",
            dataType : "json",
            data : {code_coupon : code_coupon},
            success: function (result) {
                alert("Купон отменен");
                $('.data_recount').attr('data-discount', '');
                $('.data_recount').attr('data-code_coupons', '');
                $('.form-group.not_coupon.hidden').removeClass('hidden');
                $('.form-group.have_coupon').addClass('hidden');
                recount();
            }
        });
    });
    $( 'button[data-type="cancel_certificate"]' ).live( "click", function(e) {
        e.preventDefault();
        var code_certificate = $('#cancel_certificate').attr('data-code_certificate');
        $.ajax({
            url : "/ariol-admin/order/cancel",
            type : "POST",
            dataType : "json",
            data : {code_certificate : code_certificate},
            success: function (result) {
                alert("Сертификат отменен");
                $('.data_recount').attr('data-to_amount', '');
                $('.data_recount').attr('data-code_certificate', '');
                $('.form-group.not_certificate.hidden').removeClass('hidden');
                $('.form-group.have_certificate').addClass('hidden');
                recount();
            }
        });
    });
    $('a[data-type="recount"]').click(function (e) {
        e.preventDefault();
        recount();
    });

    $('a[data-type="recount_certificate"]').click(function (e) {
        e.preventDefault();
        recount_certificate();
    });

    $('button[data-type="add_order"]').click(function(e) {
        e.preventDefault();
        var name = $('input[name="name"]').val();
        var email = $('input[name="email"]').val();
        var phone = $('input[name="phone"]').val();
        var adress = $('input[name="adress"]').val();
        var delivery = $('input[name="delivery"]:checked').val();
        var certificate = $('input[name="code_certificate"]').val();
        var coupon = $('input[name="code_coupon"]').val();
        var city = $('input[name="city"]').val();
        var index = $('input[name="index"]').val();
        var comment = $('textarea[name="comment"]').val();
        var admin_order = true;
        var error = 0;
        $.each($('tr .a_color'), function (index, value) {
            var id = $(this).attr('data-id');
            if($('a[data-id="'+id+'"]').hasClass('active') == false)
                error ++;
        });
        if(error > 0) {
            alert('Выберите цвет');
            return false;
        }
        $.ajax({
            url: "/cart/order",
            dataType: "json",
            type: "post",
            data: {
                name: name,
                email: email,
                phone: phone,
                adress: adress,
                delivery: delivery,
                coupon: coupon,
                certificate: certificate,
                admin_order: admin_order,
                comment: comment,
                city: city
            },
            success: function (result) {
                alert('Ваш заказ принят');
                window.location.href = "/ariol-admin/order/view/"+result.order_id
            }
        });
    });
    $('button[data-type="change_order"]').click(function (e) {
        e.preventDefault();
        var order_id = $('#tabledata').attr('data-order_id');
        var name = $('input[name="name"]').val();
        var email = $('input[name="email"]').val();
        var phone = $('input[name="phone"]').val();
        var adress = $('input[name="adress"]').val();
        var city = $('input[name="city"]').val();
        var index = $('input[name="index"]').val();
        var delivery = $('input[name="delivery"]:checked').val();
        var comment = $('textarea[name="comment"]').val();
        var certificate = $('#cancel_certificate').attr('data-code_certificate');
        var coupon = $('#cancel_coupon').attr('data-code_coupons');
        var error = 0;
        $.each($('tr .a_color'), function (index, value) {
            var id = $(this).attr('data-id');
            if ($('a[data-id="' + id + '"]').hasClass('active') == false)
                error++;
        });
        if (error > 0) {
            alert('Выберите цвет');
            return false;
        }
        $.ajax({
            url: "/ariol-admin/order/change_order",
            dataType: "json",
            type: "post",
            data: {
                name: name,
                email: email,
                phone: phone,
                adress: adress,
                delivery: delivery,
                coupon: coupon,
                certificate: certificate,
                order_id: order_id,
                city: city,
                index: index,
                comment: comment
            },
            success: function (result) {
                alert('Заказ изменен');
                window.location.href = "http://chocolate.local/ariol-admin/order/view/"+result.order_id;
            }
        });
    });
});
