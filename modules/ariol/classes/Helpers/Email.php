<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Helpers_Email
{
    static public function send($to, $subject, $message, $html = FALSE)
    {
        $config = array(
            'driver' => 'smtp',
            'options' => array(
                'hostname' => Kohana::$config->load('mailer.hostname'),
                'username' => Kohana::$config->load('mailer.username'),
                'password' => Kohana::$config->load('mailer.password'),
                'port'     => Kohana::$config->load('mailer.port'),
                'encryption' => Kohana::$config->load('mailer.encryption')
            )
        );

        Email::connect($config);
        Email::send($to, Kohana::$config->load('mailer.from'), $subject, $message, $html);
    }
}