<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>
<?php foreach ($options as $option_value => $option_label):?>

<?php echo form::radio($name, $option_value, $option_value == $value, $attributes + array(
	'id' => 'radio_'.$name.'_'.$option_value
));?>
<?php echo form::label('radio_'.$name.'_'.$option_value, $option_label);?>

<?php endforeach;?>	