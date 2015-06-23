<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Image extends Grid_Column
{
	protected $_resize = NULL;
	
	public function __construct(array $column = array())
	{
		parent::__construct($column);
		$this->_resize = Arr::get($column, 'resize', $this->_resize);
	}
	
	protected function _field($obj)
	{
		if(empty($obj[$this->get_name()]))
		{
			return '<td>no image</td>';
		}
		$path = $obj[$this->get_name()];
		if( ! is_null($this->_resize))
		{
			$path = Resizer::thumb($path, $this->_resize);
		}
		return '<td>'.Html::image($path).'</td>';
	}
}