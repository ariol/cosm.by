<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>

<?php if( ! empty($actions)):?>
<?php echo Ext::actions_begin()?>
<?php foreach($actions as $action):?>
	<p><?php echo Ext::actions_row($action['route'], $action['title'])?></p>
<?php endforeach;?>
<?php echo Ext::actions_end()?>
<?php endif;?>