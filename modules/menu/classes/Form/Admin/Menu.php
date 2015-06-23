<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 * @author o.zgolich
 */

class Form_Admin_Menu extends CM_Form_Abstract
{
    protected function init()
    {
        $this->add_plugin(new CM_Form_Plugin_ORM());

        $this->set_field('name', new CM_Field_String(), 10);
        $this->set_field('active', new CM_Field_Boolean(), 30);
        $this->set_field('position', new CM_Field_Int(), 40);
    }
    
    public function construct_form($param) 
    {
	    parent::construct_form($param);
	
        $siteRoutes = $this->get_menu();

        if(array_key_exists($param->url, $siteRoutes)) {
            $this->set_field('url', new CM_Field_Change($siteRoutes), 20);
        }
        else {
            $this->set_field('url', new CM_Field_Change($siteRoutes, 'string'), 20);
        }

        $this->get_field('url')->set_raw_value($param->url);
    }
    
    public function get_menu()
    {
        $siteRoutes = array();

        $pages = ORM::factory('Page')->find_all();

        foreach($pages as $page) {
            $pageUrl = '/'.$page->url;
            $siteRoutes[$pageUrl] = $page->name;
        }

        return $siteRoutes;
    }
}