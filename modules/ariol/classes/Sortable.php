<?php defined('SYSPATH') or die('No direct script access.');

class Sortable
{
	static public function get_button($name, $label, array $options = array(), $arr = false)
	{
		$dest = Arr::get($_GET, 'dest');
		$order = Arr::get($_GET, 'order');
		
		$link = $_SERVER['REQUEST_URI'];
		$link = str_replace('&dest=' . $dest, '', $link);
		$link = str_replace('&order=' . $order, '', $link);
		$link = str_replace('?dest=' . $dest, '', $link);
		$link = str_replace('?order=' . $order, '', $link);
		$options['rel'] .= 'nofollow';
		if ($options['class']) {
			$options['class'] .= ' ';
		}
		
		if (preg_match('%\?%', $link)) {
			$link .= '&';
		} else {
			$link .= '?';
		}
		
		if ($order == $name) {
			$link .= 'order=' . $order;
			if ($dest == 'asc') {
				$options['class'] .= 'desc';
				$link .= '&dest=desc';
			} else {
				$options['class'] .= 'asc';
				$link .= '&dest=asc';
			}
			
			if($arr){
				if($dest=='asc' || $dest==null){
					$arrow = ' &uarr;';
				} else {
					$arrow = ' &darr;';
				}
			}
			
		} else {
			$link .= 'order=' . $name . '&dest=asc';
			if($arr){
				$arrow = '';
			}
		}

		
		return HTML::anchor($link, $label.$arrow, $options);
	}
}