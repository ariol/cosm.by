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
                    Статистика по клиентам
                </h2>
            </div>
            <div id="selection"></div>
            <form class="form-horizontal" role="form">
                <div class="form-group">
                    <label for="inputFname" class="col-sm-2 control-label">Введите артикул :</label>
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
                <div class="article_table ">
                    <div class="article_table <?php if(!$orders_array){?>hidden<?php } ?>">
                        <table class="table table-bordered table-striped table-condensed" id="tabledata_clients_article">
                            <tbody class="main">
                            <tr>
                                <th>Имя клиента</th>
                                <th>телефон</th>
                                <th>email</th>
                                <th>артикул</th>
                                <th>наименование товара</th>
                            </tr>
                            <?php foreach ($orders_array as $data) { ?>
                                <tr>
                                    <td><?php echo $data['name']?></td>
                                    <td><?php echo $data['phone']?></td>
                                    <td><?php echo $data['email']?></td>
                                    <td><?php echo $data['article']?></td>
                                    <td><?php echo $data['p_name']?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
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