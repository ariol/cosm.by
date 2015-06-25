<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

return array(
	'admin.promo.index' => array(
		'title' => 'Список баннеров',
		'route' => 'admin-promo'
	),
	'admin.promo.edit' => array(
		'title' => 'Редактирование баннера',
		'route' => 'admin-promo:edit',
		'parent' => 'admin.promo.index'
	),
	'admin.promo.create' => array(
		'title' => 'Добавление баннера',
		'route' => 'admin-promo:create',
		'parent' => 'admin.promo.index'
	),
	'admin.slider.index' => array(
		'title' => 'Список слайдов',
		'route' => 'admin-slider'
	),
	'admin.slider.edit' => array(
		'title' => 'Редактирование слайда',
		'route' => 'admin-slider:edit',
		'parent' => 'admin.slider.index'
	),
	'admin.slider.create' => array(
		'title' => 'Добавление слайда',
		'route' => 'admin-slider:create',
		'parent' => 'admin.slider.index'
	),
	'admin.category.index' => array(
		'title' => 'Список категорий',
		'route' => 'admin-category'
	),
	'admin.order.index' => array(
		'title' => 'Список заказов',
		'route' => 'admin-orders'
	),
	'admin.order.view' => array(
		'title' => 'Заказ',
		'route' => 'admin-orders:view',
		'parent' => 'admin.orders.index'
	),
	'admin.order.edit' => array(
		'title' => 'Редактирование заказа',
		'route' => 'admin-orders:edit',
		'parent' => 'admin.orders.index'
	),
	'admin.category.edit' => array(
		'title' => 'Редактирование категории',
		'route' => 'admin-category:edit',
		'parent' => 'admin.category.index'
	),
	'admin.category.create' => array(
		'title' => 'Добавление категории',
		'route' => 'admin-category:create',
		'parent' => 'admin.category.index'
	),
	'admin.brand.index' => array(
		'title' => 'Список брендов',
		'route' => 'admin-brand'
	),
	'admin.brand.edit' => array(
		'title' => 'Редактирование бренда',
		'route' => 'admin-brand:edit',
		'parent' => 'admin.brand.index'
	),
	'admin.brand.create' => array(
		'title' => 'Добавление бренда',
		'route' => 'admin-brand:create',
		'parent' => 'admin.brand.index'
	),
    'admin.line.index' => array(
        'title' => 'Список линий',
        'route' => 'admin-line'
    ),
    'admin.coupons.index' => array(
        'title' => 'Список купонов',
        'route' => 'admin-coupons'
    ),
    'admin.coupons.edit' => array(
        'title' => 'Редактирование купона',
        'route' => 'admin-coupons:edit',
        'parent' => 'admin.coupons.index'
    ),
    'admin.provider.index' => array(
        'title' => 'Список поставщиков',
        'route' => 'admin-provider'
    ),
    'admin.provider.create' => array(
        'title' => 'Добавить поставщика',
        'route' => 'admin-provider:create',
        'parent' => 'admin.provider.index'
    ),
    'admin.coupons.create' => array(
        'title' => 'Добавление купона',
        'route' => 'admin-coupons:create',
        'parent' => 'admin.coupons.index'
    ),
    'admin.certificate.index' => array(
        'title' => 'Список сертификатов',
        'route' => 'admin-certificate'
    ),
    'admin.certificate.edit' => array(
        'title' => 'Редактирование сертификата',
        'route' => 'admin-certificate:edit',
        'parent' => 'admin.certificate.index'
    ),
    'admin.certificate.create' => array(
        'title' => 'Добавление сертификата',
        'route' => 'admin-certificate:create',
        'parent' => 'admin.certificate.index'
    ),
	'admin.parametr.index' => array(
		'title' => 'Список параметров',
		'route' => 'admin-parametr'
	),
	'admin.parametr.edit' => array(
		'title' => 'Редактирование параметра',
		'route' => 'admin-parametr:edit',
		'parent' => 'admin.parametr.index'
	),
	'admin.parametr.create' => array(
		'title' => 'Добавление параметра',
		'route' => 'admin-parametr:create',
		'parent' => 'admin.parametr.index'
	),
	'admin.section.index' => array(
		'title' => 'Список разделов',
		'route' => 'admin-section'
	),
	'admin.section.edit' => array(
		'title' => 'Редактирование раздела',
		'route' => 'admin-section:edit',
		'parent' => 'admin.section.index'
	),
	'admin.section.create' => array(
		'title' => 'Добавление раздела',
		'route' => 'admin-section:create',
		'parent' => 'admin.section.index'
	),
	'admin.product.index' => array(
		'title' => 'Список товаров',
		'route' => 'admin-product'
	),
	'admin.product.edit' => array(
		'title' => 'Редактирование товара',
		'route' => 'admin-product:edit',
		'parent' => 'admin.product.index'
	),
    'admin.product.create' => array(
        'title' => 'Добавление товара',
        'route' => 'admin-product:create',
        'parent' => 'admin.product.index'
    ),
	'admin.news.index' => array(
		'title' => 'Список новостей',
		'route' => 'admin-news'
	),
	'admin.news.edit' => array(
		'title' => 'Редактирование новости',
		'route' => 'admin-news:edit',
		'parent' => 'admin.news.index'
	),
	'admin.news.create' => array(
		'title' => 'Добавление новости',
		'route' => 'admin-news:create',
		'parent' => 'admin.news.index'
	),
	'admin.discount.index' => array(
		'title' => 'Список акций',
		'route' => 'admin-discount'
	),
	'admin.discount.edit' => array(
		'title' => 'Редактирование акции',
		'route' => 'admin-discount:edit',
		'parent' => 'admin.discount.index'
	),
	'admin.discount.create' => array(
		'title' => 'Добавление акции',
		'route' => 'admin-discount:create',
		'parent' => 'admin.discount.index'
	),
	'admin.statistics.product' => array(
		'title' => 'Статисика по товарам',
		'route' => 'admin-statistics:product',
		'parent' => 'admin.statistics.index'
	),
	'admin.statistics.calculation' => array(
		'title' => 'Расчет прибыли за переиод',
		'route' => 'admin-statistics:calculation',
		'parent' => 'admin.statistics.index'
	),
	'admin.statistics.order' => array(
		'title' => 'Заказы в ценовом диапазоне',
		'route' => 'admin-statistics:order',
		'parent' => 'admin.statistics.index'
	),
	'admin.reviews.index' => array(
		'title' => 'Отзывы',
		'route' => 'admin-reviews',
	),
	'admin.statistics.clients_summ' => array(
		'title' => 'Клиенты по сумме ',
		'route' => 'admin-statistics:clients_summ',
		'parent' => 'admin.statistics.index'
	),
	'admin.statistics.clients_quantity_order' => array(
		'title' => 'Клиенты по количеству заказов ',
		'route' => 'admin-statistics:clients_quantity_order',
		'parent' => 'admin.statistics.index'
	),
	'admin.statistics.clients_article' => array(
		'title' => 'Клиенты, покупавшие товар (артикул) ',
		'route' => 'admin-statistics:clients_article',
		'parent' => 'admin.statistics.index'
	),
	'admin.statistics.clients_city' => array(
		'title' => 'Список клиентов по городам ',
		'route' => 'admin-statistics:clients_city',
		'parent' => 'admin.statistics.index'
	),
	'admin.statistics.provider_calculation' => array(
		'title' => 'Взаиморасчет по поставщикам ',
		'route' => 'admin-statistics:provider_calculation',
		'parent' => 'admin.statistics.index'
	)
);