<?php defined('SYSPATH') or die('No direct script access.');

class CM_Form_Plugin_ORM_Autocomplete extends CM_Form_Plugin_ORM_Abstract
{
    private $_model = NULL;

    public function __construct($model)
    {
	$this->_model = $model;
    }

    public function  after_submit(CM_Form_Abstract $form)
    {
	$this->_model->save();
	
	if ( ! trim($form->get_field('url')->get_value()->get_raw()) && $this->_model->name != 'Главная страница') {
	    $this->_model->url = Extasy_Text::to_url($form->get_field('name')->get_value()->get_raw());
	}
	
	if ( ! trim($form->get_field('s_title')->get_value()->get_raw())) {
	    $this->_model->s_title = $this->_model->name;
	}
	
	$number_of_dashes = substr_count($this->_model->url, '--');
	
	$replacement_string = '';
	
	for($i = 0; $i < $number_of_dashes; $i++) {
	    $replacement_string .= '--';
	}
	
	$this->_model->url = str_replace($replacement_string, '-', $this->_model->url);

	$this->_model->save();

	parent::after_submit($form);
    }
}