<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

return array
(
	Application::NAME => array
	(
		'<i class="fa fa-user"></i> Настройки' => 'admin-config',
		'<i class="fa fa-cog"></i> Пользователи' => 'admin-user',
		'<i class="fa fa-key"></i> Сбросить пароль' => 'admin-auth:reset_password_step_1',
		'<i class="fa fa-envelope"></i> Изменить пароль' => 'admin-auth:change_password',
		'<i class="fa fa-power-off"></i> Выйти' => 'admin-auth:logout'
	)
);