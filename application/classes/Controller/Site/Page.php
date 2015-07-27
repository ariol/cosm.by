<?php defined('SYSPATH') or die('No direct script access.');/** * @version SVN: $Id:$ */class Controller_Site_Page extends Controller_Site{    public function action_index()    {        $page_url = $this->param('url');        $this->set_metatags_and_content($page_url, 'page');		$this->template->set_layout('layout/site/global');    }    public function action_contacts()    {        $page_url = 'contacts';        $this->set_metatags_and_content($page_url, 'page');		$this->template->set_layout('layout/site/global');    }    public function action_saveFeedback()    {        $name = $this->request->post('name');        $email = $this->request->post('email');        $phone = $this->request->post('phone');        $message = $this->request->post('message');        $PDO = ORM::factory('Orders')->PDO();        $stmt = $PDO -> prepare("INSERT INTO feedback(name, email, text, phone) VALUES(:name, :email, :text, :phone)");        $stmt->bindParam(':name', $name);        $stmt->bindParam(':email', $email);        $stmt->bindParam(':text', $phone);        $stmt->bindParam(':phone', $message);        $stmt->execute();        $admin_message = View::factory('site/feedback/adminmessage', array(            'name' => $name,            'email' => $email,            'phone' => $phone,            'message' => $message        ))->render();        Helpers_Email::send(Kohana::$config->load('mailer.admin'), 'Обратная связь '.$name.' '.$phone, $admin_message, true);        exit(json_encode(array()));    }}