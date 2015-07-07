<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-06-29 22:29:12 --- EMERGENCY: Kohana_Exception [ 0 ]: The s_description property does not exist in the Model_Line class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/cosm.by/modules/orm/classes/Kohana/ORM.php:603
2015-06-29 22:29:12 --- DEBUG: #0 /home/cosm.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('s_description')
#1 /home/cosm.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('s_description')
#2 /home/cosm.by/application/classes/Controller/Site/Line.php(261): Extasy_Orm->__get('s_description')
#3 /home/cosm.by/system/classes/Kohana/Controller.php(84): Controller_Site_Line->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 /home/cosm.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Line))
#6 /home/cosm.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 /home/cosm.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 /home/cosm.by/index.php(149): Kohana_Request->execute()
#9 {main} in /home/cosm.by/modules/orm/classes/Kohana/ORM.php:603
2015-06-29 22:38:05 --- EMERGENCY: Kohana_Exception [ 0 ]: View variable is not set: line_brand ~ SYSPATH/classes/Kohana/View.php [ 171 ] in /home/cosm.by/application/classes/Controller/Site/Line.php:43
2015-06-29 22:38:05 --- DEBUG: #0 /home/cosm.by/application/classes/Controller/Site/Line.php(43): Kohana_View->__get('line_brand')
#1 /home/cosm.by/system/classes/Kohana/Controller.php(84): Controller_Site_Line->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 /home/cosm.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Line))
#4 /home/cosm.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 /home/cosm.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 /home/cosm.by/index.php(149): Kohana_Request->execute()
#7 {main} in /home/cosm.by/application/classes/Controller/Site/Line.php:43
2015-06-29 23:46:23 --- EMERGENCY: Swift_TransportException [ 0 ]: Connection could not be established with host smtp.yandex.ru [Unable to find the socket transport "SSL" - did you forget to enable it when you configured PHP? #0] ~ MODPATH/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php [ 245 ] in /home/cosm.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php:80
2015-06-29 23:46:23 --- DEBUG: #0 /home/cosm.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php(80): Swift_Transport_StreamBuffer->_establishSocketConnection()
#1 /home/cosm.by/modules/email/vendor/swift/classes/Swift/Transport/AbstractSmtpTransport.php(111): Swift_Transport_StreamBuffer->initialize(Array)
#2 /home/cosm.by/modules/email/vendor/swift/classes/Swift/Mailer.php(84): Swift_Transport_AbstractSmtpTransport->start()
#3 /home/cosm.by/modules/email/classes/Email.php(144): Swift_Mailer->send(Object(Swift_Message))
#4 /home/cosm.by/modules/ariol/classes/Helpers/Email.php(22): Email::send('test@ariol.by', 'test@ariol.by', '\xD0\x9D\xD0\xBE\xD0\xB2\xD1\x8B\xD0\xB9 \xD0\xB7\xD0\xB0...', '\t\t<p>\xD0\x92 \xD0\xB8\xD0\xBD\xD1\x82\xD0...', true)
#5 /home/cosm.by/application/classes/Controller/Site/Cart.php(386): Helpers_Email::send('test@ariol.by', '\xD0\x9D\xD0\xBE\xD0\xB2\xD1\x8B\xD0\xB9 \xD0\xB7\xD0\xB0...', '\t\t<p>\xD0\x92 \xD0\xB8\xD0\xBD\xD1\x82\xD0...', true)
#6 /home/cosm.by/system/classes/Kohana/Controller.php(84): Controller_Site_Cart->action_order()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/cosm.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Cart))
#9 /home/cosm.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/cosm.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/cosm.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/cosm.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php:80
2015-06-29 23:46:28 --- EMERGENCY: Swift_TransportException [ 0 ]: Connection could not be established with host smtp.yandex.ru [Unable to find the socket transport "SSL" - did you forget to enable it when you configured PHP? #0] ~ MODPATH/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php [ 245 ] in /home/cosm.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php:80
2015-06-29 23:46:28 --- DEBUG: #0 /home/cosm.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php(80): Swift_Transport_StreamBuffer->_establishSocketConnection()
#1 /home/cosm.by/modules/email/vendor/swift/classes/Swift/Transport/AbstractSmtpTransport.php(111): Swift_Transport_StreamBuffer->initialize(Array)
#2 /home/cosm.by/modules/email/vendor/swift/classes/Swift/Mailer.php(84): Swift_Transport_AbstractSmtpTransport->start()
#3 /home/cosm.by/modules/email/classes/Email.php(144): Swift_Mailer->send(Object(Swift_Message))
#4 /home/cosm.by/modules/ariol/classes/Helpers/Email.php(22): Email::send('test@ariol.by', 'test@ariol.by', '\xD0\x9D\xD0\xBE\xD0\xB2\xD1\x8B\xD0\xB9 \xD0\xB7\xD0\xB0...', '\t\t<p>\xD0\x92 \xD0\xB8\xD0\xBD\xD1\x82\xD0...', true)
#5 /home/cosm.by/application/classes/Controller/Site/Cart.php(386): Helpers_Email::send('test@ariol.by', '\xD0\x9D\xD0\xBE\xD0\xB2\xD1\x8B\xD0\xB9 \xD0\xB7\xD0\xB0...', '\t\t<p>\xD0\x92 \xD0\xB8\xD0\xBD\xD1\x82\xD0...', true)
#6 /home/cosm.by/system/classes/Kohana/Controller.php(84): Controller_Site_Cart->action_order()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/cosm.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Cart))
#9 /home/cosm.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/cosm.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/cosm.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/cosm.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php:80