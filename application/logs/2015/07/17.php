<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-07-17 11:53:13 --- EMERGENCY: Swift_TransportException [ 0 ]: Connection could not be established with host smtp.gmail.com [Unable to find the socket transport "SSL" - did you forget to enable it when you configured PHP? #0] ~ MODPATH/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php [ 245 ] in /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php:80
2015-07-17 11:53:13 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php(80): Swift_Transport_StreamBuffer->_establishSocketConnection()
#1 /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/AbstractSmtpTransport.php(111): Swift_Transport_StreamBuffer->initialize(Array)
#2 /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Mailer.php(84): Swift_Transport_AbstractSmtpTransport->start()
#3 /home/vitaliy/www/1teh.by/modules/email/classes/Email.php(144): Swift_Mailer->send(Object(Swift_Message))
#4 /home/vitaliy/www/1teh.by/modules/ariol/classes/Helpers/Email.php(22): Email::send('o.zgolich@gmail...', 'o.zgolich@gmail...', '?????????? ????...', '<p>?? ?????????...', true)
#5 /home/vitaliy/www/1teh.by/application/classes/Controller/Admin/Order.php(333): Helpers_Email::send('o.zgolich@gmail...', '?????????? ????...', '<p>?? ?????????...', true)
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Admin_Order->action_change_order()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/vitaliy/www/1teh.by/modules/email/vendor/swift/classes/Swift/Transport/StreamBuffer.php:80