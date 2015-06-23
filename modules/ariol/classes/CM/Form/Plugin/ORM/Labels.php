<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_ORM_Labels extends CM_Form_Plugin_ORM_Abstract
{
	public function construct_form(CM_Form_Abstract $form, $params)
	{
		$this->set_model($params);

		foreach ($form->get_field_names() as $name)
		{
			if ($label = arr::get($this->get_model()->labels(), $name))
			{
				$form->get_field($name)->set_label($label);
			}
		}
	}
}