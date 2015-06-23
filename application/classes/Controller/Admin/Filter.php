<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Admin_Filter extends Controller_Crud_Children
{
    protected $_model = 'Filter';
    protected $_parent_model = 'Category';
    protected $_parent_key = 'category_id';
    protected $_parent_field = 'category_id';

    public function change_status(ORM $item)
    {
        $item->active = !$item->active;

        $item->save();
    }
}