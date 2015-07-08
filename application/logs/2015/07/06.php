<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-07-06 22:34:47 --- EMERGENCY: View_Exception [ 0 ]: The requested view admin/order/order_success could not be found ~ SYSPATH/classes/Kohana/View.php [ 257 ] in /home/cosm.by/modules/ariol/classes/Extasy/View.php:97
2015-07-06 22:34:47 --- DEBUG: #0 /home/cosm.by/modules/ariol/classes/Extasy/View.php(97): Kohana_View->set_filename('admin/order/ord...')
#1 /home/cosm.by/system/classes/Kohana/View.php(137): Extasy_View->set_filename('admin/order/ord...')
#2 /home/cosm.by/system/classes/Kohana/View.php(30): Kohana_View->__construct('admin/order/ord...', Array)
#3 /home/cosm.by/application/classes/Model/Orders.php(177): Kohana_View::factory('admin/order/ord...', Array)
#4 /home/cosm.by/modules/ariol/classes/CM/Form/Abstract.php(269): Model_Orders->save()
#5 /home/cosm.by/modules/ariol/classes/CM/Form/Abstract.php(97): CM_Form_Abstract->after_submit()
#6 /home/cosm.by/modules/ariol/classes/Controller/Crud.php(216): CM_Form_Abstract->submit()
#7 /home/cosm.by/modules/ariol/classes/Controller/Crud.php(202): Controller_Crud->process_form(Object(Model_Orders))
#8 /home/cosm.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_edit()
#9 [internal function]: Kohana_Controller->execute()
#10 /home/cosm.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#11 /home/cosm.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#12 /home/cosm.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#13 /home/cosm.by/index.php(149): Kohana_Request->execute()
#14 {main} in /home/cosm.by/modules/ariol/classes/Extasy/View.php:97
2015-07-06 22:44:19 --- EMERGENCY: View_Exception [ 0 ]: The requested view admin/order/order_success could not be found ~ SYSPATH/classes/Kohana/View.php [ 257 ] in /home/cosm.by/modules/ariol/classes/Extasy/View.php:97
2015-07-06 22:44:19 --- DEBUG: #0 /home/cosm.by/modules/ariol/classes/Extasy/View.php(97): Kohana_View->set_filename('admin/order/ord...')
#1 /home/cosm.by/system/classes/Kohana/View.php(137): Extasy_View->set_filename('admin/order/ord...')
#2 /home/cosm.by/system/classes/Kohana/View.php(30): Kohana_View->__construct('admin/order/ord...', Array)
#3 /home/cosm.by/application/classes/Model/Orders.php(177): Kohana_View::factory('admin/order/ord...', Array)
#4 /home/cosm.by/modules/ariol/classes/CM/Form/Abstract.php(269): Model_Orders->save()
#5 /home/cosm.by/modules/ariol/classes/CM/Form/Abstract.php(97): CM_Form_Abstract->after_submit()
#6 /home/cosm.by/modules/ariol/classes/Controller/Crud.php(216): CM_Form_Abstract->submit()
#7 /home/cosm.by/modules/ariol/classes/Controller/Crud.php(202): Controller_Crud->process_form(Object(Model_Orders))
#8 /home/cosm.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_edit()
#9 [internal function]: Kohana_Controller->execute()
#10 /home/cosm.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#11 /home/cosm.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#12 /home/cosm.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#13 /home/cosm.by/index.php(149): Kohana_Request->execute()
#14 {main} in /home/cosm.by/modules/ariol/classes/Extasy/View.php:97