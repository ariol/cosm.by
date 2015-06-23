<div class="row reload">
    <!-- Shipping & Shipment Block Starts -->
    <div class="col-sm-12">
        <!-- Shipment Information Block Starts -->
        <div class="panel panel-smart">
            <div class="panel-heading">
                <h2 class="panel-title">
                    Статистика по поставщикам
                </h2>
            </div>
            <div id="selection"></div>
            <form class="form-horizontal" role="form">
                Список клиентов, заказавших на сумму:
                <div class="form-group">
                    <label for="inputFname" class="col-sm-2 control-label">Введите сумму :</label>
                    <div class="col-sm-2">
                        <input class="form-control" type="text" name="price_start"  placeholder="от" value="<?php echo $price_start?>"/>
                    </div>
                    <div class="col-sm-2">
                        <input class="form-control" type="text" name="price_final"  placeholder="до" value="<?php echo $price_final?>"/>
                    </div>
                    <div class="col-sm-3 send_date">
                        <a class="send_data" rel="nofollow" href="javascript:void(0)" onclick="$('form').submit();">
                            <button  type="submit" class="btn btn-black">
                                Показать
                            </button>
                        </a>
                    </div>
                    <div>
                        <label class="email"><input type="radio" name="group"  value="email" <?php if($group == 'email') {?> checked <?php } ?>/>Групирловка по email</label><br>
                        <label class="phone"><input type="radio" name="group" value="phone" <?php if($group == 'phone') {?> checked <?php } ?>/> Групирловка по телефону</label>
                    </div>
                </div>
                <div class="calculation_table">
                    <table class="table table-bordered table-striped table-condensed" id="tabledata_clients_sum">
                        <tbody class="main">
                        <tr>
                            <th>Имя клиента</th>
                            <th>телефон</th>
                            <th>email</th>
                            <th>Общая сумма заказов</th>
                        </tr>
                        <?php  foreach ($quantity_product_orders as $data) { ?>
                            <tr>
                                <td><?php echo $data['name']?></td>
                                <td><?php echo $data['phone']?></td>
                                <td><?php echo $data['email']?></td>
                                <td><?php echo number_format($data['summ_price'], 0, '', ' ');?></td>
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
    </div>
</div>
</div>
