<?php defined('SYSPATH') or die('No direct script access.');

/**

 * @version SVN: $Id:$

 * @author o.zgolich

 */

class Form_Admin_Line extends CM_Form_Abstract
{
    public function construct_form($param)
    {
        $this->add_plugin(new CM_Form_Plugin_ORM_Autocomplete($param));
    }

    protected function init()
    {
        $this->add_plugin(new CM_Form_Plugin_ORM(NULL, array('md5_url')));
        $this->set_field('name', new CM_Field_String(), 5);
        $this->set_field('url', new CM_Field_String(), 10);
        $this->set_field('active', new CM_Field_Boolean(), 15);
    }
}