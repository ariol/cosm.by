<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

return array(
   'admin.feedback.index' => array(
		'title' => 'Обратная связь',
		'route' => 'admin-feedback'
	),
   'admin.feedback.answer' => array(
		'title' => 'Форма ответа',
		'route' => 'admin-feedback:answer',
		'parent' => 'admin-feedback'
	)
);