<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

return array(
	'admin.config.index' => array(
		'title' => 'Настройки',
		'route' => 'admin-config'
	),
	'admin.auth.login' => array(
		'title' => 'Вход',
		'route' => 'admin-auth:login'
	),
	'admin.auth.change_password' => array(
		'title' => 'Изменить пароль',
		'route' => 'admin-auth:change_password'
	),
	'admin.auth.reset_password_step_1' => array(
		'title' => 'Сбросить пароль',
		'route' => 'admin-auth:reset_password_step_1'
	),
	'admin.auth.reset_password_step_1_thankyou' => array(
		'title' => 'Сбросить пароль',
		'route' => 'admin-auth:reset_password_step_1_thankyou'
	),
	'admin.auth.reset_password_step_2' => array(
		'title' => 'Сменить пароль',
		'route' => 'admin-auth:reset_password_step_2'
	),
	'admin.user.index' => array(
		'title' => 'Список пользователей',
		'route' => 'admin-user'
	),
	'admin.user.create' => array(
		'title' => 'Добавить пользователя',
		'parent' => 'admin.user.index',
		'route' => 'admin-user:add'
	),
	'admin.user.change_password' => array(
		'title' => 'Сменить пароль пользователя "${model.name}"',
		'parent' => 'admin.user.index',
		'model' => 'user',
		'route' => 'admin-user:change_password?id=${model.id}'
	),
	'admin.user.edit' => array(
		'title' => 'Редактировать пользователя "${model.name}"',
		'parent' => 'admin.user.index',
		'model' => 'user',
		'route' => 'admin-user:edit?id=${model.id}'
	),
);