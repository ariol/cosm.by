<?php defined('SYSPATH') or die('No direct script access.');

class Form_Admin_Filter extends CM_Form_Abstract
{	
    protected function init()
    {
        $this->add_plugin(new CM_Form_Plugin_ORM(NULL, array('name')));

		$this->set_field('property_type', new CM_Field_Select(
			ORM::factory('Filter')->get_property_types()
		), 10);
    }
}