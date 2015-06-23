<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
?>
<?php echo Ext::table_begin();?>
<?php echo Ext::table_header_begin();?>
<?php foreach($labels as $label):?>
	<th><?php echo $label;?></th>
<?php endforeach;?>
<th>[X]</th>
<?php echo Ext::table_header_end(); ?>
<tbody id="<?php echo $name;?>_values_tbody">
</tbody>
<tr id="<?php echo $name;?>_form_elements">
<?php foreach($fields as $cur_name => $field):?>
<td>
<?php echo $field->input();?>
</td>
<?php endforeach;?>
<td>
<input id="<?php echo $name;?>_add_button" type="button" name="add_element" value="[+]" style="width:30px;" /></td>
</tr>
<?php echo Ext::table_end();?>
<script type="text/javascript">
var <?php echo $name?>_field_object = new extasy_field_array('<?php echo $name?>');

<?php foreach($value as $item):?>
<?php echo $name?>_field_object.add_element(<?php echo json_encode($item)?>);
<?php endforeach;?>

</script>
<input type="hidden" name="<?php echo $name?>[default]" value="default" />