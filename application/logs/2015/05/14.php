<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-05-14 17:36:57 --- EMERGENCY: Kohana_Exception [ 0 ]: The cart property does not exist in the Model_OrderData class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-05-14 17:36:57 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('cart')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('cart')
#2 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(436): Extasy_Orm->__get('cart')
#3 /home/vitaliy/www/1teh.by/application/classes/Controller/Admin/Order.php(94): Extasy_Orm->offsetGet('cart')
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Admin_Order->action_edit_order()
#5 [internal function]: Kohana_Controller->execute()
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#10 {main} in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603