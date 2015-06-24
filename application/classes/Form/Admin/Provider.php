<?php defined('SYSPATH') or die('No direct script access.');

class Form_Admin_Provider extends CM_Form_Abstract
{
    protected function init()
    {
        $this->add_plugin(new CM_Form_Plugin_ORM());
        $this->set_field('name', new CM_Field_String(), 0);
    }
}