<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_Labels extends CM_Form_Plugin_Abstract
{
	private $_labels = array();
	
	public function __construct(array $labels = array())
	{
		$this->_labels = $labels;
	}
	
	public function construct_form(CM_Form_Abstract $form, $param)
	{
		foreach ($this->_labels as $name => $label)
		{
			if ($form->has_field($name))
			{
				$form->get_field($name)->set_label($label);
			}
		}
	}
}