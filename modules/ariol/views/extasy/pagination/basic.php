<ul style="margin: 0;" class="pagination">
	<li><a <?php if ($previous_page == FALSE): ?>class="btn" style="margin-top: 0;" disabled="disabled"<?php endif ?> href="<?php echo $page->url($previous_page) ?>">&larr;</a></li>
	<?php for ($i = 1; $i <= $total_pages; $i++): ?>
		<?php if ($i == $current_page): ?>
			<li class="active"><a disabled="disabled"><?php echo $i ?><span class="sr-only">(current)</span></a></li>
		<?php else: ?>
			<li><a href="<?php echo $page->url($i) ?>"><?php echo $i ?></a></li>
		<?php endif ?>
	<?php endfor ?>
	<li><a <?php if ($next_page == FALSE): ?>class="btn" style="margin-top: 0;" disabled="disabled"<?php endif ?> href="<?php echo $page->url($next_page) ?>">&rarr;</a></li>
</ul>