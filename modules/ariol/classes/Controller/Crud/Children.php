<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Controller_Crud_Children extends Controller_Crud
{
	protected $_parent_model = NULL;
	protected $_parent_key = NULL;
	protected $_parent_field = NULL;

	/**
	 * @return ORM
	 */
	protected function before_fetch(ORM $item)
	{
		if(is_null($this->_parent_key))
		{
			throw new Extasy_Exception('Parent key not defined');
		}
		$item->where($this->_parent_key, '=', $this->param('parent'));
		return $item;
	}

	protected function before_create(ORM $item)
	{
		if(is_null($this->_parent_field))
		{
			throw new Extasy_Exception('Parent field not defined');
		}

		if(is_null($this->_parent_model))
		{
			throw new Extasy_Exception('Parent model not defined');
		}

		$item->{$this->_parent_field} = ORM::factory($this->_parent_model, $this->param('parent'));
		return $item;
	}

	protected function get_item()
	{
		$item = ORM::factory($this->_model, array(
			'id' => $this->param('id'),
			$this->_parent_key => $this->param('parent')
		));

		if( ! $item->loaded())
		{
			$this->forward_404();
		}
		return $item;
	}

	protected function get_index_route()
	{
		return 'admin-'.$this->_model.':index?parent='.$this->param('parent');
	}

	protected function get_create_route()
	{
		return 'admin-'.$this->_model.':create?parent='.$this->param('parent');
	}

	protected function get_edit_route()
	{
		return 'admin-'.$this->_model.':edit?parent='.$this->param('parent');
	}
}