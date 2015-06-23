<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

$adminPrefix = Kohana::$config->load('extasy')->admin_path_prefix;

Route::set('admin-menu', $adminPrefix.'menu(/<action>(/<id>))')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'menu',
		'action'     => 'index',
	));

Route::set('site-menu', 'menu(/<action>)')
	->defaults(array(
		'directory' => 'site',
		'controller' => 'menu',
		'action' => 'top',
		'menu' => false
	));