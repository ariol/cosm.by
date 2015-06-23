<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>
<?php echo form::input($name, $value);?>
<script type="text/javascript">
$(document).ready(function(){
	$('[name=<?php echo $name?>]').autocomplete( {
		source: '<?php echo url::query(array('select_autocomplete' => $name))?>',
		mustMatch: true
	});
});
</script>
