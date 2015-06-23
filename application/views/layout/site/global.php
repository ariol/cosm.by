<?php echo View::factory('layout/site/areas/header', array(
		'result_quantity' => $result_quantity,
		's_title' => $s_title,
		'summlikes' => $summlikes
	))->render();?>
    <?php if (Arr::get($_GET, 'debug')) { ?>
        <?php  echo $debugbarRenderer->render(); ?>
    <?php } ?>

    <?php echo $content; ?>

<?php echo View::factory('layout/site/areas/footer');?>
