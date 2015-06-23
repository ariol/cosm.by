<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Site_Qa extends Controller_Site
{
    public function action_addq()
    {
		$this->set_metatags_and_content('', 'page');
		$this->template->set_layout('layout/site/global_inner');
		if ($this->request->is_ajax()) {
			$name = $this->request->post('name_qa');
			$phone = $this->request->post('phone_qa');
			$question = $this->request->post('qa');
			$product_id = $this->request->post('product_id');
			$product_qa = $this->request->post('product_qa');
			
			
			$admin_message = View::factory('site/question/adminmessage', array(
				'name' => $name,
				'phone' => $phone,
				'question' => $question,
				'product_id' => $product_id,
				'product_qa' => $product_qa,
			))->render();
			
			Email::send('tripshopby@gmail.com', 'info@trip-shop.by', 'Новый вопрос '.$name.' '.$phone, $admin_message, true);

						
			exit(json_encode(array('admin_message' => $admin_message)));
		}
		$this->forward_404();
    }
}
