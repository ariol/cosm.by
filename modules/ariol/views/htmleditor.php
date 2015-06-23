<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>

<?php if ( ! $initialized):?>
<?php echo HTML::script('ckeditor/ckeditor.js'); ?>
<?php endif;?>
<script type="text/javascript">
$(document).ready(function(){
	var config1 = <?php echo ! empty($config) ? json_encode($config) : '{}'?>;
	var config2 = {
		'filebrowserBrowseUrl':'/ckfinder/ckfinder.html',
		'filebrowserImageBrowseUrl':'/ckfinder/ckfinder.html?type=Images',
		'filebrowserFlashBrowseUrl':'/ckfinder/ckfinder.html?type=Flash',
		'filebrowserUploadUrl':'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		'filebrowserImageUploadUrl':'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
		'filebrowserFlashUploadUrl':'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'};
	var editor = CKEDITOR.replace('<?php echo $name; ?>', merge_options(config1, config2));
});

function merge_options(obj1,obj2){
	var obj3 = {};
	for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
	for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
	return obj3;
}

</script>