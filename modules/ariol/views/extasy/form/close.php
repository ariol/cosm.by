<?php defined('SYSPATH') or die('No direct script access.');
?>
<?php echo Ext::form_end()?>
<script type="text/javascript">

$('#<?php echo $id?>').validate(<?php echo json_encode($validate)?>);

</script>