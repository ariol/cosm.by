<?php defined('SYSPATH') or die('No direct script access.');

class Form_Admin_Product extends CM_Form_Abstract
{
    protected function init()
    {
        $this->add_plugin(new CM_Form_Plugin_ORM());

		$this->set_field('name', new CM_Field_String(), 0);
		$this->set_field('url', new CM_Field_Url(), 1);
		$this->set_field('category_id', new CM_Field_Select_ORM(ORM::factory('Category')), 2);
		$this->set_field('brand_id', new CM_Field_Select_ORM(ORM::factory('Brand')), 2.1);
		$this->set_field('line_id', new CM_Field_Select_ORM(ORM::factory('Line')), 2.1);
		$this->set_field('top', new CM_Field_Boolean(), 4);
		$this->set_field('active', new CM_Field_Boolean(), 5);
		$this->get_field('active')->set_raw_value(true); 
		$this->set_field('important', new CM_Field_Boolean(), 6);
		$this->set_field('comments_enabled', new CM_Field_Boolean(), 7);
		$this->get_field('comments_enabled')->set_raw_value(true); 
		$this->set_field('content', new CM_Field_HTML(), 12);
		$this->set_field('short_content', new CM_Field_HTML(), 11);
        $this->set_field('main_image', new CM_Field_File(), 16);
		$this->set_field('more_images', new CM_Field_Multifile(), 4);
		$this->set_field('price', new CM_Field_String(), 16);
		$this->set_field('new_price', new CM_Field_String(), 17);
		$this->set_field('purchase_price',new CM_Field_String(), 16);
		$this->set_field('article',new CM_Field_String(), 16);
		$this->set_field('color',new CM_Field_String(), 18);

		$fieldgroups = array(
			'Основные данные' => array('name', 'article', 'url', 'content', 'short_content','sizes','colors', 'price','purchase_price','new_price', 'discount', 'color'),
			'Атрибуты' => array('top', 'active', 'important', 'comments_enabled'),
			'Группы' => array('category_id', 'brand_id', 'line_id'),
			'Изображения' => array('main_image', 'more_images'),

		);

		$this->add_plugin(new CM_Form_Plugin_Fieldgroups($fieldgroups));
    }
}