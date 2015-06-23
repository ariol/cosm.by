<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 * @author o.zgolich
 */

class Form_Admin_News extends CM_Form_Abstract
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
		$this->set_field('image', new CM_Field_File(), 11);
        $this->set_field('active', new CM_Field_Boolean(), 15);
        $this->set_field('content', new CM_Field_HTML(), 25);
		$this->get_field('content')->set_attributes(array('rows' => 5));
		$this->set_field('short_text', new CM_Field_HTML(), 25);
		$this->get_field('short_text')->set_attributes(array('rows' => 5));
        $this->set_field('s_title', new CM_Field_String(), 45);
        $this->set_field('s_description', new CM_Field_Text(), 50);
        $this->get_field('s_description')->set_attributes(array('rows' => 5));
        $this->set_field('s_keywords', new CM_Field_Text(), 60);
        $this->get_field('s_keywords')->set_attributes(array('rows' => 5));
    }
}