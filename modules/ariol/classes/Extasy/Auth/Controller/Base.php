<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Extasy_Auth_Controller_Base extends Controller_Auth
{
	public function before()
	{
		if ($this->request->action() == 'login')
		{
			$this->_login_route = NULL;
		}
		parent::before();
	}

	protected function do_login()
	{
		if ($this->request->is_ajax() && $_POST) {
			$this->do_auth();
		}

		$this->template->set_layout('layout/admin/login');

		$this->template->email = '';
		$this->template->remember = false;
		$this->template->error = '';

		$this->template->return = arr::get($_GET, 'return', arr::get($_POST, 'return', FALSE));

		if(isset($_POST['login']))
		{
			$email = $_POST['email'];
			$password = $_POST['password'];
			$remember = $_POST['remember'];
			$this->template->email = $email;
			$this->template->remember = $remember;
			if(Auth::instance()->login($email, $password, (bool)$remember))
			{
				if ($this->template->return)
				{
					HTTP::redirect($this->template->return);
				}
				else
				{
					return TRUE;
				}
			}
			if (Auth::instance()->is_banned())
			{
				$banned_to = Auth::instance()->get_banned_to();
				$this->template->error = 'Аккаунт заблокирован до '.date('Y-m-d H:i', $banned_to).' (до разблокировки '.(ceil(($banned_to - time()) / 3600)).' ч '.date('i мин', $banned_to - time()).')';
			}
			else
			{
				$this->template->error = 'Неверные e-mail или пароль';
			}
			return FALSE;
		}
	}

	public function do_auth()
	{
		$email = $this->request->post('email');
		$password = $this->request->post('password');
		$remember = $this->request->post('remember');
		$return = $this->request->post('return') ? $this->request->post('return') : '/ariol-admin';

		Auth::instance()->login($email, $password, $remember);
		if (Auth::instance()->logged_in() && Auth::instance()->get_user()->roles == 1) {
			Json::response(array('status' => 1, 'return' => $return));
		}
		else {
			$this->do_logout();
			Json::response(array('status' => 2));
		}
	}

	protected function do_logout()
	{
		Auth::instance()->logout();
		return TRUE;
	}

	public function action_block()
	{
		$this->template->set_layout(NULL);

		$this->set_view($this->request->directory().'/'.$this->request->controller().'/'.$this->request->action());

		$this->template->is_logged = FALSE;
		if (Auth::instance()->logged_in())
		{
			$this->template->username = $this->user()->name;
			$this->template->is_logged = TRUE;
		}
	}

	protected function do_change_password()
	{
		$user = $this->user();
		$form = new Form_Login_ChangePassword($user);

		$this->template->form = $form;

		if (isset($_POST['cancel']))
		{
			return TRUE;
		}

		return isset($_POST['submit']) AND $form->submit();
	}

	protected function do_reset_password_step_1()
	{
		$form = new CM_Form();
		$form->set_field('email', new CM_Field_String(), 10);
		$form->get_field('email')->set_label('E-Mail');

		$validate = Validation::factory(array());

		$validate->rules('email', array(
			array('not_empty'),
			array('email')
		));

		$form->add_plugin(new CM_Form_Plugin_Validate($validate));

		if (isset($_POST['submit']) AND $form->submit())
		{
			$email = $form->get_field('email')->get_value()->get_raw();

			$user = ORM::factory('User', array(
				'email' => $email
			));

			if ( ! $user->loaded())
			{
				$form->set_error('email', 'Такой e-mail не зарегистрирован в системе');
			}
			else
			{
				$code = md5(rand() + time());
				$user->reset_password_code = $code;
				$user->save();

				return array(
					'email' => $user->email,
					'code' => $code
				);
			}
		}

		$this->template->form = $form;
		return FALSE;
	}

	public function action_reset_password_step_1_thankyou()
	{

	}

	protected function do_reset_password_step_2()
	{
		$code = $this->param('code');

		if ( ! $code)
		{
			return TRUE;
		}

		$user = ORM::factory('User', array(
			'reset_password_code' => $code
		));

		if ( ! $user->loaded())
		{
			return TRUE;
		}

		$form = new Form_User_ChangePassword($user);

		if (isset($_POST['submit']) AND $form->submit())
		{
			$user->reset_password_code = NULL;
			$user->save();

			Auth::instance()->force_login($user);

			return TRUE;
		}

		$this->template->form = $form;
	}
}