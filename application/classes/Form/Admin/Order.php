<?php defined('SYSPATH') or die('No direct script access.');

class Form_Admin_Order extends CM_Form_Abstract
{	
    protected function init()
    {
        $this->add_plugin(new CM_Form_Plugin_ORM());

        $this->set_field('status', new CM_Field_Select(
			ORM::factory('Orders')->statuses
		), 5);
    }
}
