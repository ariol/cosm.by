<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
?>
<?php echo form::hidden($name.'[]', 0)?>
<?php foreach($rows as $cur_id => $cur_name):?>
<div id="toggle-state-toggle-button">
<?php echo form::checkbox($name.'[]', $cur_id, in_array($cur_id, $value))?>
</div>
<?php echo $cur_name?>&nbsp;&nbsp;
<?php endforeach;?>