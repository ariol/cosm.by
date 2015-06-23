<?php
	$max_page = $page + 2;

	if ($max_page > $count_pages) {
		$max_page = $count_pages;
	}

	if ($count_pages >= 5 && $max_page < 5) {
		$max_page = 5;
	}

	$min_page = $page - 2;
	if (($count_pages - $min_page) < 5) {
		$min_page--;
	}

	if ($min_page < 1) {
		$min_page = 1;
	}

	$query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
	$query = str_replace('&p=' . $page, '', $query);
?>
<?php if($max_page > 1) { ?>
<div class="col-sm-6 pagination-block">
<ul class="pagination">
    <li><a <?php if($page > $min_page){ ?> href="/search?<?php echo $query; ?>&p=<?php echo $page-1; ?>" <?php } ?>>&laquo;</a> </li>
	<?php for ($i = $min_page; $i <= $max_page; $i++) { ?>
		<?php if ($i == $page) { ?>
    <li class="active"><a class="current" href="/search?<?php echo $query; ?>&p=<?php echo $page; ?>"><span><?php echo $i; ?></span></a></li>
		<?php } else { ?>
    <li><a href="/search?<?php echo $query; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a> </li>
		<?php } ?>
	<?php } ?>
    <li><a <?php if($page < $max_page){ ?>  href="/search?<?php echo $query; ?>&p=<?php echo $page+1; ?>"<?php } ?>>&raquo;</a></li>
</ul>
</div>
<?php } ?>