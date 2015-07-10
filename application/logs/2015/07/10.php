<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-07-10 12:37:12 --- EMERGENCY: Kohana_Exception [ 0 ]: Not an image or invalid image:  ~ MODPATH/image/classes/Kohana/Image.php [ 107 ] in /home/vitaliy/www/1teh.by/modules/image/classes/Kohana/Image/GD.php:91
2015-07-10 12:37:12 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/image/classes/Kohana/Image/GD.php(91): Kohana_Image->__construct('/home/vitaliy/w...')
#1 /home/vitaliy/www/1teh.by/modules/image/classes/Kohana/Image.php(54): Kohana_Image_GD->__construct('/home/vitaliy/w...')
#2 /home/vitaliy/www/1teh.by/modules/ariol/classes/Lib/Image.php(127): Kohana_Image::factory('/home/vitaliy/w...')
#3 /home/vitaliy/www/1teh.by/application/classes/Model/Promo.php(53): Lib_Image::crop('/files/promo/1/...', 'promo', '1', 220, 120)
#4 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(283): Model_Promo->get_image_thumb()
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(436): Extasy_Orm->__get('image_thumb')
#6 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Core.php(23): Extasy_Orm->offsetGet('image_thumb')
#7 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Grid/Column/Template.php(21): Extasy_Core::obj_placeholders(Object(Model_Promo), '${image_thumb}')
#8 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Grid/Column.php(71): Extasy_Grid_Column_Template->_field(Object(Model_Promo))
#9 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(550): Extasy_Grid_Column->field(Object(Model_Promo))
#10 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(178): Extasy_Orm->grid_value('image')
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_get_grid_data()
#12 [internal function]: Kohana_Controller->execute()
#13 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Promo))
#14 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#15 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#16 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#17 {main} in /home/vitaliy/www/1teh.by/modules/image/classes/Kohana/Image/GD.php:91