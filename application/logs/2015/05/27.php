<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-05-27 13:28:32 --- EMERGENCY: Kohana_Exception [ 0 ]: The code_crtificate property does not exist in the Model_Orders class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-05-27 13:28:32 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('code_crtificate')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('code_crtificate')
#2 /home/vitaliy/www/1teh.by/application/views/admin/order/edit_order.php(136): Extasy_Orm->__get('code_crtificate')
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
2015-05-27 13:28:47 --- EMERGENCY: Kohana_Exception [ 0 ]: The code_crtificate property does not exist in the Model_Orders class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-05-27 13:28:47 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('code_crtificate')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('code_crtificate')
#2 /home/vitaliy/www/1teh.by/application/views/admin/order/edit_order.php(136): Extasy_Orm->__get('code_crtificate')
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
2015-05-27 13:29:00 --- EMERGENCY: Kohana_Exception [ 0 ]: The code_crtificate property does not exist in the Model_Orders class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-05-27 13:29:00 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('code_crtificate')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('code_crtificate')
#2 /home/vitaliy/www/1teh.by/application/views/admin/order/edit_order.php(136): Extasy_Orm->__get('code_crtificate')
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
2015-05-27 15:15:50 --- EMERGENCY: View_Exception [ 0 ]: The requested view cancel_coupon could not be found ~ SYSPATH/classes/Kohana/View.php [ 257 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-05-27 15:15:50 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(97): Kohana_View->set_filename('cancel_coupon')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(64): Extasy_View->set_filename('cancel_coupon')
#2 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Extasy_Controller->after()
#3 [internal function]: Kohana_Controller->execute()
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#5 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#7 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#8 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-05-27 15:19:51 --- EMERGENCY: View_Exception [ 0 ]: The requested view cancel_coupon could not be found ~ SYSPATH/classes/Kohana/View.php [ 257 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-05-27 15:19:51 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(97): Kohana_View->set_filename('cancel_coupon')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(64): Extasy_View->set_filename('cancel_coupon')
#2 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Extasy_Controller->after()
#3 [internal function]: Kohana_Controller->execute()
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#5 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#7 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#8 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97