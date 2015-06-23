<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Model_Feedback extends ORM
{
    protected $_table_name = 'feedback';

    protected $_grid_columns = array(
        'updated_at' => NULL,
        'text' => NULL,
        'name' => NULL,
        'email' => NULL,
        'status' => array(
            'type' => 'template',
            'template' => '${status_name}'
        ),
        'answers' => NULL,
        'answer' => array(
            'type' => 'link',
            'route_str' => 'admin-feedback:answer?id=${id}',
            'title' => '[ответить]',
            'alternative' => '[ответить]'
        ),
        'delete' => array(
            'width' => '50',
            'type' => 'link',
            'route_str' => 'admin-feedback:delete?id=${id}',
            'title' => '[X]',
            'alternative' => '[X]',
            'confirm' => 'Вы уверены?'
        )
    );
	
	public function labels()
	{
	    return array(
            'name' => 'Имя',
            'status' => 'Статус',
            'updated_at' => 'Дата поступления',
            'text' => 'Текст',
            'email' => 'Email',
            'answers' => 'Количество ответов'
	    );
	}
	
	public function rules()
	{
	    return array(
            'name' => array(
                array('not_empty')
            )
	    );
	}

    public function form()
	{
		return new Form_Admin_Feedback($this);
	}

    protected $_grid_options = array(
		'order_by' => 'updated_at',
		'order_direction' => 'ASC',
		'per_page' => 500
	);
	
	public function get_status_name()
	{
	    switch($this->status)
	    {
            case 'open':
                return 'Открыт';
            case 'in_process':
                return 'Принят в работу';
            case 'closed':
                return 'Закрыт';
	    }
	}
}