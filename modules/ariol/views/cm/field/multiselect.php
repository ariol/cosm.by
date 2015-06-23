<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

$selected = $value->get_values();
foreach ($selected as $key => $cur_selected)
{
	$selected[$key] = $cur_selected->get_raw();
}

?>
<?php echo form::hidden($name, '1');?>
<?php $i = 0;?>
<?php foreach ($options as $value => $label):?>
<?php $i++?>
<?php echo form::hidden($name.'_'.$i, $i);?>
<?php echo form::checkbox($name.'_'.$i.'_value', $value, in_array($value, $selected));?>
<?php echo form::label($name.'_'.$i.'_value', $label);?>
<?php endforeach;?>