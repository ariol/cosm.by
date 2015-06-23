<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>
c <?php echo $date_from->render()?> по <?php echo $date_to->render()?>
<script type="text/javascript">
<!--
$('[name=<?php echo $date_from->get_name()?>]').change(function(){
	var date_from = $(this).val();
	var date_to_input = $('[name=<?php echo $date_to->get_name()?>]');
	if (date_to_input.val() != '' && date_from > date_to_input.val())
	{
		date_to_input.val(date_from);
	}
});
$('[name=<?php echo $date_to->get_name()?>]').change(function(){
	$('[name=<?php echo $date_from->get_name()?>]').change();
});
//-->
</script>