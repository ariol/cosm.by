<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>
<?php foreach ($checkboxes as $role => $checkbox):?>
<?php echo $checkbox->render()?>
<?php echo form::label($checkbox->get_name(), $checkbox->get_label())?>
<script type="text/javascript">
<!--
$(document).ready(function(){

	$('[name=<?php echo $checkbox->get_name()?>]').change(function() {
		var tbody = $('#fieldgroup_<?php echo md5($checkbox->get_label());?>');
		if ($(this).attr('checked'))
		{
			tbody.show();
		}
		else
		{
			tbody.hide();
		}
	});

<?php if ($checkbox->get_value()->get_raw()):?>
$('#fieldgroup_<?php echo md5($checkbox->get_label());?>').show();
<?php else:?>
$('#fieldgroup_<?php echo md5($checkbox->get_label());?>').hide();
<?php endif;?>

});
//-->
</script>
<?php endforeach;?>