<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>
<?php echo form::input($name, $value);?>
<script type="text/javascript">
$(':input[name=<?php echo $name?>]').width(40).mask("99:99");
</script>