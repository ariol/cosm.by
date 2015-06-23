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
                    <label for="inputFname" class="col-sm-2 control-label">Не менее :</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="from" placeholder="не менее"  value="<?php echo $from?>"  id="autocomplete"/>
                    </div>
                    <div class="col-sm-3 send_date">
                        <a rel="nofollow" href="javascript:void(0)" onclick="$('form').submit();">
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
                <div class="clients_table">
                    <table class="table table-bordered table-striped table-condensed" id="tabledata">
                        <tbody class="main">
                        <tr>
                            <th>Имя клиента</th>
                            <th>email</th>
                            <th>Телефон</th>
                            <th>Количество заказов</th>
                        </tr>
                        <?php foreach($orders_array as $items){?>
                            <tr>
                                <td><?php echo $items['name']?></td>
                                <td><?php echo $items['email']?></td>
                                <td><?php echo $items['phone']?></td>
                                <td><?php echo $items['quantity']?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
        </div>
        </form>
        <!-- Form Ends -->
        <div class="row">
            <div class="paginator">
                <?php echo $pagination; ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.email input').on('change', function () {
            $('form').submit();
        });
        $('.phone input').on('change', function () {
            $('form').submit();
        });
    })
</script>