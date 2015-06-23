<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class ACL extends Extasy_ACL
{
    static private $_instance = NULL;

    /**
     * @return ACL
     */
    static public function instance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

	protected function init() {}
}