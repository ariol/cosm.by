<?php defined('SYSPATH') or die('No direct script access.');?>
<?php 
$last = array_pop($crumbs);
array_pop($links);
?>
<ol class="breadcrumb">
    <li><a href="<?='/'.Extasy_Url::url_to_route(Kohana::$config->load('extasy.redirect_from_index'))?>">Главная</a></li>
    <?php foreach($links as $link):?>
    	<li><?php echo $link?></li>
    <?php endforeach;?>
    <li class="active"><?php echo $last['title']?></li>
</ol>
