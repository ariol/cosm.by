<?php defined('SYSPATH') or die('No direct script access.');/** * @version SVN: $Id:$ */class Model_Orders extends ORM{    protected $_table_name = 'orders';	public $statuses = array(		1 => 'Новый',		2 => 'Подтверждён',		3 => 'Отправлен',		4 => 'Доставлен',		5 => 'Нет на складе',		6 => 'Возврат'	);    protected $_grid_columns = array(		'name' => null,		'phone' => null,		'created_at' => null,		'product_info' => null,		'status_name' => null,		'edit' => array(			'width' => '40',			'type' => 'link',			'route_str' => 'admin-orders:edit?id=${id}',			'title' => '<i class="fa fa-edit"></i>',			'color' => 'green',			'alternative' => 'Редактировать'		),        'delete' => array(            'width' => '40',            'type' => 'link',            'route_str' => 'admin-orders:delete?id=${id}',            'title' => '<i class="fa fa-trash-o"></i>',            'alternative' => 'Удалить',			'color' => 'red',            'confirm' => 'Вы уверены?'        )    );    public function labels()    {        return array(			'name' => 'Имя',			'phone' => 'Телефон',			'created_at' => 'Дата поступления',			'status_name' => 'Статус',			'product_info' => 'Детали заказа',			'status' => 'Статус'        );    }    public function form()    {        return new Form_Admin_Order($this);    }	public function get_product_info()	{		return HTML::anchor('/ariol-admin/order/view/' . $this->id, 'Детали заказа <span class="glyphicon glyphicon-link"></span>');	}	public function get_status_name()	{        $status_value = '';		switch($this->statuses[$this->status]) {            case 'Новый':                $status_value = '<span class="label label-info">Новый</span>';            break;            case 'Подтверждён':                $status_value = '<span class="label label-primary">Подтверждён</span>';            break;            case 'Отправлен':                $status_value = '<span class="label label-warning">Отправлен</span>';            break;            case 'Доставлен':                $status_value = '<span class="label label-success">Доставлен</span>';            break;            case 'Нет на складе':                $status_value = '<span class="label label-default">Нет на складе</span>';            break;            case 'Возврат':                $status_value = '<span class="label label-danger">Возврат</span>';            break;        }        return $status_value;	}    public function send_message()    {        $this->statuses == 1;    }    public function sortable_fields()    {        return array(            'created_at',        );    }    public function save(Validation $validation = NULL)    {        parent::save($validation);        if ($this->status == 4) {            $order = $this;            $PDO = ORM::factory('Orders')->PDO();            $orders_flag = $PDO->query("SELECT success_flag FROM orders WHERE id = '{$order->id}'")->fetchAll(PDO::FETCH_ASSOC);            if ($orders_flag[0]['success_flag'] != 1) {                $time = time();                $time_end = $time + (86400 * 180);                $time_end = date("Y-m-d", $time_end);                $time_end_coupon = $time + (86400 * 60);                $time_end_coupon = date("Y-m-d", $time_end_coupon);                $full_price = 0;                $success_flag_query = "UPDATE orders SET success_flag = 1 WHERE id = '{$order->id}'";                $PDO->exec($success_flag_query);                $PDO = ORM::factory('OrderProduct')->PDO();                $query = "SELECT order_product.quantity, order_product.price                          FROM order_product                          WHERE order_id = '{$order->id}'";                $order_data = $PDO->query($query)->fetchAll(PDO::FETCH_ASSOC);                $discount = $PDO->query("SELECT coupons.code, coupons.discount, coupons.time_end                                    FROM coupons                                    WHERE coupons.order_id = '{$order->id}'")->fetch();                if ($discount['discount']) {                    $order_success = View::factory('admin/order/order_success', array(                        'name' => $order->name,                        'email' => $order->email,                        'phone' => $order->phone,                        'adress' => $order->adress,                        'time' => $time_end,                        'discount' => $discount['discount'],                        'code' => $discount['code']                    ))->render();                    $PDO_coupon = ORM::factory('Coupons')->PDO();                    $PDO_coupon->query("UPDATE coupons SET time_end = '{$time_end_coupon}', active = 1  WHERE order_id = '{$order->id}'");                    if ($order->email != '') {                        Email::send($order->email, 'vitalyasv@mail.ru', 'Скидочный купон', $order_success, true);                    }                }                $PDO_order_certificate = ORM::factory('OrderCertificate')->PDO();                $query_validity_certificate = "SELECT id, time_end                                                FROM order_certificate                                                WHERE order_id = '{$order->id}'";                $validity_data = $PDO_order_certificate->query($query_validity_certificate)->fetchAll(PDO::FETCH_ASSOC);                foreach($validity_data as $v_data)                {                    $time = time();                    $time_end_certificate = $time + (86400 * $v_data['time_end']);                    $time_end_certificate = date("Y-m-d", $time_end_certificate);                    $update_validity_query = "UPDATE order_certificate SET time_end = '{$time_end_certificate}', active = 1 WHERE id = '{$v_data['id']}'";                    $PDO_order_certificate->query($update_validity_query);                }            }        }    }}