<div class="row reload">
    <!-- Shipping & Shipment Block Starts -->
    <div class="col-sm-12">
        <!-- Shipment Information Block Starts -->
        <div class="panel panel-smart">
            <div class="panel-heading">
                <h2 class="panel-title">
                    Статистка по заказам
                </h2>
            </div>
            <div id="selection"></div>
            <form class="form-horizontal" role="form">
                <div class="panel-smart panel_hidden">
                    <div class="input_product"></div>
                    <div class="form-group">
                        <label for="inputFname" class="col-sm-3 control-label">Заказы в ценовом диапазоне:</label>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" name="price_start"  placeholder="от" value=""/>
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" name="price_final"  placeholder="до" value=""/>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3 send_date">
                                <button name="send_date_limit"  type="submit" class="btn btn-black">
                                    Показать
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="calculation_table hidden">
                        <table class="table table-bordered table-striped table-condensed" id="tabledata">
                            <tbody class="main">
                            <tr>
                                <th>Имя</th>
                                <th>Телефон</th>
                                <th>Дата поступления</th>
                                <th>Сумма заказа</th>
                                <th>Детали заказа</th>
                                <th>Статус</th>
                            </tr>
                        </table>
                        <ul class="pagination hidden">
                            <li class="li_prev_page disabled"><a class="page prev_page" href="#" data-page="">&laquo;</a></li>
                            <li class="li_next_page"><a class="page next_page" href="#" data-page="">&raquo;</a></li>
                        </ul>

                    </div>
                </div>
            </form>
            <!-- Form Ends -->
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.send_date button[name="send_date_limit"]').click(function(e) {
            e.preventDefault();
            var price_start = $('input[name="price_start"]').val();
            var price_final = $('input[name="price_final"]').val();
            $.ajax({
                url: "/ariol-admin/statistics/range_orders",
                type:"POST",
                dataType: "JSON",
                data: {
                    price_start: price_start,
                    price_final: price_final
                },
                success: function(data){
                    if ($('.send_date button[name="send_date_limit"]').hasClass('flag')==true){$('.flag_row').remove()}
                    if(data.total_page == null) {
                        $('.pagination').addClass('hidden');
                        for (var item in data.orders_array) {
                            var value_item = data.orders_array[item];
                            var count = 1;
                            $('#tabledata').append("<tr class='flag_row'> " +
                                                        "<td class='name'></td> " +
                                                        "<td class='phone'></td> " +
                                                        "<td class='date'></td> " +
                                                        "<td class='price'></td> " +
                                                        "<td></td> " +
                                                        "<td class='status'></td> " +
                                                    "</tr>");
                            for (var i in value_item) {
                                var value = value_item[i];
                                $('#tabledata tr:last-child td:nth-child(' + count + ' )').html(value);
                                count++;
                            }
                        }
                   }
                    if(data.total_page){
                        if ($('.send_date button[name="send_date_limit"]').hasClass('flag')==true){$('.flag_row').remove()}
                        $('.pagination.hidden').removeClass('hidden');
                        for (var item in data.orders_array) {
                            var value_item = data.orders_array[item];
                            var count = 1;
                            $('#tabledata').append("<tr class='flag_row'> " +
                                                        "<td class='name'></td> " +
                                                        "<td class='phone'></td> " +
                                                        "<td class='date'></td> " +
                                                        "<td class='price'></td> " +
                                                        "<td></td> " +
                                                        "<td class='status'></td> " +
                                                    "</tr>");
                            for (var i in value_item) {
                                var value = value_item[i];
                                $('#tabledata tr:last-child td:nth-child(' + count + ' )').html(value);
                                count++;
                            }
                        }
                        for(i = 1; i <= data.total_page; i++) {
                            if(i > 5) break;
                            $('.pagination li:nth-child('+i+')').after("<li class='flag_row page_"+i+"''><a class='page' data-page='"+ i + "' href='#' >" + i + "</a></li>")
                        }
                    }
                    $('.calculation_table.hidden').removeClass('hidden');
                    $('button[name="send_date_limit"]').addClass('flag');
                    $('li.page_1').addClass('active');
                    $('.next_page').attr('data-page', data.next_page)
                }
            });
        });
        $('a.page').live('click', function(e) {
            e.preventDefault();
            var price_start = $('input[name="price_start"]').val();
            var price_final = $('input[name="price_final"]').val();
            var page = $(this).attr('data-page');
            $.ajax({
                url: "/ariol-admin/statistics/range_orders",
                type:"POST",
                dataType: "JSON",
                data: {
                    price_start: price_start,
                    price_final: price_final,
                    page: page
                },
                success: function(data) {
                    if (data.total_page) {
                        if ($('.send_date button[name="send_date_limit"]').hasClass('flag') == true) {
                            $('.flag_row').remove()
                        }
                        $('.pagination.hidden').removeClass('hidden');
                        for (var item in data.orders_array) {
                            var value_item = data.orders_array[item];
                            var count = 1;
                            $('#tabledata').append("<tr class='flag_row'> " +
                                                        "<td class='name'></td> " +
                                                        "<td class='phone'></td> " +
                                                        "<td class='date'></td> " +
                                                        "<td class='price'></td> " +
                                                        "<td></td>" +
                                                        "<td class='status'></td> " +
                                                    "</tr>");
                            for (var i in value_item) {
                                var value = value_item[i];
                                $('#tabledata tr:last-child td:nth-child(' + count + ' )').html(value);
                                count++;
                            }
                        }
                        for (i = 1; i <= data.total_page; i++) {
                            $('.pagination li:nth-child(' + i + ')')
                                .after("<li class='flag_row page_"+i+"''>" +
                                            "<a class='page' data-page=" + i + " href='#" + i + "'>" + i + "" +
                                            "</a><" +
                                        "/li>")
                        }
                            for(i = 1; i <= data.total_page; i++){
                                if(Math.abs(data.page - i) > 3 )
                                $('a.page[data-page='+ i +']').addClass('hidden');
                            }
                        $('a.next_page').removeClass('hidden');
                        $('a.prev_page').removeClass('hidden');
                    }
                    $('.calculation_table.hidden').removeClass('hidden');
                    $('button[name="send_date_limit"]').addClass('flag');
                    if(data.next_page > data.total_page)
                        $('.li_next_page').addClass('disabled');
                    else {
                        $('.li_next_page').removeClass('disabled');
                        $('.next_page').attr('data-page', data.next_page);
                    }
                    if(data.prev_page == 0)
                        $('.li_prev_page').addClass('disabled');
                    else {
                        $('.li_prev_page').removeClass('disabled');
                        $('.prev_page').attr('data-page', data.prev_page);
                    }
                    $('li.page_'+data.page).addClass('active');
                }
                })
            });
    });
</script>