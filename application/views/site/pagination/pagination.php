<?php
	$max_page = $current_page + 2;

	if ($max_page > $total_pages) {
		$max_page = $total_pages;
	}

	if ($total_pages >= 5 && $max_page < 5) {
		$max_page = 5;
	}

	$min_page = $current_page - 2;
	if (($total_pages - $min_page) < 5) {
		$min_page--;
	}

	if ($min_page < 1) {
		$min_page = 1;
	}
?>
    <!-- Pagination Starts -->
    <div class="col-sm-12 pagination-block">
        <ul class="pagination">
            <?php if ($current_page > 1) { ?>
            <li><a href="<?php echo urldecode($page->url($current_page - 1)); ?>">&laquo;</a></li>
            <?php } ?>
            <?php for ($i = $min_page; $i <= $max_page; $i++) { ?>
                <?php if ($i == $current_page) { ?>
                    <li class="active"><a href="<?php echo urldecode($page->url($i)) ?>"><?php echo $i; ?></a></li>
                <?php } else { ?>
            <li><a href="<?php echo urldecode($page->url($i)) ?>"><?php echo $i; ?></a></li>
                <?php } ?>
            <?php } ?>
            <?php if ($current_page < $max_page) { ?>
            <li><a href="<?php echo urldecode($page->url($current_page + 1)); ?>">&raquo;</a></li>
            <?php } ?>
        </ul>
    </div>
    <!-- Pagination Ends -->
