<?php defined('SYSPATH') or die('No direct script access.');?>
<?php foreach($submits as $submit):?>
<?php echo Ext::submit($submit['name'], $submit['value'])?>
<?php endforeach;?>
