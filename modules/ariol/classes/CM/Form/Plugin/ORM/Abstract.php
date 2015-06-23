<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class CM_Form_Plugin_ORM_Abstract extends CM_Form_Plugin_Abstract
{
	/**
	 * @var ORM
	 */
	private $_model = NULL;

	protected function set_model($model)
	{
		$this->_model = $model;

		if ($this->_model instanceof CM_ModelContainer_Interface)
		{
			$this->_model = $this->_model->get_model();
		}

		if ( ! $this->_model instanceof ORM)
		{
			throw new Exception('model must be an instance of ORM');
		}
	}

	/**
	 * @return ORM
	 */
	protected function get_model()
	{
		if (is_null($this->_model))
		{
			throw new Exception('Model not set');
		}
		return $this->_model;
	}
}