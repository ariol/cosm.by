<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 * @author FlaBla
 */

class Form_Admin_Answer extends CM_Form_Abstract
{   
	protected $_model = NULL;
	
	public function construct_form($param) 
	{
	    $this->set_field('name', new CM_Field_String(), 10);
	    $this->get_field('name')->set_label('Имя');
	    $this->get_field('name')->set_attributes(array('disabled' => true));
	    $this->set_field('email', new CM_Field_String(), 20);
	    $this->get_field('email')->set_label('Email');
	    $this->get_field('email')->set_attributes(array('disabled' => true));
	    $this->set_field('text', new CM_Field_Text(10), 30);
	    $this->get_field('text')->set_label('Текст');
	    $this->get_field('text')->set_attributes(array('rows' => 5, 'disabled' => true));
	    
	    $this->get_field('name')->set_raw_value($param->name);
	    $this->get_field('email')->set_raw_value($param->email);
	    $this->get_field('text')->set_raw_value($param->text);
	    $this->_model = $param;
	}
	
	public function init() 
	{
	    $this->add_plugin(new CM_Form_Plugin_ORM(NULL, array('answer', 'subject')));
	    
	    $this->set_field('subject', new CM_Field_String(), 40);
	    $this->get_field('subject')->set_label('Тема');
	    $this->set_field('answer', new CM_Field_HTML(), 50);
	    $this->get_field('answer')->set_label('Ответ');
	    $this->get_field('answer')->set_attributes(array('rows' => 5));
	}


	public function after_submit() 
	{
	    Email::send($this->get_field('email')->get_value()->get_raw(), array('o.zgolich@gmail.com', 'zgol-web.by'),
		    $this->get_field('subject')->get_value()->get_raw(), 
			$this->get_field('answer')->get_value()->get_raw(), TRUE);
	    $model = ORM::factory('Feedback')->where('id', '=', $this->_model->id)->find();
	    $model->answers++;
	    $model->save();
	}
}