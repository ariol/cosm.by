<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

$adminPrefix = Kohana::$config->load('extasy')->admin_path_prefix;

Route::set('admin-dynamic', trim($adminPrefix.'dynamic-page(/<action>(/<id>))'))
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'dynamic',
		'action'     => 'index',
	));

Route::set('admin-static', trim($adminPrefix.'static-page(/<action>(/<id>))'))
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'static',
		'action'     => 'index',
	));

Route::set('admin-page', trim($adminPrefix.'page(/<action>(/<id>))'))
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'page',
		'action'     => 'index',
	));
