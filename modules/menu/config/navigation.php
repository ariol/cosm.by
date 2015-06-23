<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

return array(
       'admin.menu.index' => array(
		'title' => 'Меню',
		'route' => 'admin-menu'
	),
       'admin.menu.edit' => array(
		'title' => 'Редактирование меню',
		'route' => 'admin-menu:edit',
		'parent' => 'admin.menu.index'
	),
	'admin.menu.create' => array(
		'title' => 'Добавление пункта меню',
		'route' => 'admin-menu:create',
		'parent' => 'admin.menu.index'
	)
);