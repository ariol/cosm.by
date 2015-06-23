<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Password extends CM_Field_String
{
	public function render()
	{
        $attributes = $this->get_attributes();

        if (arr::get($attributes, 'class')) {
            $attributes['class'] .= ' form-control';
        }
        else {
            $attributes['class'] = 'form-control';
        }

		return form::password($this->get_name(), $this->get_value(), $attributes);
	}
	
	public function get_type_name()
	{
		return 'Пароль';
	}
}