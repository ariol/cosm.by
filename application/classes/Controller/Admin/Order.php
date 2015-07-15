<?php defined('SYSPATH') or die('No direct script access.');/** * @version SVN: $Id:$ */class Controller_Admin_Order extends Controller_Crud{    protected $_model = 'Orders';    public function before_fetch(ORM $item)    {        if (isset($_GET['cancel_filter']))        {            $this->redirect('/' . Extasy_Url::url_to_route($this->get_index_route()));        }        $filter_form = new Form_Filter_Order($item);        if (isset($_GET['filter']))        {            $filter_form->submit();        }        $this->template->filter_form = $filter_form;        return parent::before_fetch($item);    }	protected $_group_actions = array(		'delete' => array(			'handler' => 'delete_routine',			'title' => '<i class="fa fa-trash-o"></i> Удалить',			'confirm' => 'Вы уверены?',			'class' => 'btn-danger',			'one_item' => TRUE		)	);    public function action_create()    {    }	public function action_view()	{		$order = ORM::factory('Orders', $this->request->param('id'));        $PDO_orderProduct = ORM::factory('OrderProduct')->PDO();        $order_product = "SELECT order_product.id, order_product.order_id,                              order_product.product_id, order_product.quantity, order_product.price, color                              FROM order_product                          WHERE order_id = '{$order->id}'";        $PDO_orderCertificate = ORM::factory('OrderCertificate')->PDO();        $order_certificate = "SELECT id, order_id, certificate_id, price, code                              FROM order_certificate                          WHERE order_id = '{$order->id}'";        $PDO_coupons = ORM::factory('Coupons')->PDO();        $discount_coupon = "SELECT discount, time_end                            FROM coupons WHERE code = '{$order->code_coupon}'";        $coupon = $PDO_coupons->query($discount_coupon)->fetch(PDO::FETCH_ASSOC);            $discount = $coupon['discount'];        $coupon_order = $PDO_coupons->query ("SELECT discount, code, time_end                            FROM coupons WHERE order_id = '{$order->id}'")->fetch(PDO::FETCH_ASSOC);;        $PDO_certificate = ORM::factory('Certificate')->PDO();        $to_amount_query = $discount_coupon = "SELECT to_amount                            FROM order_certificate WHERE code = '{$order->code_certificate}'";        $to_amount = $PDO_certificate->query($to_amount_query)->fetchAll(PDO::FETCH_ASSOC);        foreach($to_amount as $item) {            $amount = $item['to_amount'];        }        $this->template->order_product = $PDO_orderProduct->query($order_product)->fetchAll(PDO::FETCH_ASSOC);        $this->template->order_certificate = $PDO_orderCertificate->query($order_certificate)->fetchAll(PDO::FETCH_ASSOC);        $this->template->discount = $discount;        $this->template->time_end =  $coupon_order['time_end'];        $this->template->coupon_order = $coupon_order['discount'];        $this->template->coupon_code = $coupon_order['code'];        $this->template->amount = $amount;		$this->template->order = $order;	}	public function action_print_page()	{		$order = ORM::factory('Orders', $this->request->param('id'));        $PDO_orderProduct = ORM::factory('OrderProduct')->PDO();        $order_product = "SELECT order_product.id, order_product.order_id,                              order_product.product_id, order_product.quantity, order_product.price, color                              FROM order_product                          WHERE order_id = '{$order->id}'";        $PDO_orderCertificate = ORM::factory('OrderCertificate')->PDO();        $order_certificate = "SELECT id, order_id, certificate_id, price, code                              FROM order_certificate                          WHERE order_id = '{$order->id}'";        $PDO_coupons = ORM::factory('Coupons')->PDO();        $discount_coupon = "SELECT discount                            FROM coupons WHERE code = '{$order->code_coupon}'";        $coupon = $PDO_coupons->query($discount_coupon)->fetch(PDO::FETCH_ASSOC);            $discount = $coupon['discount'];        $coupon_order = $PDO_coupons->query ("SELECT discount, code                            FROM coupons WHERE order_id = '{$order->id}'")->fetch(PDO::FETCH_ASSOC);;        $PDO_certificate = ORM::factory('Certificate')->PDO();        $to_amount_query = $discount_coupon = "SELECT to_amount                            FROM order_certificate WHERE code = '{$order->code_certificate}'";        $to_amount = $PDO_certificate->query($to_amount_query)->fetchAll(PDO::FETCH_ASSOC);        foreach($to_amount as $item) {            $amount = $item['to_amount'];        }        $this->template->order_product = $PDO_orderProduct->query($order_product)->fetchAll(PDO::FETCH_ASSOC);        $this->template->order_certificate = $PDO_orderCertificate->query($order_certificate)->fetchAll(PDO::FETCH_ASSOC);        $this->template->discount = $discount;        $this->template->coupon_order = $coupon_order['discount'];        $this->template->coupon_code = $coupon_order['code'];        $this->template->amount = $amount;		$this->template->order = $order;	}	public function action_edit_order()	{        $order = ORM::factory('Orders', $this->request->param('id'));        $PDO_orderProduct = ORM::factory('OrderProduct')->PDO();        $order_product = "SELECT order_product.id, order_product.order_id,                              order_product.product_id, order_product.quantity, order_product.price, order_product.color                              FROM order_product                          WHERE order_id = '{$order->id}'";        $product = $PDO_orderProduct->query($order_product)->fetchAll(PDO::FETCH_ASSOC);        $cart_items = Session::instance()->get('cart');        $items = array();        foreach($product as $item)        {            $items[$item['product_id']] = array(                'id' => $item['product_id'],                'quantity' => $item['quantity'],                'price' => $item['price'],                'color' => $item['color']            );        }        $cart_items['cart'] = json_encode($items);        Session::instance()->set('cart', $cart_items);        $cart = Session::instance()->get('cart');        $cartitems = json_decode($cart_items['cart']);        $PDO_orderCertificate = ORM::factory('OrderCertificate')->PDO();        $order_certificate = "SELECT certificate_id, id, price, COUNT(certificate_id) as quantity                                FROM order_certificate                                 WHERE order_id = '{$order->id}' GROUP BY certificate_id ";        $certificate = $PDO_orderCertificate->query($order_certificate)->fetchAll(PDO::FETCH_ASSOC);        $cart_certificate = Session::instance()->get('cart_certificate');        $certificate_items = [];        foreach($certificate as $item)        {            $certificate_items[$item['certificate_id']] = array(                'id' => $item['certificate_id'],                'quantity' => $item['quantity'],                'price' => $item['price']            );        }         if($order->code_coupon) {             $PDO_coupon = ORM::factory('Coupons')->PDO();             $coupons_query =$PDO_coupon->query( "SELECT discount FROM coupons WHERE code = '{$order->code_coupon}'")->fetchAll();             foreach($coupons_query as $discount_coupons)             {                 $discount = $discount_coupons['discount'];             }         }         if($order->code_certificate) {             $PDO_certificate = ORM::factory('OrderCertificate')->PDO();             $certificate_query =$PDO_certificate->query("SELECT to_amount FROM order_certificate WHERE code = '{$order->code_certificate}'")->fetchAll();             foreach($certificate_query as $certificate_to_amount)             {                 $to_amount = $certificate_to_amount['to_amount'];             }         }        $cart_certificate['cart_certificate'] = json_encode($certificate_items);        Session::instance()->set('cart_certificate', $certificate_items);        $certificate = json_decode($cart_certificate['cart_certificate']);        $this->template->cartitems = $cartitems;        $this->template->order_certificate = $certificate;        $this->template->order = $order;        $this->template->discount = $discount;        $this->template->to_amount = $to_amount;    }    public function action_change_order()    {        if ($this->request->is_ajax()) {            $order_id = $this->request->post('order_id');            $name = $this->request->post('name');            $email = $this->request->post('email');            $phone = $this->request->post('phone');            $adress = $this->request->post('adress');            $code = $this->request->post('coupon');            $city = $this->request->post('city');            $index = $this->request->post('index');            $delivery = $this->request->post('delivery');            $comment = $this->request->post('comment');            $code_certificate = $this->request->post('certificate');            $active = 0;            $cart = Session::instance()->get('cart');            $certificate = Session::instance()->get('cart_certificate');            $cartitems = json_decode($cart['cart']);            $certificateitems = json_decode($certificate['cart_certificate']);            $PDO = ORM::factory('Orders')->PDO();            $date = date('Y-m-d');            $dataCertificate = $PDO->query("SELECT order_certificate.code, to_amount                                            FROM order_certificate                                            WHERE code = '{$code_certificate}' AND time_end > '{$date}'")->fetch();            $dataCoupon = $PDO->query("SELECT coupons.code, coupons.discount                                            FROM coupons                                            WHERE code = '{$code}'  AND time_end > '{$date}'")->fetch();            $query= "UPDATE  orders SET orders.name = '{$name}',                                              orders.email = '{$email}',                                              orders.phone = '{$phone}',                                              orders.adress = '{$adress}',                                              orders.code_coupon = '{$code}',                                              orders.code_certificate = '{$code_certificate}',                                              orders.delivery = '{$delivery}',                                              orders.city = '{$city}',                                              orders.index = '{$index}',                                              orders.comment = '{$comment}'                                               WHERE orders.id = '{$order_id}'";            $PDO->exec($query);            $delete_query = "DELETE FROM order_product WHERE order_id = '{$order_id}'";            $PDO->query($delete_query);            $stmt = $PDO -> prepare("INSERT INTO order_product (order_id, price, product_id, quantity, color)                                      VALUES(:order_id, :price, :product_id, :quantity, :color)");            foreach ($cartitems as $items) {                if($items->color){                    $color = $items->color;                }else{                    $color = "";                }                $stmt->bindParam(':price', $items->price, PDO::PARAM_INT);                $stmt->bindParam(':product_id', $items->id, PDO::PARAM_INT);                $stmt->bindParam(':quantity', $items->quantity, PDO::PARAM_INT);                $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);                $stmt->bindParam(':color', $color);                $stmt->execute();            }                $delete_query_certificate = "DELETE FROM order_certificate WHERE order_id = '{$order_id}'";                $PDO->query($delete_query_certificate);                foreach ($certificateitems as $crcitems) {                    $validity = $PDO->query("SELECT certificate.validity, certificate.sum                                                                           FROM certificate                                                                           WHERE id = '{$crcitems->id}'")->fetch();                    $i = 0;                    while($i < $crcitems->quantity){                        $certificate = $PDO->prepare("INSERT INTO order_certificate (certificate_id,                                                                                      order_id,                                                                                      code,                                                                                      price,                                                                                      time_end,                                                                                      to_amount,                                                                                      active)                                                                            VALUES(:certificate_id,                                                                                    :order_id,                                                                                    :certificate_code,                                                                                    :price,                                                                                    :time_end,                                                                                    :to_amount,                                                                                    :active)");                        $certificate_code = substr(md5(microtime()), rand(0,5), rand(11,16));                        $certificate->bindParam(':certificate_id', $crcitems->id, PDO::PARAM_INT);                        $certificate->bindParam(':order_id', $order_id, PDO::PARAM_INT);                        $certificate->bindParam(':price', $crcitems->price, PDO::PARAM_INT);                        $certificate->bindParam(':certificate_code', $certificate_code, PDO::PARAM_STR);                        $certificate->bindParam(':time_end', $validity['validity']);                        $certificate->bindParam(':to_amount', $validity['sum']);                        $certificate->bindParam(':active', $active);                        $certificate->execute();                        $i++;                    }                }            $stmt = $PDO->prepare("UPDATE coupons SET active = :active WHERE code = :code");            $stmt->bindParam(':code', $code);            $stmt->bindParam(':active', $active);            $stmt->execute();            $cart_mail = $cart['cart'];            if($email) {                $user_message = View::factory('admin/order/usermessage', array(                    'name' => $name,                    'email' => $email,                    'phone' => $phone,                    'adress' => $adress,                    'city' => $city,                    'index' => $index,                    'code_certificate' => $code_certificate,                    'code' => $code,                    'delivery' => $delivery,                    'to_amount' => $dataCertificate['to_amount'],                    'coupon_discount' => $dataCoupon['discount'],                    'cart' => json_decode($cart_mail),                    'cert' => $certificateitems                ))->render();            }            $admin_message = View::factory('admin/order/adminmessage', array(                'name' => $name,                'email' => $email,                'phone' => $phone,                'adress' => $adress,                'city' => $city,                'index' => $index,                'code_certificate' => $code_certificate,                'code' => $code,                'delivery' => $delivery,                'to_amount' => $dataCertificate['to_amount'],                'coupon_discount' => $dataCoupon['discount'],                'cart' => json_decode($cart_mail),                'cert' => $certificateitems            ))->render();            //Helpers_Email::send(Kohana::$config->load('mailer.admin'), 'Новый заказ '.$name.' '.$phone, $admin_message, true);            //Helpers_Email::send($email, 'Новый заказ '.$name.' '.$phone, $user_message, true);            exit(json_encode(array('order_id'=>$order_id, 'user_message' => $user_message, 'admin_message' => $admin_message)));        }        $this->forward_404();    }    public function action_cancel()    {        $code_coupon = $this->request->post('code_coupon');        $code_certificate = $this->request->post('code_certificate');        $active = 1;        if($code_coupon) {            $PDO = ORM::factory('Coupons')->PDO();            $stmt = $PDO->prepare("UPDATE coupons SET active = :active WHERE code = :code");            $stmt->bindParam(':code', $code_coupon);            $stmt->bindParam(':active', $active);            $stmt->execute();        }        if($code_certificate) {            $PDO = ORM::factory('OrderCertificate')->PDO();            $stmt = $PDO->prepare("UPDATE order_certificate SET active = :active WHERE code = :code");            $stmt->bindParam(':code', $code_certificate);            $stmt->bindParam(':active', $active);            $stmt->execute();        }        exit(json_encode(array()));    }    public function action_empty_cart()    {        $products_s = Session::instance()->get('cart');        $cart = json_decode($products_s['cart']);        $items = array();        foreach ($cart as $key => $item) {            if($item->id) {                $items[$key] = array(                    'id' => '',                    'quantity' => '',                    'price' => ''                );            }        }        $products_s['cart'] = json_encode($items);        $cart = Session::instance()->get('cart');        $cartitems = json_decode($cart['cart']);        Session::instance()->set('cart', $products_s);        $cart = Session::instance()->get('cart');        $cartitems = json_decode($cart['cart']);        exit(json_encode(array('cartitems' => $cartitems)));    }    public function action_empty_certificate_cart()    {        $products_s = Session::instance()->get('cart_certificate');        $cart = json_decode($products_s['cart_certificate']);        $items = array();        foreach ($cart as $key => $item) {            if($item->id) {                $items[$key] = array(                    'id' => '',                    'quantity' => '',                    'price' => ''                );            }        }        $products_s['cart_certificate'] = json_encode($items);        $cart = Session::instance()->get('cart_certificate');        $cartitems = json_decode($cart['cart_certificate']);        Session::instance()->set('cart_certificate', $products_s);        $cart = Session::instance()->get('cart_certificate');        $cartitems = json_decode($cart['cart_certificate']);        exit(json_encode(array('cartitems' => $cartitems)));    }    public function action_recount()    {        $quantity = $_POST['quantity'];        $price_start = $_POST['price'];        $id = $_POST['id'];        $to_amount = $_POST['to_amount'];        $discount = $_POST['discount'];        $price = $quantity * $price_start;        $cart_items = Session::instance()->get('cart');        $array_key = $id;        if (isset($cart_items['cart'])) {            $cart = json_decode($cart_items['cart']);        } else {            $cart = array();        }        $items = array();        if ($cart) {            foreach ($cart as $key => $item) {                $items[$key] = array(                    'id' => $item->id,                    'quantity' => $item->quantity,                    'price' => $item->price,                    'color' => $item->color                );            }        }        if (isset($items[$array_key])) {            $items[$array_key]['quantity'] = $quantity;        }else {            $items[$array_key] = array(                'id' => $array_key,                'quantity' => $quantity,                'price' => $price_start            );        }        $cart_items['cart'] = json_encode($items);        Session::instance()->set('cart', $cart_items);        $cart = Session::instance()->get('cart');        $cartitems = json_decode($cart['cart']);        $last_price = 0;        if ($cartitems) {            foreach ($cartitems as $key => $item) {                if ($item->quantity > 1) {                    $price_quantity = $item->price * $item->quantity;                } else {                    $price_quantity = $item->price;                }                $last_price += $price_quantity;            }            if($discount) {                $last_price -= ($last_price / 100 * $discount);            }            if($to_amount){                $last_price -= $to_amount;            }            if($last_price < 0) {                $last_price = 0;            }            $last_price_view = number_format($last_price, 0, '', ' ');        }        $price_view = number_format( $price, 0, '', ' ');        exit(json_encode(array(            'id' => $id,            'price' => $price,            'price_view' => $price_view,            'last_price_view' => $last_price_view,            'last_price' => $last_price            )));    }    public function action_recount_certificate()    {        $quantity = $_POST['quantity'];        $price_start = $_POST['price'];        $id = $_POST['id'];        $last_price = 0;        $array_key = $id;        $price = $quantity * $price_start;        $certificate_items = Session::instance()->get('cart_certificate');        if (isset($certificate_items['cart_certificate'])) {            $cart = json_decode($certificate_items['cart_certificate']);        } else {            $cart = array();        }        $items = array();        if ($cart) {            foreach ($cart as $key => $item) {                $items[$key] = array(                    'id' => $item->id,                    'quantity' => $item->quantity,                    'price' => $item->price                );            }        }        if (isset($items[$array_key])) {            $items[$array_key]['quantity'] = $quantity;        } else {            $items[$array_key] = array(                'id' => $array_key,                'quantity' => $quantity,                'price' => $price_start            );        }        $certificate_items['cart_certificate'] = json_encode($items);        Session::instance()->set('cart_certificate', $certificate_items);        $cart_certificate = Session::instance()->get('cart_certificate');        $certificate = json_decode($cart_certificate['cart_certificate']);        if ($certificate) {            foreach ($certificate as $key => $item) {                if ($item->quantity > 1) {                    $price_quantity = $item->price * $item->quantity;                } else {                    $price_quantity = $item->price;                }                $last_price += $price_quantity;            }            $last_price_view = number_format($last_price, 0, '', ' ');            $price_view = number_format( $price, 0, '', ' ');        }        exit(json_encode(array('id' => $id,            'last_price_view' => $last_price_view,            'price' => $price,            'price_view' => $price_view)));    }    public function action_last_certificate()    {        $certificate_items = Session::instance()->get('cart_certificate');        $items = null;        $certificate_items = json_encode($items);        Session::instance()->set('cart_certificate', $certificate_items);        exit(json_encode(array()));    }    public function action_autocomplete()    {        $search = $_POST['query'];        $ignore_ids = $_POST['ignore_ids'];        $PDO = ORM::factory('Product')->PDO();        $query = "SELECT price, product.name, id, article, new_price, color, volume                  FROM product                  WHERE product.name                  LIKE '%{$search}%' OR product.article LIKE '%{$search}%' AND product.active = 1";        if ($ignore_ids) {            $query .= " AND product.id NOT IN ('" . join("', '", $ignore_ids) . "')";        }        $response = array();        $product = $PDO->query($query)->fetchAll(PDO::FETCH_ASSOC);        foreach($product as $row) {            if($row['new_price'])                $price = $row['new_price'];            else                $price = $row['price'];            $price_view = number_format($price, 0, '', ' ');            $response[] = array(                'product' => $row['name'],                'product_id' => $row['id'],                'price_view' =>$price_view,                'price' => $price,                'article' => $row['article'],                'ignore_ids' => $ignore_ids,                'color' => $row['color'],                'volume' => $row['volume'],                'article' => $row['article']            );        }        echo json_encode($response);        exit;    }    public function action_certificate()    {        $search = $_POST['certificate'];        $ignore_ids_certificate = $_POST['ignore_ids_certificate'];        $PDO = ORM::factory('Certificate')->PDO();        $query = "SELECT id, certificate.name, price, certificate.sum                  FROM certificate                  WHERE certificate.name                  LIKE '%{$search}%' AND certificate.active = 1";        if ($ignore_ids_certificate) {            $query .= " AND certificate.id NOT IN ('" . join("', '", $ignore_ids_certificate) . "')";        }        $response = array();        $product = $PDO->query($query)->fetchAll(PDO::FETCH_ASSOC);        foreach($product as $row) {            $price = $row['price'];            $price_view = number_format($row['price'], 0, '', ' ');            $response[] = array(                'product' => $row['name'],                'product_id' => $row['id'],                'price_view' =>$price_view,                'price' => $price,                'ignore_ids_certificate' => $ignore_ids_certificate            );        }        echo json_encode($response);        exit;    }    public function action_autocomplete_coupon()    {        $search = $_POST['code_coupon'];        $date  = date('Y-m-d');        $PDO = ORM::factory('Coupons')->PDO();        $query = "SELECT coupons.code, coupons.discount                    FROM coupons                    WHERE code LIKE '%{$search}%' AND active = 1 AND time_end > '{$date}'";        $response = array();        $coupon = $PDO->query($query)->fetchAll(PDO::FETCH_ASSOC);        foreach($coupon as $row) {            $response[] = array(                'code_coupon' => $row['code'],                'discount_coupon' => $row['discount'],            );        }        echo json_encode($response);        exit;    }    public function action_autocomplete_certificate()    {        $search = $_POST['code_certificate'];        $date  = date('Y-m-d');        $PDO = ORM::factory('OrderCertificate')->PDO();        $query = "SELECT order_certificate.code, order_certificate.to_amount                    FROM order_certificate                   WHERE code LIKE '%{$search}%' AND active = '1' AND time_end > '{$date}' ";        $response = array();        $certificate = $PDO->query($query)->fetchAll(PDO::FETCH_ASSOC);        foreach($certificate as $row) {            $response[] = array(                'code_certificate' => $row['code'],                'to_amount' => $row['to_amount'],            );        }        echo json_encode($response);        exit;    }}