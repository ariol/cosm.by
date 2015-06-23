<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
$i = 0;
?>
<?php echo form::hidden($name, '1');?>
<script type="text/javascript">
<!--
var <?php echo $name?>_next_field_num = 0;
<?php if ($allow_add):?>
var <?php echo $name?>_add_field = function()
{
	var new_tr_content = $('#<?php echo $name?>_field_new').html();
	new_tr_content = new_tr_content.replace(/__i__/gi, <?php echo $name?>_next_field_num);
	var new_tr = '<tr id="<?php echo $name?>_field_' + <?php echo $name?>_next_field_num + '">' + new_tr_content + '</tr>';

	$('#<?php echo $name?>_fields_tbody').append(new_tr);

	<?php echo $name?>_next_field_num++;
}
<?php endif;?>
//-->
</script>
<table>
<tr>
	<th width="20%">Название поля</th>
	<th>Значение</th>
	<th></th>
</tr>
<tbody id="<?php echo $name?>_fields_tbody">
<?php foreach ($value->get_values() as $cur_name => $cur_value):?>
<?php
	$field_name = new CM_Field_String();
	$field_name->set_name($name.'_'.$i);
	$field_name->set_raw_value($cur_name);

	$field_value = clone $value_field;
	$field_value->set_value($cur_value);
	$field_value->set_name($name.'_'.$i.'_value');
?>
<tr id="<?php echo $name?>_field_<?php echo $i?>">
	<td>
<?php echo $field_name->render()?>
	</td>
	<td>
<?php echo $field_value->render()?>
<?php if ($error = $field_value->get_error()):?>
<?php echo form::label($field_value->get_name(), $field_value->get_error(), array('class' => 'error'))?>
<?php endif;?>
	</td>
	<td>
		<a href="#" onclick="javascript: return confirm('Вы уверены?') && $('#<?php echo $name?>_field_<?php echo $i?>').remove() && false;">[x]</a>
<script type="text/javascript">
<!--
<?php $i++;?>
<?php echo $name?>_next_field_num = <?php echo $i?>;
//-->
</script>
	</td>
</tr>
<?php endforeach;?>
</tbody>
<?php
	$field_name = new CM_Field_String();
	$field_name->set_name($name.'___i__');
	$value_field->set_name($name.'___i___value');
?>
<?php if ($allow_add):?>
<tr style="display: none;" id="<?php echo $name?>_field_new">
	<td>
<?php echo $field_name->render()?>
	</td>
	<td>
<?php echo $value_field->render()?>
	</td>
	<td>
		<a href="#" onclick="javascript: return confirm('Вы уверены?') && $('#<?php echo $name?>_field___i__').remove() && false;">[x]</a>
	</td>
</tr>
<tr>
	<td colspan="100">
		<a href="#" style="text-decoration: none; border-bottom: 1px dashed;" onclick="javascript: <?php echo $name?>_add_field(); return false;">
			Добавить
		</a>
	</td>
</tr>
<?php endif;?>
</table>