<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 * @author o.zgolich
 */

$adminPrefix = Kohana::$config->load('extasy')->admin_path_prefix;

Route::set('admin-filter', $adminPrefix.'category/<parent>/filter(/<action>(/<id>))')
        ->defaults(array(
		'directory' => 'admin',
		'controller' => 'filter',
		'action' => 'index'
	));

Route::set('admin-orders', $adminPrefix . 'order(/<action>(/<id>))')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'order',
		'action' => 'index'
	));
	
Route::set('admin-promo', $adminPrefix . 'promo(/<action>(/<id>))')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'promo',
		'action' => 'index'
	));

Route::set('admin-slider', $adminPrefix . 'slider(/<action>(/<id>))')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'slider',
		'action' => 'index'
	));
	
Route::set('admin-category', $adminPrefix . 'category(/<action>(/<id>))')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'category',
		'action' => 'index'
	));
	
Route::set('admin-brand', $adminPrefix . 'brand(/<action>(/<id>))')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'brand',
		'action' => 'index'
	));
Route::set('admin-line', $adminPrefix . 'line(/<action>(/<id>))')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'line',
		'action' => 'index'
	));

Route::set('admin-news', $adminPrefix . 'news(/<action>(/<id>))')
->defaults(array(
	'directory' => 'admin',
	'controller' => 'news',
	'action' => 'index'
));

Route::set('admin-discount', $adminPrefix . 'discount(/<action>(/<id>))')
->defaults(array(
	'directory' => 'admin',
	'controller' => 'discount',
	'action' => 'index'
));

Route::set('admin-product', $adminPrefix . 'product(/<action>(/<id>))')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'product',
		'action' => 'index'
	));
	
Route::set('admin-reviews', $adminPrefix . 'reviews(/<action>(/<id>))')
->defaults(array(
	'directory' => 'admin',
	'controller' => 'reviews',
	'action' => 'index'
));

Route::set('admin-coupons', $adminPrefix . 'coupons(/<action>(/<id>))')
->defaults(array(
	'directory' => 'admin',
	'controller' => 'coupons',
	'action' => 'index'
));
Route::set('admin-certificate', $adminPrefix . 'certificate(/<action>(/<id>))')
->defaults(array(
	'directory' => 'admin',
	'controller' => 'certificate',
	'action' => 'index'
));
Route::set('admin-statistics-clients_quantity_order', $adminPrefix . 'statistics/clients_quantity_order')
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'statistics',
        'action' => 'clients_quantity_order'
    ));
Route::set('admin-statistics-calculation', $adminPrefix . 'statistics/calculation')
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'statistics',
        'action' => 'calculation'
    ));

Route::set('admin-statistics-total-summ', $adminPrefix . 'statistics/clients_total_summ')
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'statistics',
        'action' => 'clients_total_summ'
    ));
Route::set('admin-statistics-order', $adminPrefix . 'statistics/order')
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'statistics',
        'action' => 'order'
    ));
Route::set('admin-statistics-product', $adminPrefix . 'statistics/product')
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'statistics',
        'action' => 'product'
    ));
Route::set('admin-statistics-clients-summ', $adminPrefix . 'statistics/clients_summ')
->defaults(array(
	'directory' => 'admin',
	'controller' => 'statistics',
    'action' => 'clients_summ'
));
Route::set('admin-statistics-article', $adminPrefix . 'statistics/clients_article')
->defaults(array(
	'directory' => 'admin',
	'controller' => 'statistics',
    'action' => 'clients_article'
));
Route::set('admin-statistics-city', $adminPrefix . 'statistics/clients_city')
->defaults(array(
	'directory' => 'admin',
	'controller' => 'statistics',
    'action' => 'clients_city'
));
Route::set('admin-statistics-provider_calculation', $adminPrefix . 'statistics/provider_calculation')
->defaults(array(
	'directory' => 'admin',
	'controller' => 'statistics',
    'action' => 'provider_calculation'
));
Route::set('admin-statistics-autocomplit-article', $adminPrefix . 'statistics/autocomplete_article')
->defaults(array(
	'directory' => 'admin',
	'controller' => 'statistics',
    'action' => 'autocomplete_article'
));

Route::set('admin-statistics', $adminPrefix . 'statistics(/<action>(/<id>))')
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'statistics'
    ));