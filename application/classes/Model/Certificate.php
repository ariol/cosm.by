<?php defined('SYSPATH') or die('No direct script access.');

/** * @version SVN: $Id:$ */
class Model_Certificate extends ORM
{
    protected $_table_name = 'certificate';

    protected $_grid_columns = array(
        'name' => null,
        'create_date' => null,
        'sum' => null,
        'edit' => array(
            'width' => '50',
            'type' => 'link',
            'route_str' => 'admin-certificate:edit?id=${id}',
            'title' => '<i class="fa fa-pencil"></i>',
            'color' => 'green',
            'alternative' => 'Редактировать'
        ),
        'delete' => array(
            'width' => '50',
            'type' => 'link',
            'route_str' => 'admin-certificate:delete?id=${id}',
            'title' => '<i class="fa fa-trash-o"></i>',
            'color' => 'red',
            'alternative' => 'Удалить',
            'confirm' => 'Вы уверены?'
        )
    );

    public function labels()
    {
        return array(
            'name' => 'Заглавие сертификата',
            'url' => 'URL',
            'image' => 'Изображение',
            'content' => 'Описание',
            'price' => 'Цена',
            'sum' => 'Номинал сертификата',
            'create_date' => 'Дата создания',
            'active' => 'Активность',
            'important' => 'Показать на странице',
            'validity' => 'Срок действия (кол-во дней)',
        );
    }

    public function form()
    {
        return new Form_Admin_Certificate($this);
    }

    public function fetcCertificateForMmain()
    {
        return $this->where('certificate.active', '=', 1)
            ->where('certificate.important', '=', 1)
            ->order_by('certificate.create_date', 'DESC')->limit(8)->find_all();
    }
    public function fetchCertificateById($id)
    {
        return $this->where('certificate.active', '=', 1)
            ->where('certificate.id', '=', $id)->find();
    }

}