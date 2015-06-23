<div<?php if ($checked) { ?> class="selected"<?php } ?>>
<div class="col-xs-6 param"><label for="filter_<?php echo $filter['id']; ?>"><?php echo $filter['name']; ?></label></div>
<div class="col-xs-6 val"><input id="filter_<?php echo $filter['id']; ?>" type="checkbox" name="filter[<?php echo $filter['id']; ?>]"<?php if ($checked) { ?>checked="checked"<?php } ?>/> <label for="filter_<?php echo $filter['id']; ?>">Наличие важно</label></div>
</div>
