<div<?php if ($checked) { ?> class="selected"<?php } ?>>
<div class="col-xs-6 param"><?php echo $filter['name']; ?></div>
<div class="col-xs-6 val"><?php echo Form::select('filter[' . $filter["id"] .']', $dictionaries, $value); ?></div>
</div>