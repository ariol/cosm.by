<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-04-26 23:04:32 --- EMERGENCY: View_Exception [ 0 ]: The requested view site/pagination/index could not be found ~ SYSPATH/classes/Kohana/View.php [ 257 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-04-26 23:04:32 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(97): Kohana_View->set_filename('site/pagination...')
#1 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(137): Extasy_View->set_filename('site/pagination...')
#2 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(30): Kohana_View->__construct('site/pagination...', Array)
#3 /home/vitaliy/www/1teh.by/application/classes/Controller/Site/Search.php(37): Kohana_View::factory('site/pagination...', Array)
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Site_Search->action_index()
#5 [internal function]: Kohana_Controller->execute()
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Search))
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#10 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97