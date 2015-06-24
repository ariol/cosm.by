<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

return array
(
	Application::NAME => array
	(
		'<i class="fa fa-shopping-cart"></i>|Магазин' => array(
			'<i class="fa fa-file"></i>|Товары' => 'admin-product',
			'<i class="fa fa-tasks"></i>|Категории' => 'admin-category',
			'<i class="fa fa-tasks"></i>|Линии' => 'admin-line',
			'<i class="fa fa-tasks"></i>|Бренды' => 'admin-brand',
			'<i class="fa fa-comments-o"></i>|Отзывы' => 'admin-reviews',
			'<i class="fa fa-file"></i>|Заказы' => 'admin-orders',
            '<i class="fa fa-file"></i>|Купоны' => 'admin-coupons',
            '<i class="fa fa-file"></i>|Сертификаты' => 'admin-certificate',
            '<i class="fa fa-file"></i>|Поставщики' => 'admin-provider',
		),
		'<i class="fa fa-shopping-cart"></i>|Статистика' => array(
			'<i class="fa fa-tasks"></i>|Товары' => 'admin-statistics:product',
			'<i class="fa fa-tasks"></i>|Расчет прибыли' => 'admin-statistics:calculation',
			'<i class="fa fa-tasks"></i>|Заказы по сумме' => 'admin-statistics:order',
            '<i class="fa fa-tasks"></i>|Клиенты по сумме' => 'admin-statistics:clients_summ',
            '<i class="fa fa-tasks"></i>|По кол-ву заказов' => 'admin-statistics:clients_quantity_order',
            '<i class="fa fa-tasks"></i>|По товару' => 'admin-statistics:clients_article',
            '<i class="fa fa-tasks"></i>|По городам' => 'admin-statistics:clients_city',
            '<i class="fa fa-tasks"></i>|По поставщикам' => 'admin-statistics:provider_calculation',
		),
		'<i class="fa fa-key"></i>|Слайды' => 'admin-slider',
		'<i class="fa fa-key"></i>|Баннеры' => 'admin-promo',
		'<i class="fa fa-key"></i>|Войти' => 'admin-auth:login',
		'<i class="fa fa-key"></i>|Сбросить пароль' => 'admin-auth:reset_password_step_1',
		'<i class="fa fa-gears"></i>|Служебное' => array(
			'<i class="fa fa-gear"></i>|Настройки' => 'admin-config',
			'<i class="fa fa-user"></i>|Пользователи' => 'admin-user',
		),
	)
);