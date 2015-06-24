
<div class="row reload">
    <!-- Shipping & Shipment Block Starts -->
    <div class="col-sm-12">
        <!-- Shipment Information Block Starts -->
        <div class="panel panel-smart">
            <div class="panel-heading">
                <h2 class="panel-title">
                    Расчет прибыли
                </h2>
            </div>
            <div id="selection"></div>
            <form class="form-horizontal" role="form">
                <div class="panel-smart panel_hidden">
                    <div class="input_product"></div>
                    <div class="form-group">
                        <label for="inputFname" class="col-sm-3 control-label">Расчет прибыли за период :</label>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" name="from"  placeholder="от" value="<?php echo $from?>"/>
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" name="to"  placeholder="до" value="<?php echo $to?>"/>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3 send_date">
                                <button name="send_date_limit"  type="submit" class="btn btn-black">
                                    Посчитать
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php if(!$provider) { ?>
                    <div class="calculation_table">
                        <table class="table table-bordered table-striped table-condensed" id="tabledata">
                            <tbody class="main">
                            <tr>
                                <th>Поставщик</th>
                                <th>Количество заказов</th>
                                <th>Иоговая сумма закупки</th>
                            </tr>
                            <?php foreach($result as $item) { ?>
                            <tr>
                                <td> <a href="<?php echo $_SERVER['REQUEST_URI']."&provider=".$item['prov_id']?>"><?php echo $item['name'];?></a></td>
                                <td><?php echo $item['quantity_prod'];?></td>
                                <td><?php echo  number_format($item['purchase_price_quantity'], 0, '', ' ');?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <?php } else { ?>
                    <?php foreach($result as $item) { ?>
                            <h2><?php echo $item['prov_name']?></h2>
                    <?php break; } ?>
                    <div class="calculation_table">
                        <table class="table table-bordered table-striped table-condensed" id="tabledata">
                            <tbody class="main">
                            <tr>
                                <th>Наименование товара</th>
                                <th>Количество</th>
                                <th>Иоговая сумма закупки</th>
                            </tr>
                            <?php foreach($result as $item) { ?>
                                <tr>
                                    <td> <a href="/ariol-admin/product/edit/<?php echo $item['product_id']?>"><?php echo $item['prod_name'];?></a></td>
                                    <td><?php echo $item['quantity_prod'];?></td>
                                    <td><?php echo  number_format($item['purchase_price_quantity'], 0, '', ' ');?></td>
                                </tr>
                            <?php } ?>
                        </table>
                            </div>
                        <?php } ?>
                </div>
            </form>
            <div class="row">
                <div class="paginator">
                    <?php echo $pagination; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('input[name="from"]').daterangepicker({
                format: 'YYYY-MM-DD',
                singleDatePicker: true,
                showDropdowns: true
            },
            function (start, end, label) {
                var years = moment().diff(start, 'years');
            });
    });
    $(function() {
        $('input[name="to"]').daterangepicker({
                format: 'YYYY-MM-DD',
                singleDatePicker: true,
                showDropdowns: true
            },
            function (start, end, label) {
                var years = moment().diff(start, 'years');
            });
    });
    $(document).ready(function() {
        $("button[name = 'send_date_limit']").on('change', function () {
            $('form').submit();
        });
        $('a .provider').on('click', function () {
            $('form').submit();
        });

    });
</script>