<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 * @author o.zgolich
 */

class Form_Admin_Certificate extends CM_Form_Abstract
{
    protected function init()
    {
        $this->add_plugin(new CM_Form_Plugin_ORM());
        $this->set_field('name', new CM_Field_String(), 1);
        $this->set_field('url', new CM_Field_Url(), 2);
        $this->set_field('sum', new CM_Field_String(), 3);
        $this->set_field('active', new CM_Field_Boolean(), 4);
        $this->set_field('important', new CM_Field_Boolean(), 5);
        $this->set_field('image', new CM_Field_File(), 6);
        $this->set_field('price', new CM_Field_String(), 7);
        $this->set_field('content', new CM_Field_Text(), 8);
        $this->set_field('validity', new CM_Field_String(), 9);
    }
}