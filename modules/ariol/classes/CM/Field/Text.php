<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Text extends CM_Field_String
{
	protected $_max_len = NULL;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_type_name()
	{
		return 'Текст';
	}

	public function render()
	{
		$attributes = $this->get_attributes();

		if (isset($attributes['style'])) {
			$attributes['style'] .= ' width: 100%;';
		}
		else {
			$attributes['style'] = 'width: 100%;';
		}

        $attributes['style'] .= ' height: 150px;';
		$attributes['style'] .= ' resize: none;';
		$attributes['style'] .= ' padding: 5px 5px 5px 10px;';

		return Form::textarea($this->get_name(), $this->get_value()->get_raw(), $attributes);
	}
}