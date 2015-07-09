<?php defined('SYSPATH') or die('No direct script access.');

/** * @version SVN: $Id:$ */
class Model_Coupons extends ORM
{
    protected $_table_name = 'coupons';

    protected $_grid_columns = array(
        'name' => array(
            'type' => 'template',
            'template' => '${findOrederName}'
        ),
        'code' => null,
        'create_date' => null,
        'time_end' => null,
        'active' => array(
            'type' => 'bool'
        ),
        'edit' => array(
            'width' => '50',
            'type' => 'link',
            'route_str' => 'admin-coupons:edit?id=${id}',
            'title' => '<i class="fa fa-pencil"></i>',
            'color' => 'green',
            'alternative' => 'Редактировать'
        ),
        'delete' => array(
            'width' => '50',
            'type' => 'link',
            'route_str' => 'admin-coupons:delete?id=${id}',
            'title' => '<i class="fa fa-trash-o"></i>',
            'color' => 'red',
            'alternative' => 'Удалить',
            'confirm' => 'Вы уверены?'
        )
    );

    public function labels()
    {
        return array(
            'name' => 'Клиент',
            'code' => 'Код купона',
            'create_date' => 'Дата создания',
            'discount' => 'Скидка',
            'time_end' => 'Срок действия',
            'active' => 'Активность',
        );
    }

    public function form()
    {
        return new Form_Admin_Coupons($this);
    }

    protected $_grid_options = array(
        'order_by' => 'id',
        'order_direction' => 'DESC'
    );

    public function get_findOrederName()
    {
        $PDO_coupon = ORM::factory('Coupons')->PDO();
        $query = "SELECT orders.name, orders.id
                        FROM orders
                         WHERE orders.id = '{$this->order_id}'";
        $result = $PDO_coupon->query($query)->fetch();
        return $result['name'];
    }
}