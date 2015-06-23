<?php

# KCAPTCHA configuration file

$alphabet = Kohana::$config->load('captcha')->alphabet; # do not change without changing font files!

# symbols used to draw CAPTCHA
//$allowed_symbols = "0123456789"; #digits
$allowed_symbols = Kohana::$config->load('captcha')->allowed_symbols; #alphabet without similar symbols (o=0, 1=l, i=j, t=f)

# folder with fonts
$fontsdir = 'fonts';

# CAPTCHA string length
$length = Kohana::$config->load('captcha')->length; # random 5 or 6
//$length = 6;

# CAPTCHA image size (you do not need to change it, whis parameters is optimal)
$width = Kohana::$config->load('captcha')->width;
$height = Kohana::$config->load('captcha')->height;

# symbol's vertical fluctuation amplitude divided by 2
$fluctuation_amplitude = 5;

# increase safety by prevention of spaces between symbols
$no_spaces = true;

# show credits
$show_credits = Kohana::$config->load('captcha')->show_credits; # set to false to remove credits line. Credits adds 12 pixels to image height
$credits = Kohana::$config->load('captcha')->credits; # if empty, HTTP_HOST will be shown

# CAPTCHA image colors (RGB, 0-255)
//$foreground_color = array(0, 0, 0);
//$background_color = array(220, 230, 255);
$foreground_color = Kohana::$config->load('captcha')->foreground_color;
$background_color = Kohana::$config->load('captcha')->background_color;

# JPEG quality of CAPTCHA image (bigger is better quality, but larger file size)
$jpeg_quality = 90;
?>