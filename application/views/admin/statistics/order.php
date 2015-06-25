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
                            <input class="form-control" type="text" name="price_start"  placeholder="от" value="<?php echo $price_start?>"/>
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" name="price_final"  placeholder="до" value="<?php echo $price_final?>"/>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3 send_date">
                                <a rel="nofollow" href="javascript:void(0)" onclick="$('form').submit();">
                                <button type="submit" class="btn btn-black">
                                    Показать
                                </button>
                                    </a>
                            </div>
                        </div>
                    </div>
                    <div class="calculation_table">
                        <table class="table table-bordered table-striped table-condensed <?php if(!$result){?> hidden<?php } ?>" id="tabledata">
                            <tbody class="main">
                            <tr>
                                <th>Имя</th>
                                <th>Телефон</th>
                                <th>Дата поступления</th>
                                <th>Сумма заказа</th>
                                <th>Детали заказа</th>
                                <th>Статус</th>
                            </tr>
                            <?php foreach($result as $item) { ?>
                                <tr>
                                    <td><?php echo $item['name']?></td>
                                    <td><?php echo $item['phone']?></td>
                                    <td><?php echo $item['created_at']?></td>
                                    <td><?php echo number_format($item['price'], 0, '', ' ');  ?></td>
                                    <td><a href="/ariol-admin/order/view/<?php echo $item['id']?>">Детали заказа<span class="glyphicon glyphicon-link"></span></a></td>
                                    <td><?php echo $item['status'] ?></td>
                                </tr>
                            <?php } ?>
                        </table>
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
</div>
