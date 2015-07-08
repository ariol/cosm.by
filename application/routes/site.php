<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 * @author o.zgolich
 */

Route::set('site-index', '')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'index',
		'action' => 'index',
		'menu' => true,
		'name' => 'Главная'
	));
	
Route::set('site-redirect', 'catalog/<url>', array('url' => '.*'))
	->defaults(array(
		'directory' => 'site',
		'controller' => 'index',
		'action' => 'redirect'
	));

Route::set('site-contacts', 'page/contacts')
	->defaults(array(
		'directory' => 'site/contacts',
		'controller' => 'page',
		'action' => 'contacts',
	));

Route::set('site-page', 'page/<url>')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'page',
		'action'     => 'index',
	));
	
Route::set('site-price', 'price')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'price',
		'action'     => 'index',
	));
	
Route::set('site-sale', 'sale(/<page>)', array('page' => '\d+'))
	->defaults(array(
		'directory' => 'site',
		'controller' => 'sale',
		'action' => 'index',
	));
	
Route::set('site-search', 'search(/<action>(/<page>))', array('page' => '\d+'))
	->defaults(array(
		'directory' => 'site',
		'controller' => 'search',
		'action' => 'index',
	));
	
Route::set('site-news', 'news')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'news',
		'action' => 'index',
	));	

Route::set('site-news-item', 'news/<url>')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'news',
		'action' => 'item',
	));

Route::set('site-discount', 'discount')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'discount',
		'action' => 'index',
	));	
Route::set('site-discount-item', 'discount/<url>')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'discount',
		'action' => 'item',
	));
	
Route::set('site-reviews', 'reviews(/<action>)')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'reviews',
		'action' => 'index',
	));

Route::set('site-cart', 'cart(/<action>)')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'cart',
		'action' => 'index',
	));

Route::set('site-like', 'like(/<action>)')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'like',
		'action' => 'index',
	));

Route::set('site-certificate-product', 'certificate_product/<url>')
    ->defaults(array(
        'directory' => 'site',
        'controller' => 'certificate',
        'action' => 'product',
    ));

Route::set('site-certificate', 'certificate(/<action>)')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'certificate',
		'action' => 'index',
	));
	
Route::set('site-brand', 'brand/<url>(/<page>)', array('page' => '\d+'))
	->defaults(array(
		'directory' => 'site',
		'controller' => 'brand',
		'action' => 'index',
	));

Route::set('site-line', 'brand/<brand>/<line>(/<page>)', array('page' => '\d+'))
	->defaults(array(
		'directory' => 'site',
		'controller' => 'line',
		'action' => 'index',
	));

Route::set('site-category', '<category>(/<page>)', array('page' => '\d+'))
    ->defaults(array(
        'directory' => 'site',
        'controller' => 'category',
        'action' => 'index',
    ));

Route::set('site-product', '<category>/<product>', array('category' => '.*','product' => '.*'))
    ->defaults(array(
        'directory' => 'site',
        'controller' => 'product',
        'action' => 'index',
    ));

	
Route::set('site-category-brand', '<category>/<brand>(/<page>)', array('page' => '\d+'))
	->defaults(array(
		'directory' => 'site',
		'controller' => 'category',
		'action' => 'brand',
	));

Route::set('site-basket', $_SERVER['REQUEST_URI'])
	->defaults(array(
		'directory' => 'site',
		'controller' => 'cart',
		'action' => 'basket',
	));

