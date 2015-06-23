<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Validate extends Kohana_Validate
{
	public function to_jquery()
	{
		$out = array();
		foreach($this->_rules as $field => $rules)
		{
			foreach($rules as $rule => $params)
			{
				$function = 'to_jquery_rule_'.$rule;
				if(is_callable(array('Validate', $function)))
				{
					$result = call_user_func_array('Validate::'.$function, $params);
					$out['rules'][$field][$result['name']] = Arr::get($result, 'params');
				}
			}
		}
		
		return $out;
	}
	
	static public function to_jquery_rule_not_empty()
	{
		return array(
			'name' => 'required',
			'params' => TRUE
		);
	}
	
	static public function to_jquery_rule_min_length($len)
	{
		return array(
			'name' => 'minlength',
			'params' => $len
		);
	}
	
	static public function to_jquery_rule_max_length($len)
	{
		return array(
			'name' => 'maxlength',
			'params' => $len
		);
	}
	
	static public function to_jquery_rule_digit()
	{
		return array(
			'name' => 'digits',
			'params' => TRUE
		);
	}
	
	static public function to_jquery_rule_range($min, $max)
	{
		return array(
			'name' => 'range',
			'params' => array($min, $max)
		);
	}
	
	static public function to_jquery_rule_alpha_dash()
	{
		return array(
			'name' => 'alphadash',
			'params' => TRUE
		);
	}
	
	static public function to_jquery_rule_email()
	{
		return array(
			'name' => 'email',
			'params' => TRUE
		);
	}
	
	static public function to_jquery_callback($callback)
	{
		
	}
}