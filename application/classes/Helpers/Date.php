<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Helpers_Date
{
    public static function get_date($dateStr)
    {
	   $time = strtotime($dateStr); 
	   
	   $year = date('Y', $time);
	   $month = self::get_month_name(date('m', $time));
	   $day = date('d', $time);
	   
	   return $day.' '.$month.' '.$year;
	   
    }
    
    public static function get_article_date($dateStr)
    {
	$time = strtotime($dateStr); 
	
	return self::get_date($dateStr).' в '.date('H:i', $time);
    }

    private static function get_month_name($month_number)
    {
	switch(intval($month_number))
	{
	    case 1:
		return 'Января';
		break;
	    case 2:
		return 'Февраля';
		break;
	    case 3:
		return 'Марта';
		break;
	    case 4:
		return 'Апреля';
		break;
	    case 5:
		return 'Мая';
		break;
	    case 6:
		return 'Июня';
		break;
	    case 7:
		return 'Июля';
		break;
	    case 8:
		return 'Августа';
		break;
	    case 9:
		return 'Сентября';
		break;
	    case 10:
		return 'Октября';
		break;
	    case 11:
		return 'Ноября';
		break;
	    case 12:
		return 'Декабря';
		break;
	}
    }
}