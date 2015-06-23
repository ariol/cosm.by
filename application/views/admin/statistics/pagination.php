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

<div class="col-sm-8 pagination-block">
    <?php echo $page;?>
    <ul class="pagination">
        <li <?php if($current_page == 1) { ?>class="disabled"<?php } ?>><a href="<?php echo $query;?>?page=<?php echo $current_page - 1 ;?>" >&laquo;</a></li>
        <?php for ($i = $min_page; $i <= $max_page; $i++) { ?>
            <?php if ($i == $current_page) { ?>
                <li class="active"><a href="#"><?php echo $i; ?></a></li>
            <?php } else { ?>
                <li><a href="<?php echo $query;?>?page=<?php echo $i ?>"><?php echo $i; ?></a></li>
            <?php } ?>
        <?php } ?>
        <li <?php if($current_page == $max_page) { ?>class="disabled"<?php } ?>><a href="<?php echo $query;?>&?page=<?php echo $current_page + 1; ?>">&raquo;</a></li>
    </ul>
</div>