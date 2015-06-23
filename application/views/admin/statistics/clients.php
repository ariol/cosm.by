<div class="row reload">
    <!-- Shipping & Shipment Block Starts -->
    <div class="col-sm-12">
        <!-- Shipment Information Block Starts -->
        <div class="panel panel-smart">
            <div class="panel-heading">
                <h2 class="panel-title">
                    Статистика по клиентам
                </h2>
            </div>
            <div id="selection"></div>
            <form class="form-horizontal" role="form">
                Клиенты по количеству заказов
                <div class="form-group">
                    <div class="col-sm-3 send_date">
                        <button name="show_clients"  type="submit" class="btn btn-black">
                            Показать
                        </button>
                        <button name="hide_clients"  type="submit" class="btn btn-black">
                            Скрыть
                        </button>
                    </div>
                </div>
                    <div class="clients_table hidden">
                        <table class="table table-bordered table-striped table-condensed" id="tabledata">
                            <tbody class="main">
                            <tr>
                                <th>Имя клиента</th>
                                <th>email</th>
                                <th>Количество заказов</th>
                            </tr>
                            <?php foreach($quantity_clients_orders as $items){?>
                            <tr>
                                <td><?php echo $items['name']?></td>
                                <td><?php echo $items['email']?></td>
                                <td><?php echo $items['quantity']?></td>
                            </tr>
                            <?php } ?>
                        </table>
                        <?php echo $pagination; ?>
                        <ul class="pagination hidden">
                            <li class="li_prev_page disabled"><a class="page prev_page" href="#" data-page="">&laquo;</a></li>
                            <li class="li_next_page"><a class="page next_page" href="#" data-page="">&raquo;</a></li>
                        </ul>

                    </div>
                Клиенты по общей сумме
                <div class="form-group">
                    <div class="col-sm-3 send_date">
                        <button name="show_clients_total_sum"  type="submit" class="btn btn-black">
                            Показать
                        </button>
                        <button name="hide_clients_total_sum"  type="submit" class="btn btn-black">
                            Скрыть
                        </button>
                    </div>
                </div>
                    <div class="clients_total_sum_table hidden">
                        <table class="table table-bordered table-striped table-condensed" id="tabledata">
                            <tbody class="main">
                            <tr>
                                <th>Имя клиента</th>
                                <th>email</th>
                                <th>телефон</th>
                                <th>Общая сумма заказов</th>
                            </tr>
                            <?php foreach($sum_orders as $items) {
                                if(!$items['summ_price']){
                                    $price = $items['cert_price'];
                                }else {
                                    $price = $items['summ_price'];
                                }
                                ?>
                            <tr>
                                <td><?php echo $items['name']?></td>
                                <td><?php echo $items['email']?></td>
                                <td><?php echo $items['phone']?></td>
                                <td><?php echo number_format($price, 0, '', ' '); ?> руб.</td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                Список клиентов по городам
                <div class="form-group">
                    <label for="inputFname" class="col-sm-2 control-label">Введите гогрод :</label>
                    <div class="col-sm-2">
                        <input class="form-control" type="text" name="city"  placeholder="город" value=""/>
                    </div>
                    <div class="col-sm-3">
                        <button name="show_clients_city"  type="submit" class="btn btn-black">
                            Показать
                        </button>
                        <button name="hide_clients_city"  type="submit" class="btn btn-black">
                            Скрыть
                        </button>
                    </div>
                </div>
                <div class="clients_city_table hidden">
                    <table class="table table-bordered table-striped table-condensed" id="tabledata_city">
                        <tbody class="main">
                        <tr>
                            <th>Имя клиента</th>
                            <th>email</th>
                            <th>телефон</th>
                            <th>Город</th>
                        </tr>
                    </table>
                </div>
                Список клиентов, заказавших на сумму:
                <div class="form-group">
                    <label for="inputFname" class="col-sm-2 control-label">Введите сумму :</label>
                    <div class="col-sm-2">
                        <input class="form-control" type="text" name="price_start"  placeholder="от" value=""/>
                    </div>
                    <div class="col-sm-2">
                        <input class="form-control" type="text" name="price_final"  placeholder="до" value=""/>
                    </div>
                    <div class="col-sm-3 send_date">
                        <button name="show_clients_sum"  type="submit" class="btn btn-black">
                            Показать
                        </button>
                        <button name="hide_clients_sum"  type="submit" class="btn btn-black">
                            Скрыть
                        </button>
                    </div>
                </div>
                <div class="calculation_table hidden">
                    <table class="table table-bordered table-striped table-condensed" id="tabledata_clients_sum">
                        <tbody class="main">
                        <tr>
                            <th>Имя клиента</th>
                            <th>телефон</th>
                            <th>email</th>
                            <th>Общая сумма заказов</th>
                        </tr>
                    </table>
                </div>
                Поиск клиентов, покупавших товар такой-то (артикул товара)
                <div class="form-group">
                    <label for="inputFname" class="col-sm-2 control-label">Введите артикул :</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="article"  placeholder="артикул" value=""/>
                    </div>
                    <div class="col-sm-3 send_date">
                        <button name="show_article_product"  type="submit" class="btn btn-black">
                            Показать
                        </button>
                        <button name="hide_article_product"  type="submit" class="btn btn-black">
                            Скрыть
                        </button>
                    </div>
                </div>
                <div class="article_table hidden">
                    <div class="article_table hidden">
                        <table class="table table-bordered table-striped table-condensed" id="tabledata_clients_article">
                            <tbody class="main">
                            <tr>
                                <th>Имя клиента</th>
                                <th>телефон</th>
                                <th>email</th>
                                <th>артикул</th>
                                <th>наименование товара</th>
                            </tr>
                        </table>
                    </div>
                </div>
                </div>
            </form>
            <!-- Form Ends -->
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('button[name="show_clients"]').click(function(e){
            e.preventDefault();
                $('.clients_table.hidden').removeClass('hidden');
        });
        $('button[name="hide_clients"]').click(function(e){
            e.preventDefault();
            $('.clients_table').addClass('hidden');
        });
        $('button[name="show_clients_total_sum"]').click(function(e){
            e.preventDefault();
            $('.clients_total_sum_table.hidden').removeClass('hidden');
        });
        $('button[name="hide_clients_total_sum"]').click(function(e){
            e.preventDefault();
            $('.clients_total_sum_table').addClass('hidden');
        });
        $('button[name="hide_clients_sum"]').click(function(e){
            e.preventDefault();
            $('.calculation_table').addClass('hidden');
        });
        $('button[name="hide_article_product"]').click(function(e){
            e.preventDefault();
            $('.article_table').addClass('hidden');
        });

            $('.send_date button[name="show_clients_sum"]').click(function(e) {
                e.preventDefault();
                var price_start = $('input[name="price_start"]').val();
                var price_final = $('input[name="price_final"]').val();
                $.ajax({
                    url: "/ariol-admin/statistics/clients_sum",
                    type:"POST",
                    dataType: "JSON",
                    data: {
                        price_start: price_start,
                        price_final: price_final
                    },
                    success: function(data){
                        if ($('button[name="show_clients_sum"]').hasClass('flag')==true){$('.flag_row').remove()}
                        for(var item in data.orders_array){
                            var value_item = data.orders_array[item];
                            var count = 1;
                            $('#tabledata_clients_sum').append("<tr class='flag_row'> <td class='name'></td> <td class='phone'></td> <td class='email'></td> <td class='price'></td></tr>");
                            for(var i in value_item){
                                var value = value_item[i];
                                $('#tabledata_clients_sum tr:last-child td:nth-child(' + count + ' )').html(value);
                                count++;
                            }
                        }
                        $('.calculation_table.hidden').removeClass('hidden');
                        $('button[name="show_clients_sum"]').addClass('flag');
                    }
                });
            });

        $('button[name="show_article_product"]').click(function(e){
            e.preventDefault();
            var article = $('input[name="article"]').val();
            $.ajax({
                url: "/ariol-admin/statistics/count_article",
                type: "POST",
                dataType: "JSON",
                data: {
                    article: article
                },
                success: function(data){
                    if ($('button[name="show_article_product"]').hasClass('flag')==true){$('.flag_row').remove()}
                    for(var item in data.orders_array){
                        var value_item = data.orders_array[item];
                        var count = 1;
                        $('#tabledata_clients_article').append("<tr class='flag_row'> <td class='name'></td> <td class='phone'></td> <td class='email'></td> <td></td><td></td></tr>");
                        for(var i in value_item){
                            var value = value_item[i];
                            $('#tabledata_clients_article tr:last-child td:nth-child(' + count + ' )').html(value);
                            count++;
                        }
                    }
                    $('.article_table.hidden').removeClass('hidden');
                    $('button[name="show_article_product"]').addClass('flag');
                }
            });
        });
    });
</script>