<?php defined('SYSPATH') or die('No direct script access.');

Kohana::$config->attach(new Extasy_Config_Database(), TRUE);

Route::set('admin-index', trim(Kohana::$config->load('extasy.admin_path_prefix'), '/'))
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'index',
		'action'     => 'index',
	));

Route::set('admin-auth', Kohana::$config->load('extasy.admin_path_prefix').'auth/<action>(/<code>)')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'auth'
	));

Route::set('admin-ajax', Kohana::$config->load('extasy.admin_path_prefix').'ajax/<action>')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'ajax'
	));

Route::set('admin-user', Kohana::$config->load('extasy.admin_path_prefix').'user(/<action>(/<id>))')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'user',
		'action'     => 'index',
	));

Route::set('admin-config', Kohana::$config->load('extasy.admin_path_prefix').'config(/<action>)')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'config',
		'action'     => 'index',
	));

Route::set('error', '__error__/<code>')
	->defaults(array(
		'controller' => 'error',
		'action' => 'index'
	));

Route::set('deploy', 'deploy/<action>')
	->defaults(array(
		'controller' => 'deploy',
	));

Route::set('captcha', 'captcha')
	->defaults(array(
		'controller' => 'captcha',
		'action' => 'index'
	));

Route::set('admin-file-uploader', Kohana::$config->load('extasy.admin_path_prefix').'file-uploader')
	->defaults(array(
		'directory' => 'file',
		'controller' => 'uploader',
		'action' => 'index'
	));