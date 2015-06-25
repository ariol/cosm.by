<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Statistics extends Controller_Crud
{
    protected $_model = 'Statistics';
    public function action_calculation()
    {

    }
    public function action_order()
    {
        $page = $_GET['page'];
        if(!$page)
            $page = 1;
        $limit = 20;
        $price_start =  $_GET['price_start'];
        $price_final = $_GET['price_final'];
        $this->template->price_start = $price_start;
        $this->template->price_final = $price_final;
        $PDO_product = ORM::factory('Orders')->PDO();
        $offset = $page * $limit - $limit;
        $query = "SELECT  orders.id,
                        orders.name,
                        orders.email,
                        orders.phone,
                        orders.status,
                        orders.created_at,
                        orderproduct.prod_price,
                        ordercertificate.cert_price,
                        code_certificate,
                        coupons.discount,
                        order_certificate.to_amount
                        FROM orders
                      LEFT JOIN (SELECT order_id, product_id, SUM(quantity * price) as prod_price
                      FROM order_product
                      GROUP BY order_id) as orderproduct ON orderproduct.order_id = orders.id
                      LEFT JOIN
                      (SELECT order_id, SUM(price) as cert_price
                      FROM order_certificate
                      GROUP BY order_id) as  ordercertificate
                      ON orders.id = ordercertificate.order_id
                      LEFT JOIN  coupons ON coupons.code = orders.code_coupon
                      LEFT JOIN order_certificate ON order_certificate.code = orders.code_certificate
                      ORDER BY orderproduct.prod_price DESC";
        $result = $PDO_product->query($query)->fetchAll(PDO::FETCH_ASSOC);
        $orders_array = [];
        $total_product = 0;
        foreach ($result as $item) {
            $price = $item['prod_price'];
            if ($item['discount'])
                $price = $item['prod_price'] - ($item['prod_price'] / 100 * $item['discount']);
            if ($item['to_amount'])
                $price = $item['prod_price'] - $item['to_amount'];
            if ($item['discount'] and $item['to_amount'])
                $price = ($item['prod_price'] - ($item['prod_price'] / 100 * $item['discount'])) - $item['to_amount'];
            $price += $item['cert_price'];
            if ($price >= $price_start and $price <= $price_final) {
                $price = $price;
                switch ($item['status']) {
                    case 1:
                        $status_value = '<span class="label label-info">Новый</span>';
                        break;
                    case 2:
                        $status_value = '<span class="label label-primary">Подтверждён</span>';
                        break;
                    case 3:
                        $status_value = '<span class="label label-warning">Отправлен</span>';
                        break;
                    case 4:
                        $status_value = '<span class="label label-success">Доставлен</span>';
                        break;
                    case 5:
                        $status_value = '<span class="label label-default">Нет на складе</span>';
                        break;
                    case 6:
                        $status_value = '<span class="label label-danger">Возврат</span>';
                        break;
                }
                $total_product++;
                $orders_array[] = ['name' => $item['name'], 'phone' => $item['phone'], 'created_at' => $item['created_at'], 'price' => $price, 'id' => $item['id'], 'status' => $status_value];
            }
        }
        usort ($orders_array, function($x, $y) {
            if ($x['price'] == $y['price'])
                return 0;
            else if ($x['price'] > $y ['price'])
                return -1;
            else
                return 1;
        });
        $orders_array = array_slice($orders_array, $offset, $limit);
        $this->template->result = $orders_array;
        $total_page = ceil($total_product / $limit);
        $this->template->pagination =
            Pagination::factory(
                array(
                    'total_items'    => $total_page,
                    'items_per_page' => $this->_items_on_page,
                    'view' => 'extasy/pagination/basic',
                    'current_page'   => array('source' => 'query_string', 'key' => 'page'),
                )
            )->render();
    }

    public function action_provider_calculation()
    {
        $from = $_GET['from'];
        $to = $_GET['to'];
        $provider = $_GET['provider'];
        $this->template->from = $from;
        $this->template->to = $to;
        $this->template->provider = $provider;
        $limit = 20;
        $page =  $_GET['page'];
        if(!$page) {
            $page = 1;
        }
        $offset = $page * $limit - $limit;
        $PDO = ORM::factory('Orders')->PDO();
        if(!$provider) {
            $query = "SELECT SQL_CALC_FOUND_ROWS *,
                        product_id,
                        quantity,
                        order_product.price * order_product.quantity as price_quantity,
                        orders.created_at,
                        orders.status,
                        brand_id,
                        provider.name,
                        provider.id as prov_id,
                        SUM(order_product.quantity) as quantity_prod,
                        SUM(purchase_price * order_product.quantity) as purchase_price_quantity
                        FROM order_product
                        LEFT JOIN orders  ON orders.id = order_product.order_id
                        LEFT JOIN product  ON product.id = order_product.product_id
                        LEFT JOIN brand  ON brand.id = product.brand_id
                        LEFT JOIN provider  ON provider.id = brand.provider_id
                        WHERE orders.created_at BETWEEN '{$from}' AND '{$to}' AND orders.status = 4 GROUP BY provider.name ORDER BY purchase_price_quantity DESC LIMIT $offset, $limit";
        }else{
            $query = "SELECT SQL_CALC_FOUND_ROWS *,
                        product_id,
                        product.name as prod_name,
                        quantity,
                        orders.created_at,
                        orders.status,
                        brand_id,
                        provider.name as prov_name,
                        provider.id as prov_id,
                        order_product.quantity as quantity_prod,
                        purchase_price * order_product.quantity as purchase_price_quantity
                        FROM order_product
                        LEFT JOIN orders  ON orders.id = order_product.order_id
                        LEFT JOIN product  ON product.id = order_product.product_id
                        LEFT JOIN brand  ON brand.id = product.brand_id
                        LEFT JOIN provider  ON provider.id = brand.provider_id
                        WHERE orders.created_at BETWEEN '{$from}' AND '{$to}' AND orders.status = 4 AND  provider.id = '{$provider}' ORDER BY quantity_prod DESC LIMIT $offset, $limit";
        }
        $search_result = $PDO->query($query)->fetchAll(PDO::FETCH_ASSOC);
        $rs1 = $PDO->query('SELECT FOUND_ROWS()');
        $total_product = (int) $rs1->fetchColumn();
        $this->template->result = $search_result;
        $total_page = ceil($total_product / $limit);
        $this->template->pagination =
            Pagination::factory(
                array(
                    'total_items'    => $total_page,
                    'items_per_page' => $this->_items_on_page,
                    'view' => 'extasy/pagination/basic',
                    'current_page'   => array('source' => 'query_string', 'key' => 'page'),
                )
            )->render();
    }

    public function action_clients_article()
    {
        $limit = 20;
        $article = $_GET['article'];
        $this->template->article = $article;
        $page =  $_GET['page'];
        if(!$page) {
            $page = 1;
        }
        $PDO = ORM::factory('Orders')->PDO();
        $offset = $page * $limit - $limit;
        $query = "SELECT o.name, o.email, o.phone, o.id, p.article,  p.name as p_name FROM order_product op
                            LEFT JOIN product p ON op.product_id = p.id
                            LEFT JOIN orders o ON op.order_id = o.id
                            WHERE p.article = '{$article}'
                            GROUP BY o.email
                            LIMIT $offset, $limit";
        $client_article = $PDO->query($query)->fetchAll(PDO::FETCH_ASSOC);
        $total_product = $PDO->query("SELECT count(DISTINCT email) FROM order_product op
                            LEFT JOIN product p ON op.product_id = p.id
                            LEFT JOIN orders o ON op.order_id = o.id
                            WHERE p.article = '{$article}'")->fetch(PDO::FETCH_COLUMN);

        foreach($client_article as $item)
        {
            $orders_array[] = [ 'name' => $item['name'],  'phone' => $item['phone'], 'email' =>   $item['email'],  'article' => $item['article'],  'p_name' => $item['p_name']];
        }
        $this->template->orders_array = $orders_array;
            $total_page = ceil($total_product / $limit);
            $this->template->pagination =
                Pagination::factory(
                    array(
                        'total_items' => $total_page,
                        'items_per_page' => $this->_items_on_page,
                        'view' => 'extasy/pagination/basic',
                        'current_page'   => array('source' => 'query_string', 'key' => 'page'),
                    )
                )->render();
        }

    public function action_clients_summ()
    {
        $limit = 20;
        $price_start = $_GET['price_start'];
        $price_final = $_GET['price_final'];
        $group = $_GET['group'];
        $this->template->price_start = $price_start;
        $this->template->price_final = $price_final;
        if(!$price_start)
            $price_start = 0;
        if(!$price_final)
            $price_final = 10000000000000000;
        $page =  $_GET['page'];
        if(!$page) {
            $page = 1;
        }
        if(!$group) {
            $group = 'email';
        }
        $this->template->group = $group;
        $PDO = ORM::factory('Orders')->PDO();
        $offset = $limit * $page - $limit;
        if($group == 'phone'){
            $search_query = "SELECT
                                SQL_CALC_FOUND_ROWS *,
                                orders.id,
                                orders.name,
                                orders.email,
                                orders.phone,
                                orderproduct.prod_price,
                                ordercertificate.cert_price,
                                SUM(IFNULL(orderproduct.prod_price, 0) + IFNULL(ordercertificate.cert_price, 0)) as summ_price
                                FROM orders
                              LEFT JOIN (SELECT order_id, product_id, SUM(IFNULL(quantity,0) * IFNULL(price, 0)) as prod_price
                              FROM order_product
                              GROUP BY order_id) as orderproduct ON orderproduct.order_id = orders.id
                              LEFT JOIN
                              (SELECT order_id, SUM(IFNULL(price,0)) as cert_price
                              FROM order_certificate
                              GROUP BY order_id) as  ordercertificate
                              ON orders.id = ordercertificate.order_id
                              GROUP BY orders.phone  HAVING summ_price BETWEEN {$price_start} AND {$price_final}
                              ORDER BY summ_price DESC LIMIT $offset, $limit";

        }else {
            $search_query = "SELECT
                                SQL_CALC_FOUND_ROWS *,
                                orders.id,
                                orders.name,
                                orders.email,
                                orders.phone,
                                orderproduct.prod_price,
                                ordercertificate.cert_price,
                                SUM(IFNULL(orderproduct.prod_price, 0) + IFNULL(ordercertificate.cert_price, 0)) as summ_price
                                FROM orders
                              LEFT JOIN (SELECT order_id, product_id, SUM(IFNULL(quantity,0) * IFNULL(price, 0)) as prod_price
                              FROM order_product
                              GROUP BY order_id) as orderproduct ON orderproduct.order_id = orders.id
                              LEFT JOIN
                              (SELECT order_id, SUM(IFNULL(price,0)) as cert_price
                              FROM order_certificate
                              GROUP BY order_id) as  ordercertificate
                              ON orders.id = ordercertificate.order_id
                              GROUP BY orders.email  HAVING summ_price BETWEEN {$price_start} AND {$price_final}
                              ORDER BY summ_price DESC LIMIT $offset, $limit";
        }
        $search_result = $PDO->query($search_query)->fetchAll(PDO::FETCH_ASSOC);
        $rs1 = $PDO->query('SELECT FOUND_ROWS()');
        $total_product = (int) $rs1->fetchColumn();
        $this->template->quantity_product_orders = $search_result;
            $total_page = ceil($total_product / $limit);
            $this->template->pagination =
                Pagination::factory(
                    array(
                        'total_items'    => $total_page,
                        'items_per_page' => $this->_items_on_page,
                        'view' => 'extasy/pagination/basic',
                        'current_page'   => array('source' => 'query_string', 'key' => 'page'),
                    )
                )->render();
    }

    public function action_clients_city()
    {
        $limit = 20;
        $city = $_GET['city'];
        $group = $_GET['group'];
        $this->template->city = $city;
        $page =  $_GET['page'];
        if(!$page) {
            $page = 1;
        }
        if(!$group) {
            $group = 'email';
        }
        $this->template->group = $group;
        $offset = $limit * $page - $limit;
        $PDO = ORM::factory('Orders')->PDO();
        if( $group == 'email') {
            $query = "SELECT   SQL_CALC_FOUND_ROWS *, name, email, phone, adress, city
                            FROM orders
                            WHERE adress LIKE '%{$city}%' OR city LIKE '%{$city}%'
                            GROUP BY email
                            LIMIT $offset, $limit";
        }else {
            $query = "SELECT   SQL_CALC_FOUND_ROWS *, name, email, phone, adress, city
                            FROM orders
                            WHERE adress LIKE '%{$city}%' OR city LIKE '%{$city}%'
                            GROUP BY phone
                            LIMIT $offset, $limit";
        }
        $search_result = $PDO->query($query)->fetchAll(PDO::FETCH_ASSOC);
        $rs1 = $PDO->query('SELECT FOUND_ROWS()');
        $total_product = (int) $rs1->fetchColumn();
        $orders_array = [];
        foreach($search_result as $clients)
        {
            $orders_array[] = ['name' => $clients['name'], 'phone' => $clients['phone'], 'email' => $clients['email'], 'city' => $clients['city'], 'adress' => $clients['adress']];
        }
        $this->template->orders_array = $orders_array;
        $total_page = ceil($total_product / $limit);
        $this->template->pagination =
            Pagination::factory(
                array(
                    'total_items'    => $total_page,
                    'items_per_page' => $this->_items_on_page,
                    'view' => 'extasy/pagination/basic',
                    'current_page'   => array('source' => 'query_string', 'key' => 'page'),
                )
            )->render();
    }

    public function action_product()
    {
        $PDO = ORM::factory('Product')->PDO();
        $page =  $_GET['page'];
        $article =  $_GET['article'];
        $this->template->article = $article;
        if(!$page) {
            $page = 1;
        }
        $limit = 20;
        $offset = $limit * $page - $limit;
        if($article){
            $query_quantity_product = " SELECT COUNT(product_id) as total_result,  product_id, SUM(quantity) as quantity_prod, url, product.id, product.name, product.article
                                    FROM order_product
                                    LEFT JOIN product ON product.id = order_product.product_id
                                    GROUP BY product_id HAVING product.article = '{$article}'";
            $quantity_product_orders = $PDO->query($query_quantity_product)->fetchAll(PDO::FETCH_ASSOC);
            $this->template->quantity_product_orders = $quantity_product_orders;
        }else{
            $query_quantity_product = " SELECT COUNT(product_id) as total_result,  product_id, SUM(quantity) as quantity_prod, url, product.id, product.name
                                        FROM order_product
                                        LEFT JOIN product ON product.id = order_product.product_id
                                        GROUP BY product_id
                                        ORDER BY quantity_prod DESC LIMIT $offset, $limit";
            $quantity_product_orders = $PDO->query($query_quantity_product)->fetchAll(PDO::FETCH_ASSOC);
            $total_quantity = $PDO->query(" SELECT COUNT(product_id) as total_result,  SUM(quantity) as quantity_prod
                                        FROM order_product
                                        LEFT JOIN product ON product.id = order_product.product_id
                                        GROUP BY product_id")->fetch(PDO::FETCH_COLUMN);
            $this->template->quantity_product_orders = $quantity_product_orders;
            $total_page = ceil($total_quantity / $limit);
            $this->template->pagination =
                Pagination::factory(
                    array(
                        'total_items' => $total_page,
                        'items_per_page' => $this->_items_on_page,
                        'view' => 'extasy/pagination/basic',
                        'current_page'   => array('source' => 'query_string', 'key' => 'page'),
                    )
                )->render();
    }
    }

    public function action_clients_quantity_order()
    {
        $limit = 20;
        $from = $_GET['from'];
        $page =  $_GET['page'];
        $group = $_GET['group'];
        if(!$page) {
            $page = 1;
        }
        if(!$from) {
            $from = '';
        }
        if(!$group) {
            $group = 'email';
        }
        $this->template->from = $from;
        $this->template->group = $group;
        $PDO = ORM::factory('Orders')->PDO();
        $offset = $limit * $page - $limit;
        if($group == 'email') {
            $query_quantity_orders = " SELECT  SQL_CALC_FOUND_ROWS *,
                                             name,
                                             email,
                                             phone,
                                             COUNT(email) as quantity
                                            FROM orders
                                            GROUP BY email
                                            HAVING quantity >= '{$from}'
                                            ORDER BY quantity DESC LIMIT $offset, $limit";
        }else{
            $query_quantity_orders = " SELECT  SQL_CALC_FOUND_ROWS *,
                                             name,
                                             email,
                                             phone,
                                             COUNT(phone) as quantity
                                            FROM orders
                                            GROUP BY phone
                                            HAVING quantity >= '{$from}'
                                            ORDER BY quantity DESC LIMIT $offset, $limit";
        }
        $quantity_clients_orders = $PDO->query($query_quantity_orders)->fetchAll(PDO::FETCH_ASSOC);
        $rs1 = $PDO->query('SELECT FOUND_ROWS()');
        $total_product = (int) $rs1->fetchColumn();
        $this->template->orders_array = $quantity_clients_orders;
        $total_page = ceil($total_product / $limit);
        $this->template->pagination =
            Pagination::factory(
                array(
                    'total_items' => $total_page,
                    'items_per_page' => $this->_items_on_page,
                    'view' => 'extasy/pagination/basic',
                    'current_page'   => array('source' => 'query_string', 'key' => 'page'),
                )
            )->render();
    }

    public function action_revenue_for_period()
    {
        $from = $this->request->post('from');
        $to = $this->request->post('to');
        $PDO_product = ORM::factory('Orders')->PDO();
        $query = "SELECT orderproduct.prod_price,
                        ordercertificate.cert_price,
                        code_certificate,
                        coupons.discount,
                        order_certificate.to_amount
                        FROM orders
                      LEFT JOIN (SELECT order_id, product_id, SUM(quantity * price) as prod_price
                      FROM order_product
                      GROUP BY order_id) as orderproduct ON orderproduct.order_id = orders.id
                      LEFT JOIN
                      (SELECT order_id, SUM(price) as cert_price
                      FROM order_certificate
                      GROUP BY order_id) as  ordercertificate
                      ON orders.id = ordercertificate.order_id
                      LEFT JOIN  coupons ON coupons.code = orders.code_coupon
                      LEFT JOIN order_certificate ON order_certificate.code = orders.code_certificate
                       WHERE orders.created_at BETWEEN '{$from}' AND '{$to}' AND status = 4
                      ORDER BY orderproduct.prod_price + ordercertificate.cert_price DESC LIMIT  20";
        $result = $PDO_product->query($query)->fetchAll(PDO::FETCH_ASSOC);
        $quantity = 0;
        $result_price = 0;
        foreach($result as $item)
        {
            $price =  $item['prod_price'];
            if($item['discount'])
                $price = $item['prod_price'] - ($item['prod_price']/100 * $item['discount']);
            if($item['to_amount'])
                $price = $item['prod_price'] - $item['to_amount'];
            if($item['to_amount'] and $item['discount'])
                $price = ($item['prod_price'] - ($item['prod_price']/100 * $item['discount'])) - $item['to_amount'];
            $price += $item['cert_price'];
            $result_price += $price;
            $quantity ++;
        }
        $result_price = number_format($result_price, 0, '', ' ');
        exit(json_encode(array('quantity' => $quantity, 'result_price' => $result_price)));
    }

    public function action_autocomplete_article()
    {
        $search = $_POST['query'];
        $PDO = ORM::factory('Product')->PDO();
        $query = "SELECT id, article
                  FROM product
                  WHERE product.article
                  LIKE '%{$search}%' AND product.active = 1";
        $response = array();
        $product = $PDO->query($query)->fetchAll(PDO::FETCH_ASSOC);
        foreach($product as $row) {
            $response[] = array(
                'product_id' => $row['id'],
                'article' => $row['article']
            );
        }
        echo json_encode($response);
        exit;
    }
}