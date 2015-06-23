<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-04-22 16:11:00 --- EMERGENCY: View_Exception [ 0 ]: The requested view layout/site/global_inner could not be found ~ SYSPATH/classes/Kohana/View.php [ 257 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-04-22 16:11:00 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(97): Kohana_View->set_filename('layout/site/glo...')
#1 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(339): Extasy_View->set_filename('layout/site/glo...')
#2 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(28): Kohana_View->render('layout/site/glo...')
#3 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#4 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Site.php(83): Extasy_Controller->after()
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Controller_Site->after()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Like))
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97