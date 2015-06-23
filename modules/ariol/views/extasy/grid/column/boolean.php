<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>

<td align="right">
	<?php echo Form::checkbox($field, $value, $value, array(
		'class' => 'change',
		'data-class' => get_class($object),
		'data-field' => $field,
		'data-id' => $object->id
	)); ?>
</td>