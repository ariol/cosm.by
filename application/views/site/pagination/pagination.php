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
    <div class="col-sm-8 pagination-block">
        <ul class="pagination">
            <li><a href="<?php echo urldecode($page->url($current_page - 1)); ?>">&laquo;</a></li>
            <?php for ($i = $min_page; $i <= $max_page; $i++) { ?>
                <?php if ($i == $current_page) { ?>
                    <li class="active"><a href="<?php echo urldecode($page->url($i)) ?>"><?php echo $i; ?></a></li>
                <?php } else { ?>
            <li><a href="<?php echo urldecode($page->url($i)) ?>"><?php echo $i; ?></a></li>
                <?php } ?>
            <?php } ?>
            <li><a href="<?php echo urldecode($page->url($current_page + 1)); ?>">&raquo;</a></li>
        </ul>
    </div>
    <!-- Pagination Ends -->
