<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Auth_Driver extends Kohana_Auth_ORM
{
	private $_banned_to = 0;
	private $_ban_time = 0;

	public function is_banned()
	{
		return (bool) $this->get_banned_to();
	}

	public function get_banned_to()
	{
		return $this->_banned_to;
	}

	public function get_ban_time()
	{
		return $this->_ban_time;
	}

	private function _logged_in()
	{
		return $this->_session->get($this->_config['session_key']);
	}

	public function logged_in($role = NULL)
	{
		$status = FALSE;

		if ( ! is_null($role))
		{
			$role = arr::get($this->_config['roles'], $role);
			if ( ! $role)
			{
				return FALSE;
			}
		}

		// Get the user from the session
		if ( ! $user = $this->_logged_in())
		{
			$this->auto_login();
			$user = $this->_logged_in();
		}

		if (is_object($user) AND $user instanceof Model_User AND $user->loaded())
		{
			// Everything is okay so far
			$status = TRUE;

			if ( ! empty($role))
			{
				$status = $role & $user->roles;
			}
		}

		return $status;
	}

	public function _login($user, $password, $remember)
	{
		if (is_string($password))
		{
			// Create a hashed password
			$password = $this->hash($password);
		}

		if ( ! is_object($user))
		{
			// Load the user
			$user = ORM::factory('User', array('email' => $user));
		}

		if ( ! $user->loaded())
		{
			return FALSE;
		}

		if ($user->banned_to > time())
		{
			$this->_banned_to = $user->banned_to;
			$this->_ban_time = $user->ban_time;
			return FALSE;
		}
		else
		{
			$user->banned_to = NULL;
			$user->save();
		}

		// If the passwords match, perform a login
		if ($user->password === $password)
		{
			$user->login_attempts = 0;
			$user->ban_time = NULL;
			$user->banned_to = NULL;
			$user->save();

			if ($remember === TRUE)
			{
				// Token data
				$data = array(
					'user_id'    => $user->id,
					'expires'    => time() + $this->_config['lifetime'],
					'user_agent' => sha1(Request::$user_agent),
				);

				// Create a new autologin token
				$token = ORM::factory('User_Token')
							->values($data)
							->create();

				// Set the autologin cookie
				Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
			}

			// Finish the login
			$this->complete_login($user);

			return TRUE;
		}

		$user->login_attempts++;
		if ($user->login_attempts >= Kohana::$config->load('auth_ban.max_login_attempts'))
		{
			$user->login_attempts = 0;
			$user->ban_time = $user->ban_time
				? $user->ban_time * Kohana::$config->load('auth_ban.time_multiplier')
				: Kohana::$config->load('auth_ban.base_time');
			$user->banned_to = time() + $user->ban_time;
			$this->_banned_to = $user->banned_to;
			$this->_ban_time = $user->ban_time;
		}
		$user->save();

		// Login failed
		return FALSE;
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   $user
	 * @return  string
	 */
	public function password($user)
	{
		if ( ! is_object($user))
		{
			// Load the user
			$user = ORM::factory('User', array('email' => $user));
		}

		return $user->password;
	}
}