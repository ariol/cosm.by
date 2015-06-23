<div<?php if ($checked) { ?> class="selected"<?php } ?>>
<div class="col-xs-6 param"><?php echo $filter['name']; ?></div>
<div class="col-xs-6 val">
	от: <input type="text" value="<?php echo Arr::get($value, 0); ?>" name="filter[<?php echo $filter['id']; ?>][0]" />
	до: <input type="text" value="<?php echo Arr::get($value, 1); ?>" name="filter[<?php echo $filter['id']; ?>][1]" />
</div>
</div>