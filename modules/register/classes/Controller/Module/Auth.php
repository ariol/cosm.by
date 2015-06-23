<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Module_Auth extends Controller_Site
{
    public function action_get_media()
    {
        $this->template->set_layout('site/module_auth/layout');

        $this->template->media = file_get_contents(realpath(MODPATH .'register/media/script.js'));
    }

    public function action_get_light_media()
    {
        $this->template->set_layout('site/module_auth/layout');

        $this->template->media = file_get_contents(realpath(MODPATH .'register/media/register_light.js'));
    }

    public function action_logout()
    {
        Auth::instance()->logout();

        $this->redirect('/');
    }

    public function action_ajax_register()
    {
        if ($this->request->is_ajax()) {
            $login = trim($this->request->post('login'));
            $password = trim($this->request->post('password'));
            $email = trim($this->request->post('email'));
            $type = $this->request->post('type');

            $role = $type == 'retailer' ? 3 : 2;

            $emptyPassword = !$password;
            $emptyLogin = !$login;
            $invalidEmail = !filter_var($email, FILTER_VALIDATE_EMAIL);

            $emailExists = ORM::factory('User')->where('email', '=', $email)->find();
            $loginExists = ORM::factory('User')->where('name', '=', $login)->find();

            $errors = array(
                'empty_password' => $emptyPassword,
                'empty_login' => $emptyLogin,
                'invalid_email' => $invalidEmail,
                'email_exists' => $emailExists->loaded(),
                'login_exists' => $loginExists->loaded()
            );

            $errors_exists = false;

            foreach ($errors as $error) {
                if ($error) {
                    $errors_exists = true;
                }
            }

            if (!$errors_exists) {
                $token = md5(time() . $login . $email);

                $user = ORM::factory('User');
                $user->name = $login;
                $user->username = md5($login);
                $user->email = $email;
                $user->roles = $role;
                $user->password = $password;
                $user->register_token = $token;
                $user->save();

                $message = sprintf("Спасибо за регистрацию <br/>" .
                            "Ваш логин: %s <br/>" .
                            "Ваш пароль: %s <br/>" .
                            "Ваш email: %s <br/>" .
                            "Ссылка для активации: %s",
                    $login, $password, $email,
                    HTML::anchor(URL::base('http') . 'module_auth/token?email=' . $email .'&token=' . $token)
                );

                Helpers_Email::send($email, 'Регистрация', $message, true);
            }

            echo json_encode(
                array(
                    'errors' => $errors,
                    'errors_exists' => $errors_exists
                )
            );
        }

        exit();
    }

    public function action_ajax_light_register()
    {
        if ($this->request->is_ajax()) {
            $email = trim($this->request->post('email'));

            $role = 2;

            $invalidEmail = !filter_var($email, FILTER_VALIDATE_EMAIL);
            $emailExists = ORM::factory('User')->where('email', '=', $email)->find();

            $errors = array(
                'invalid_email' => $invalidEmail,
                'email_exists' => $emailExists->loaded(),
            );

            $textErrors = array();

            $errors_exists = false;

            foreach ($errors as $key => $error) {
                if ($error) {
                    $errors_exists = true;
                }
            }

            if ($errors['invalid_email']) {
                $textErrors[] = 'Неверный формат email адреса!';
            }
            if ($errors['email_exists']) {
                $textErrors[] = 'Данный email адрес занят!';
            }

            if (!$errors_exists) {
                $token = md5(time() . $email);

                $emailParts = explode('@', $email);

                $password = Text::limit_chars(md5(time() . 'hello world' . $email), 8, '');

                $user = ORM::factory('User');
                $user->name = Arr::get($emailParts, 0);
                $user->username = Arr::get($emailParts, 0);
                $user->email = $email;
                $user->roles = $role;
                $user->password = $password;
                $user->register_token = $token;
                $user->save();

                $message = sprintf("Спасибо за регистрацию <br/>" .
                    "Ваш логин: %s <br/>" .
                    "Ваш пароль: %s <br/>" .
                    "Ваш email: %s <br/>" .
                    "Ссылка для активации: %s",
                    Arr::get($emailParts, 0), $password, $email,
                    HTML::anchor(URL::base('http') . 'module_auth/token?email=' . $email .'&token=' . $token)
                );

                Helpers_Email::send($email, 'Регистрация', $message, true);
            }

            echo json_encode(
                array(
                    'errors' => $textErrors,
                    'errors_exists' => $errors_exists
                )
            );
        }

        exit();
    }

    public function action_ajax_login()
    {
        if ($this->request->is_ajax()) {
            $login = trim($this->request->post('login'));
            $password = trim($this->request->post('password'));
            $remember = trim($this->request->post('remember'));

            $user = ORM::factory('User', array('username' => md5($login)));
            if ($user->loaded()) {
                $login = $user->email;
            }

            Auth::instance()->login($login, $password, !!$remember);

            echo json_encode(array(
                'logged_in' => Auth::instance()->logged_in(),
				'errors' => array(
					'Неверный логин или пароль'
				)
            ));
        }

        exit();
    }

    public function action_token()
    {
        $this->set_metatags_and_content('');

        $token = arr::get($_GET, 'token');
        $email = arr::get($_GET, 'email');

        $user = ORM::factory('User', array(
            'email' => $email,
            'register_token' => $token
        ));

        if ($user->loaded() && $token) {
            $user->active = 1;
            $user->register_token = null;
            $user->save();

            Auth::instance()->logout();
            Auth::instance()->force_login($user);
        }

        $this->redirect('/');

        exit();
    }
}