<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-07-07 17:42:23 --- EMERGENCY: View_Exception [ 0 ]: The requested view admin/order/order_success could not be found ~ SYSPATH/classes/Kohana/View.php [ 257 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-07-07 17:42:23 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(97): Kohana_View->set_filename('admin/order/ord...')
#1 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(137): Extasy_View->set_filename('admin/order/ord...')
#2 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(30): Kohana_View->__construct('admin/order/ord...', Array)
#3 /home/vitaliy/www/1teh.by/application/classes/Model/Orders.php(177): Kohana_View::factory('admin/order/ord...', Array)
#4 /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(269): Model_Orders->save()
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(97): CM_Form_Abstract->after_submit()
#6 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(216): CM_Form_Abstract->submit()
#7 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(202): Controller_Crud->process_form(Object(Model_Orders))
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_edit()
#9 [internal function]: Kohana_Controller->execute()
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#12 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#13 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#14 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-07-07 17:59:47 --- EMERGENCY: Swift_TransportException [ 0 ]: Connection could not be established with host smtp.gmail.com [Unable to find the socket transport "SSL" - did you forget to enable it when you configured PHP? #0] ~ MODPATH/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php [ 245 ] in /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php:80
2015-07-07 17:59:47 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php(80): Swift_Transport_StreamBuffer->_establishSocketConnection()
#1 /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/AbstractSmtpTransport.php(111): Swift_Transport_StreamBuffer->initialize(Array)
#2 /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Mailer.php(84): Swift_Transport_AbstractSmtpTransport->start()
#3 /home/vitaliy/www/1teh.by/modules/email/classes/Email.php(144): Swift_Mailer->send(Object(Swift_Message))
#4 /home/vitaliy/www/1teh.by/modules/ariol/classes/Helpers/Email.php(22): Email::send('o.zgolich@gmail...', 'o.zgolich@gmail...', '?????????? ????...', '??<p>?? ???????...', true)
#5 /home/vitaliy/www/1teh.by/application/classes/Controller/Site/Cart.php(386): Helpers_Email::send('o.zgolich@gmail...', '?????????? ????...', '??<p>?? ???????...', true)
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Site_Cart->action_order()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Cart))
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php:80
2015-07-07 18:01:50 --- EMERGENCY: View_Exception [ 0 ]: The requested view admin/order/order_success could not be found ~ SYSPATH/classes/Kohana/View.php [ 257 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-07-07 18:01:50 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(97): Kohana_View->set_filename('admin/order/ord...')
#1 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(137): Extasy_View->set_filename('admin/order/ord...')
#2 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(30): Kohana_View->__construct('admin/order/ord...', Array)
#3 /home/vitaliy/www/1teh.by/application/classes/Model/Orders.php(177): Kohana_View::factory('admin/order/ord...', Array)
#4 /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(269): Model_Orders->save()
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(97): CM_Form_Abstract->after_submit()
#6 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(216): CM_Form_Abstract->submit()
#7 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(202): Controller_Crud->process_form(Object(Model_Orders))
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_edit()
#9 [internal function]: Kohana_Controller->execute()
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#12 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#13 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#14 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-07-07 18:08:46 --- EMERGENCY: Swift_TransportException [ 0 ]: Connection could not be established with host smtp.gmail.com [Unable to find the socket transport "SSL" - did you forget to enable it when you configured PHP? #0] ~ MODPATH/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php [ 245 ] in /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php:80
2015-07-07 18:08:46 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php(80): Swift_Transport_StreamBuffer->_establishSocketConnection()
#1 /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/AbstractSmtpTransport.php(111): Swift_Transport_StreamBuffer->initialize(Array)
#2 /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Mailer.php(84): Swift_Transport_AbstractSmtpTransport->start()
#3 /home/vitaliy/www/1teh.by/modules/email/classes/Email.php(144): Swift_Mailer->send(Object(Swift_Message))
#4 /home/vitaliy/www/1teh.by/modules/ariol/classes/Helpers/Email.php(22): Email::send('cert@mail.ru', 'o.zgolich@gmail...', '?????????? ????...', '<p>????????????...', true)
#5 /home/vitaliy/www/1teh.by/application/classes/Model/Orders.php(181): Helpers_Email::send('cert@mail.ru', '?????????? ????...', '<p>????????????...', true)
#6 /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(269): Model_Orders->save()
#7 /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(97): CM_Form_Abstract->after_submit()
#8 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(216): CM_Form_Abstract->submit()
#9 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(202): Controller_Crud->process_form(Object(Model_Orders))
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_edit()
#11 [internal function]: Kohana_Controller->execute()
#12 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#13 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#14 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#15 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#16 {main} in /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php:80