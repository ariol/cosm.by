
<div class="row reload">
    <!-- Shipping & Shipment Block Starts -->
    <div class="col-sm-12">
        <!-- Shipment Information Block Starts -->
        <div class="panel panel-smart">
            <div id="selection"></div>
            <form class="form-horizontal" role="form">
                <div class="panel-smart panel_hidden">
                    <div class="input_product"></div>
                    <div class="form-group">
                        <label for="inputFname" class="col-sm-3 control-label">Расчет прибыли за период :</label>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" name="from"  placeholder="от" value=""/>
                        </div>
                        <div class="col-sm-2">
                           <input class="form-control" type="text" name="to"  placeholder="до" value=""/>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3 send_date">
                                <button name="send_date_limit"  type="submit" class="btn btn-black">
                                    Посчитать
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="calculation_table hidden">
                        <table class="table table-bordered table-striped table-condensed" id="tabledata" data-order_id="<?php echo $order->id?>">
                            <tbody class="main">
                            <tr>
                                <th>Количество заказов</th>
                                <th>Иоговая сумма заказов</th>
                            </tr>
                                <tr>
                                    <td class="quantity"></td>
                                    <td class="result_price"></td>
                                </tr>
                        </table>
                    </div>
                </div>
            </form>
            <!-- Form Ends -->
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
        $('.send_date button[name="send_date_limit"]').click(function(e) {
            e.preventDefault();
            var from = $('input[name="from"]').val();
            var to = $('input[name="to"]').val();
            $.ajax({
               url: "/ariol-admin/statistics/revenue_for_period",
                type:"POST",
                dataType: "JSON",
                data: {
                    from: from,
                    to: to
                },
                success: function(data){
                    $('.calculation_table.hidden').removeClass('hidden');
                    $('.quantity').text(data.quantity);
                    $('.result_price').text(data.result_price);
                }
            });
        });
    });
</script>