<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-06-02 10:55:48 --- EMERGENCY: View_Exception [ 0 ]: The requested view count_article could not be found ~ SYSPATH/classes/Kohana/View.php [ 257 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-06-02 10:55:48 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(97): Kohana_View->set_filename('count_article')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(64): Extasy_View->set_filename('count_article')
#2 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Extasy_Controller->after()
#3 [internal function]: Kohana_Controller->execute()
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Statistics))
#5 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#7 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#8 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97