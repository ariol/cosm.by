<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-05-26 19:47:42 --- EMERGENCY: Kohana_Exception [ 0 ]: The quantity property does not exist in the Model_Certificate class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-05-26 19:47:42 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('quantity')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('quantity')
#2 /home/vitaliy/www/1teh.by/application/views/admin/order/edit_order.php(69): Extasy_Orm->__get('quantity')
#3 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(61): include('/home/vitaliy/w...')
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(348): Kohana_View::capture('/home/vitaliy/w...', Array)
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(27): Kohana_View->render(NULL)
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#7 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Extasy_Controller->after()
#9 [internal function]: Kohana_Controller->execute()
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#12 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#13 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#14 {main} in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-05-26 19:49:18 --- EMERGENCY: Kohana_Exception [ 0 ]: The quantity property does not exist in the Model_Certificate class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-05-26 19:49:18 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('quantity')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('quantity')
#2 /home/vitaliy/www/1teh.by/application/views/admin/order/edit_order.php(69): Extasy_Orm->__get('quantity')
#3 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(61): include('/home/vitaliy/w...')
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(348): Kohana_View::capture('/home/vitaliy/w...', Array)
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(27): Kohana_View->render(NULL)
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#7 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Extasy_Controller->after()
#9 [internal function]: Kohana_Controller->execute()
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#12 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#13 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#14 {main} in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-05-26 19:52:53 --- EMERGENCY: Kohana_Exception [ 0 ]: The quantity property does not exist in the Model_Certificate class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-05-26 19:52:53 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('quantity')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('quantity')
#2 /home/vitaliy/www/1teh.by/application/views/admin/order/edit_order.php(70): Extasy_Orm->__get('quantity')
#3 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(61): include('/home/vitaliy/w...')
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(348): Kohana_View::capture('/home/vitaliy/w...', Array)
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(27): Kohana_View->render(NULL)
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#7 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Extasy_Controller->after()
#9 [internal function]: Kohana_Controller->execute()
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#12 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#13 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#14 {main} in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-05-26 19:59:56 --- EMERGENCY: Kohana_Exception [ 0 ]: The quantity property does not exist in the Model_Certificate class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-05-26 19:59:56 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('quantity')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('quantity')
#2 /home/vitaliy/www/1teh.by/application/views/admin/order/edit_order.php(71): Extasy_Orm->__get('quantity')
#3 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(61): include('/home/vitaliy/w...')
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(348): Kohana_View::capture('/home/vitaliy/w...', Array)
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(27): Kohana_View->render(NULL)
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#7 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Extasy_Controller->after()
#9 [internal function]: Kohana_Controller->execute()
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#12 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#13 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#14 {main} in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603