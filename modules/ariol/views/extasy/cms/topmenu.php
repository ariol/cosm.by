<ul class="dropdown-menu">
		<li>
			<a href="/<?php echo Extasy_Url::url_to_route('site-index'); ?>" target="blank">
				<i class="fa fa-external-link-square"></i> Перейти на сайт
			</a>
		</li>
	<?php foreach ($menu as $name => $route): ?>
		<li>
			<a href="/<?php echo Extasy_Url::url_to_route($route); ?>">
				<?php echo $name; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>