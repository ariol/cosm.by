<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 * @author o.zgolich
 */

class Form_Admin_Page extends CM_Form_Abstract
{
    public function construct_form($param) 
    {
        $this->add_plugin(new CM_Form_Plugin_ORM_Autocomplete($param));

        if($param->static) {
            $this->get_field('url')->set_attributes(array('disabled' => 'disabled'));
            $this->get_field('name')->set_attributes(array('disabled' => 'disabled'));
        }
    }

    protected function init()
    {
        $this->add_plugin(new CM_Form_Plugin_ORM(NULL, array('updated_at')));

        $this->set_field('name', new CM_Field_String(), 10);
        $this->set_field('s_title', new CM_Field_String(), 20);
        $this->set_field('s_description', new CM_Field_Text(), 30);
        $this->get_field('s_description')->set_attributes(array('rows' => 5));
        $this->set_field('s_keywords', new CM_Field_Text(), 40);
        $this->get_field('s_keywords')->set_attributes(array('rows' => 5));
        $this->set_field('url', new CM_Field_String(), 50);
        $this->set_field('active', new CM_Field_Boolean(), 60);
        $this->set_field('content', new CM_Field_HTML(), 70);
    }
}