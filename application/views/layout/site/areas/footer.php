<!-- Footer Section Starts -->
<footer id="footer-area">
    <!-- Footer Links Starts -->
    <div class="footer-links">
        <div class="row">
            <!-- Information Links Starts -->
            <div class="col-md-2 col-sm-6">
                <h5>Категории</h5>
                <ul>
                <?php $category = ORM::factory('Category')
                    ->where('active', '=', 1)
                    ->find_all();?>
                <?php foreach($category as $item) { ?>
                    <li><a href="/<?php echo $item->url; ?>"> <?php echo $item->name; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="col-md-3 col-sm-6">
                <h5>Информация</h5>
                <ul>
                    <li><a href="/page/publichnaya-oferta">Публичная оферта</a></li>
                    <li><a href="/page/dostavka-oplata"><span>Оплата и доставка</span></a></li>
                    <li><a href="/page/usloviya-obslujivaniya"><span>Условия обслуживания</span></a></li>
					<li><a href="/page/contacts"><span>Контакты</span></a></li>
                    <li><a href="/like"><span>Избранное</span></a></li>
                    <li><a href="/cart"><span>Корзина</span></a></li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 last">
                <h5>О магазине</h5>
                <ul>
                    <li><i class="icon-map-marker"></i></li>
                    <li>
                        <span class="glyphicon glyphicon-pushpin"></span> ИП Ганичева Карина Борисовна, зарегистрирована Минским районным исполнительным комитетом 04.02.2015 года, УНП 691783090. Юридический и почтовый адрес:223050, Минская обл., Минский р-н., аг.Колодищи, ул. Осенняя, дом 5. Интернет-магазин зарегистрирован в Торговом реестре: Рег.№205813 от 18.02.2015 года. Время, дни работы Интернет-магазина:ПН-ПТ 10:00-19:00. Заказы принимаются круглосуточно.
                    </li>
                    <li>
                        <span class="glyphicon glyphicon-envelope"></span>  E-mail: annalotanby@yandex.ru
                    </li>
                    <li>
                        <span class="glyphicon glyphicon-earphone"></span> Velcom: мтс: +37529-525-15-15; velcom: +37529-667-62-33</a>
                    </li>
                </ul>
            </div>
            <!-- Contact Us Ends -->
        </div>
    </div>
    <!-- Footer Links Ends -->
    <!-- Copyright Area Starts -->
    <div class="copyright clearfix">
        <!-- Starts -->
        <p class="pull-left">
            Интернет-магазин профессиональной косметики COSM.by
        </p>
    </div>
    <!-- Copyright Area Ends -->
</footer>
<!-- Footer Section Ends -->
</div>
<!-- Container Ends -->
<!-- JavaScript Files -->

</body>
</html>