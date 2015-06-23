<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

return array(
       'admin.static.index' => array(
		'title' => 'Постоянные страницы',
		'route' => 'admin-static'
	),
	'admin.dynamic.index' => array(
		'title' => 'Свободные страницы',
		'route' => 'admin-dynamic'
	),
       'admin.page.edit' => array(
		'title' => 'Редактирование старницы',
		'route' => 'admin-page:edit',
		'parent' => 'admin.dynamic.index'
	),
	'admin.page.create' => array(
		'title' => 'Добавление страницы',
		'route' => 'admin-page:create',
		'parent' => 'admin.dynamic.index'
	)
);