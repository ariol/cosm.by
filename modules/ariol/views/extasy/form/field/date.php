<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>

<?php echo form::input($name, $value, $attributes);?>
<script type="text/javascript">
$(':input[name=<?php echo $name?>]').width(70).mask("9999-99-99");
</script>