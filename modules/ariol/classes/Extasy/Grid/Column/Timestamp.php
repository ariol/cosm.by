<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Timestamp extends Grid_Column
{
	protected $_orderable = TRUE;
	
	protected function _field($obj)
	{
		$timestamp = $obj[$this->get_name()];
		if( ! $timestamp)
		{
			return '<td>---</td>';
		}
		return '<td>'.date('Y-m-d H:i:s', $timestamp).'</td>';
	}
}