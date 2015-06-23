<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

return array(
	'reload_time' => 60,
	'form' => array(
		'priorities' => array(
			'Общие' => 100,
            'Почта' => 200
		),
		'fieldgroups' => array(
            'Почта' => array(
                'mailer.hostname' => array(
                    'label' => 'Хост',
                    'field' => new CM_Field_String(),
                ),
                'mailer.from' => array(
                    'label' => 'Отправитель',
                    'field' => new CM_Field_String(),
                ),
                'mailer.username' => array(
                    'label' => 'Логин',
                    'field' => new CM_Field_String(),
                ),
                'mailer.password' => array(
                    'label' => 'Пароль',
                    'field' => new CM_Field_Password(),
                ),
                'mailer.port' => array(
                    'label' => 'Порт',
                    'field' => new CM_Field_Int(),
                ),
                'mailer.encryption' => array(
                    'label' => 'Шифрование',
                    'field' => new CM_Field_String(),
                )
            ),
			'Общие' => array(
				 'price.update' => array(
                    'label' => 'Обновить прайс',
                    'field' => new CM_Field_Boolean(),
                ),
			)
		)
	)
);