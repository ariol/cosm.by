<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Action extends Grid_Column_Link
{
	protected $_action = NULL;

	public function __construct(array $column = array())
	{
		parent::__construct($column);

		$this->_action = Arr::get($column, 'action', 'all');
	}

	protected function _draw_field($obj)
	{
		$title = Extasy::obj_placeholders($obj, $this->_title);
		if ($obj->{'allow_'.$this->_action}())
		{
			return parent::_draw_field($obj);
		}
		else
		{
			return '<td align="'.$this->_align.'"><font color="grey">'.$title.'</font></td>';
		}
	}
}