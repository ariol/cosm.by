<div class="row reload">
    <!-- Shipping & Shipment Block Starts -->
    <div class="col-sm-12">
        <!-- Shipment Information Block Starts -->
        <div class="panel panel-smart">
            <div class="panel-heading">
                <h2 class="panel-title">
                    Статистика по товарам
                </h2>
            </div>
            <div id="selection"></div>
            <form class="form-horizontal" role="form">
                Товары – по количеству заказов по убыванию
                <div class="form-group">
                    <label for="inputFname" class="col-sm-2 control-label">Поиск товара по артикул :</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="article" placeholder="введите артикул"  value="<?php echo $article?>"  id="autocomplete"/>
                    </div>
                    <div class="col-sm-3 send_date">
                        <a rel="nofollow" href="javascript:void(0)" onclick="$('form').submit();">
                            <button  type="submit" class="btn btn-black">
                                Показать
                            </button>
                        </a>
                    </div>
                </div>
                <div class="product_table">
                    <table class="table table-bordered table-striped table-condensed" id="tabledata">
                        <tbody class="main">
                        <tr>
                            <th>Наименование товара</th>
                            <th>Количество заказов</th>
                        </tr>
                        <?php foreach($quantity_product_orders as $item) { ?>
                            <tr>
                                <td><a class="name" href="/ariol-admin/product/edit/<?php echo $item['id']?>"><?php echo $item['name']?></a></td>
                                <td><?php echo $item['quantity_prod']?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
        </form>
            <div class="row">
                <div class="paginator">
                    <?php echo $pagination; ?>
                </div>
            </div>
        <!-- Form Ends -->
    </div>
</div>
</div>
<script>
    $('#autocomplete').autocomplete({
        source: function(request, response){
            $.ajax({
                url : "/ariol-admin/statistics/autocomplete_article",
                type : "POST",
                dataType : "json",
                data:{
                    maxRows: 12, // показать первые 12 результатов
                    query: request.term // поисковая фраза
                },
                success: function(data){
                    response($.map(data, function(item){
                        return {
                            plink: item.product_id,
                            label: item.article
                        }
                    }));
                }
            });
        },
        select: function( event, ui ) {
            event.preventDefault();
            $('input[name="article"]').val(ui.item.label);
            return false;
        },
        minLength: 2
    });
    </script>