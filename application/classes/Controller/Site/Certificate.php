<?php defined('SYSPATH') or die('No direct script access.');



class Controller_Site_Certificate extends Controller_Site

{

    public function action_index()
    {
        $this->set_metatags_and_content('', 'page');
        $certificate = ORM::factory('Certificate')->fetcCertificateForMmain()->as_array();
        $this->template->certificate = $certificate;
    }

    public function action_product()
    {
        $this->set_metatags_and_content($this->param('url'), 'certificate');
        $this->template->set_layout('layout/site/global');
    }

    public function action_add()
    {
        $this->set_metatags_and_content('', 'page');
        if ($this->request->is_ajax()) {
            $id = $this->request->post('id');
            $quantity = $this->request->post('quantity');
            $price = $this->request->post('price');
            $cart_items = Session::instance()->get('cart_certificate');
            $array_key = $id;
            $cart_sertificate = array();
            if (isset($cart_items['cart_certificate'])) {
                $cart_certificate = json_decode($cart_items['cart_certificate']);
            }
            $items = array();
            if ($cart_certificate) {
                foreach ($cart_certificate as $key => $item) {
                    $items[$key] = array(
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    );
                }
            }
            if (isset($items[$array_key])) {
                $items[$array_key]['quantity'] += $quantity;
            } else {
                $items[$array_key] = array(
                    'id' => $array_key,
                    'quantity' => $quantity,
                    'price' => $price
                );
            }
            $cart_items['cart_certificate'] = json_encode($items);
            Session::instance()->set('cart_certificate', $cart_items);
            $cart_sertificate = Session::instance()->get('cart_certificate');
            $cartitems = json_decode($cart_sertificate['cart_certificate']);
            $cart_session = Session::instance()->get('cart');
            $cart = json_decode($cart_session['cart']);
            $pr_quantity = 0;
            if($cart){
                foreach($cart as $cnt){
                    $pr_quantity = $cnt->quantity + $pr_quantity;
                }
            }
            $result_quantity = 0;
            $result_price = 0;
            if($cartitems){
                foreach($cartitems as $key => $item){
                    if($item->quantity > 1){
                        $result_price = $item->price*$item->quantity + $result_price;
                    } else {
                        $result_price = $item->price + $result_price;
                    }
                    $result_quantity = $item->quantity + $result_quantity;
                }
                $result_quantity +=  $pr_quantity;
                $prie_view = number_format($result_price, 0, '', ' ');
                exit(json_encode(array('price' => $result_price, 'quantity' => $result_quantity, 'prie_view' => $prie_view)));
            }
        }
        $this->forward_404();
    }

    public function action_delete()
    {
        $this->set_metatags_and_content('', 'page');
        $this->template->set_layout('site/global');
        if ($this->request->is_ajax()) {
            $id = $this->request->post('id');
            $products_s = Session::instance()->get('cart_certificate');
            $cart = json_decode($products_s['cart_certificate']);
            $items = array();
            foreach ($cart as $key => $item) {
                if($id!=$item->id) {
                    $items[$key] = array(
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    );
                }
            }
            $products_s['cart_certificate'] = json_encode($items);
            Session::instance()->set('cart_certificate', $products_s);
            $cart_certificate = Session::instance()->get('cart_certificate');
            $cartitems = json_decode($cart_certificate['cart_certificate']);
            $result_quantity = 0;
            $result_price = 0;
            $cart_product_session = Session::instance()->get('cart');
            $cart_product = json_decode($cart_product_session['cart']);
            $pr_price = 0;
            $pr_quantity = 0;
            if($cart_product){
                foreach($cart_product as $product){
                    $pr_quantity = $product->quantity + $pr_quantity;
                    $pr_price =$product->price * $product->quantity + $pr_price;
                }
            }
            if($cartitems) {
                foreach($cartitems as $key => $items) {
                    if ($items->quantity > 1) {
                        $result_price = $items->price * $items->quantity + $result_price;
                    } else {
                        $result_price = $items->price + $result_price;
                    }
                    $result_quantity = $items->quantity + $result_quantity;
                }
            }
            $result_quantity +=  $pr_quantity;
            $result_price +=  $pr_price;
            $price_view = number_format($result_price, 0, '', ' ');
            exit(json_encode(array('price_view' =>$price_view, 'quantity' => $result_quantity, 'id' => $id)));
        }
        $this->forward_404();
    }



    public function action_empty_certificate_cart()
    {
        $products_s = Session::instance()->get('cart_certificate');
        $cart = json_decode($products_s['cart_certificate']);
        $items = array();
        foreach ($cart as $key => $item) {
            if($item->id) {
                $items[$key] = array(

                    'id' => '',

                    'quantity' => '',

                    'price' => ''
                );
            }
        }
        $products_s['cart_certificate'] = json_encode($items);
        $cart = Session::instance()->get('cart_certificate');
        $cartitems = json_decode($cart['cart_certificate']);
        Session::instance()->set('cart_certificate', $products_s);
        $cart = Session::instance()->get('cart_certificate');
        $cartitems = json_decode($cart['cart_certificate']);
        exit(json_encode(array('cartitems' => $cartitems)));

    }

    public function action_recount()
    {
        $this->set_metatags_and_content('', 'page');
        if ($this->request->is_ajax()) {
            $id = $this->request->post('id');
            $quantity = $this->request->post('quantity');
            $cart_items = Session::instance()->get('cart_certificate');
            $array_key = $id;
            if (isset($cart_items['cart_certificate'])) {
                $cart = json_decode($cart_items['cart_certificate']);
            } else {
                $cart = array();
            }
            $items = array();
            if ($cart) {
                foreach ($cart as $key => $item) {
                    $items[$key] = array(
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    );
                }
            }
            if (isset($items[$array_key])) {
                $items[$array_key]['quantity'] = $quantity;
            }
            $cart_items['cart_certificate'] = json_encode($items);
            Session::instance()->set('cart_certificate', $cart_items);
            $cart = Session::instance()->get('cart_certificate');
            $cartitems = json_decode($cart['cart_certificate']);
            $result_quantity = 0;
            $result_price = 0;
            if($cartitems){
                foreach($cartitems as $key => $item){
                    if($item->quantity > 1){
                        $result_price = $item->price*$item->quantity + $result_price;
                    } else {
                        $result_price = $item->price + $result_price;
                    }
                    $result_quantity = $item->quantity + $result_quantity;
                    $prodprice = $quantity * $item->price;
                }
                exit(json_encode(array('price' => $result_price, 'quantity' => $result_quantity, 'quantity_prod' => $quantity, 'prodprice' => $prodprice)));
            }
        }
        $this->forward_404();
    }

}