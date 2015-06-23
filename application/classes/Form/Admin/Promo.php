<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 * @author o.zgolich
 */

class Form_Admin_Promo extends CM_Form_Abstract
{
    protected function init()
    {
        $this->add_plugin(new CM_Form_Plugin_ORM());

		$this->set_field('href', new CM_Field_String(), 1);
		$this->set_field('active', new CM_Field_Boolean(), 2);
		$this->set_field('position', new CM_Field_Int(), 3);
		$this->set_field('image', new CM_Field_File(), 5);
    }
}