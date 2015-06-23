<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
return array(
	'driver' => 'extasy',
	'hash_method'  => 'sha256',
	'hash_key'     => 'ergEr4d124Nwqefii$123x',
	'roles' => array(
		'admin'   => 1,
		'user'   => 2,
	),
	'roles_labels' => array(
		'admin' => 'Администратор',
		'user' => 'Пользователь'
	),
	'form_plugins' => array(
		'admin' => 'Form_Plugin_Auth_Admin',
		'user' => 'Form_Plugin_Auth_Admin',
		'retailer' => 'Form_Plugin_Auth_Admin'
	)
);