<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_Fieldgroups extends CM_Form_Plugin_Abstract
{
	private $_fieldgroups = array();
	
	public function __construct(array $fieldgroups = array())
	{
		$this->_fieldgroups = $fieldgroups;
	}
	
	public function construct_form(CM_Form_Abstract $form, $param)
	{
		$renderer = new CM_Form_Renderer_Fieldgroups();
		
		foreach ($this->_fieldgroups as $group_name => $fields)
		{
			if (is_array($fields))
			{
				foreach ($fields as $field_name)
				{
					$renderer->set_field_fieldgroup($field_name, $group_name);
				}
			}
			else
			{
				foreach ($form->get_field_names() as $field_name)
				{
					if (preg_match($fields, $field_name))
					{
						$renderer->set_field_fieldgroup($field_name, $group_name);
					}
				}
			}
		}
		
		$form->set_renderer($renderer);
	}
}