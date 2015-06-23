<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Site_Ajax extends Controller
{
    public function after(){}

    public function action_ajax_add_feedback()
    {
        if($_POST) {
            $errors = array(
                'name' => 'false',
                'text' => 'false',
                'email' => 'false',
                'check' => 'false',
				'phone' => 'false'
            );

            if(
                Validation::factory($_POST)
                    ->rule('email', 'email')
                    ->rule('email', 'not_empty')
                    ->check()
            ) {
                $errors['email'] = 'true';
            }
			
			if(
                Validation::factory($_POST)
                    ->rule('phone', 'not_empty')
                    ->check()
            ) {
                $errors['phone'] = 'true';
            }

            if(
                Validation::factory($_POST)
                    ->rule('name', 'not_empty')
                    ->check()
            ) {
                $errors['name'] = 'true';
            }

            if(
                Validation::factory($_POST)
                    ->rule('text', 'not_empty')
                    ->check()
            ) {
                $errors['text'] = 'true';
            }

            $check = arr::get($_POST, 'check');

            if (!$check) {
                $errors['check'] = 'true';
            }

            if($errors['name'] == 'true' && $errors['email'] == 'true' && $errors['phone'] == 'true'
                && $errors['text'] == 'true' && $errors['check'] == 'true') {

                $feedback = ORM::factory('Feedback');

                $feedback->name = arr::get($_POST, 'name');
				$feedback->phone = arr::get($_POST, 'phone');
                $feedback->email = arr::get($_POST, 'email');
                $feedback->text = arr::get($_POST, 'text');

                $feedback->save();

                Email::send('info@trip-shop.by', array('info@trip-shop.by', 'Trip-Shop'),
                    'Новый отзыв',
                    'Имя - '.arr::get($_POST, 'name').'<br/>'.
                    'Email - '.arr::get($_POST, 'email').'<br/>'.
					'Телефон - '.arr::get($_POST, 'phone').'<br/>'.
                    arr::get($_POST, 'text'),
                    /*html*/true
                );
            }

            echo json_encode($errors);
        }
        else {
            $this->forward_404();
        }
    }
}
