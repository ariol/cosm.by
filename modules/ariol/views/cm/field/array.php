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

var <?php echo $name?>_delete_field = function(num)
{
	var field_tr = $('#<?php echo $name?>_field_'+num);
	field_tr.remove();
	var value_tr = $('#<?php echo $name?>_field_'+num+'_value');
	value_tr.remove();
	return true;
}
//-->
</script>
<table>
<tbody id="<?php echo $name?>_fields_tbody">
<?php foreach ($value->get_values() as $cur_value):?>
<?php
	$field_value = clone $value_field;
	$field_value->set_value($cur_value);
	$field_value->set_name($name.'_'.$i.'_value');
?>
<tr id="<?php echo $name?>_field_<?php echo $i?>">
	<td>
<?php echo form::hidden($name.'_'.$i, $i);?>
<?php echo $field_value->render()?>
<?php if ($error = $field_value->get_error()):?>
<?php echo form::label($field_value->get_name(), $field_value->get_error(), array('class' => 'error'))?>
<?php endif;?>
	</td>
	<td>
		<a href="#" onclick="javascript: return confirm('Вы уверены?') && <?php echo $name?>_delete_field(<?php echo $i?>) && false;"><i class="fa fa-trash-o"></i></a>
	</td>
</tr>
<tr id="<?php echo $name?>_field_<?php echo $i?>_value" style="display:none;">
	<td colspan="2"><span style="text-decoration: line-through;"><?php echo $field_value->render_value()?></span>

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
	$value_field->set_name($name.'___i___value');
?>
<?php if ($allow_add):?>
<tr style="display: none;" id="<?php echo $name?>_field_new">
	<td>
<?php echo form::hidden($name.'___i__', '__i__');?>
<?php echo $value_field->render()?>
	</td>
	<td>
		<a href="#" onclick="javascript: return confirm('Вы уверены?') && $('#<?php echo $name?>_field___i__').remove() && false;"><i class="fa fa-trash-o"></i></a>
	</td>
</tr>
<tr>
	<td colspan="100">
		<a class="add_button" href="#" onclick="javascript: <?php echo $name?>_add_field(); return false;">
			<button class="btn btn-xs btn-info">Добавить</button>
		</a>
	</td>
</tr>
<?php endif;?>
</table>
<?php if ($i == 0) { ?>
<script>
    $(document).ready(function() {
        $('.add_button').trigger('click');
    })
</script>
<?php } ?>