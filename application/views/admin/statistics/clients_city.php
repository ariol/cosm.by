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

                Список клиентов по городам
                <div class="form-group">
                    <label for="inputFname" class="col-sm-2 control-label">Введите гогрод :</label>
                    <div class="col-sm-2">
                        <input class="form-control" type="text" name="city"  placeholder="город" value="<?php echo $city?>" />
                    </div>
                    <div class="col-sm-3">
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
                <div class="clients_city_table">
                    <table class="table table-bordered table-striped table-condensed" id="tabledata_city">
                        <tbody class="main">
                        <tr>
                            <th>Имя клиента</th>
                            <th>телефон</th>
                            <th>email</th>
                            <th>Город</th>
                        </tr>
                        <?php foreach ($orders_array as $data) { ?>
                            <tr>
                                <td><?php echo $data['name']?></td>
                                <td><?php echo $data['phone']?></td>
                                <td><?php echo $data['email']?></td>
                                <?php if($data['city']) { ?><td><?php echo $data['city']?></td><?php }else { ?>
                                <td><?php echo $data['adress']?></td><?php } ?>
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