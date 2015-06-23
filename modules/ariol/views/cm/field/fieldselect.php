<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>
<?php echo Ext::form_fields_begin()?>
<?php echo Ext::form_row(
	$type_field->render(),
	$type_field->get_label()
)?>
<?php foreach ($fieldschemas as $type => $fieldschema):?>
<tbody id="<?php echo $name?>_config_tbody_<?php echo $type?>" class="<?php echo $name?>_config_tbody">
<?php foreach ($fieldschema->get_field_names() as $field_name):?>
<?php echo Ext::form_row(
	$fieldschema->get_field($field_name)->render(),
	$fieldschema->get_field($field_name)->get_label()
)?>
<?php endforeach;?>
</tbody>
<?php endforeach;?>
<?php echo Ext::form_fields_end()?>
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('[name=<?php echo $type_field->get_name()?>]').change(function(){
		$('.<?php echo $name?>_config_tbody').hide();
		var val = $(this).val();
<?php //TODO: Убрать костыль в select ?>
		val = val.replace('option_', '');
		$('#<?php echo $name?>_config_tbody_'+val).show();
	});

	$('[name=<?php echo $type_field->get_name()?>]').change();
});
//-->
</script>