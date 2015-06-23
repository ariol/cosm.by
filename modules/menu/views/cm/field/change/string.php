<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>

<div class="change" id="changed-field">
    <?php $attributes['id'] = 'changed'; ?>
    <?php $attributes['onkeyup'] = 'change.setOldFieldStringValue();';?>
    <?php echo form::input($name, $value, $attributes);?>
</div>
<div class="fa fa-refresh" id="change"></div>

<script type="text/javascript">

    var change = {
	
	oldFieldStringValue : $('#changed-field').children('input').val(),
	oldFieldSelectValue : "",
	value : "",
	name :  $('#changed-field').children().attr('name'),
	fieldType : $('#changed-field').children().attr('type'),
	setOldFieldStringValue : function() {
	    this.oldFieldStringValue = $('#changed').val();
	},
	setOldFieldSelectValue : function() {
	    this.oldFieldSelectValue = $('#changed').val();
	},
	change : function() {
	    <?php 
		$attributes['id'] = 'changed';
		$attributes['onchange'] = 'change.setOldFieldSelectValue();';
		$formField = form::select_improved($name, $options, $value, $attributes);
		$formField = str_replace("\n", '', $formField);
		$formField = str_replace('"', "'", $formField);
	    ;?>
	    if(this.fieldType == 'text') {
		this.value = "<?= $formField; ?>";
		$('#changed-field').html(this.value);
		$('#changed-field').children().val(this.oldFieldSelectValue);
	    }
	    else {
		this.fieldValue = $('#changed-field').children('select').val();
		$('#changed-field').html('<input onchange="change.setOldFieldStringValue();" id="changed" value="' + this.oldFieldStringValue + '" type="text" name="' + this.name + '" />');
	    }
	    
	    this.fieldType = $('#changed-field').children().attr('type');
	}
    };
    
    $(document).ready(function(){
	$('#change').click(function(){
	    change.change();
		$('select').selectpicker();
	});
    });
</script>