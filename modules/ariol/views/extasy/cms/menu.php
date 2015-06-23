<?php $currentRoute = Route::name(Request::current()->route()); ?>
<div class="sidebar-nav nav-collapse collapse navbar-collapse">
	<ul class="nav main-menu">
		<?php $i = 0; foreach ($menu as $name => $route) { ?>
			<?php if(is_array($route)) { ?>
				<li class="item<?php echo $i?>">
					<?php $nameParts = Helpers_Menu::getNameParts($name); ?>
					<a class="dropmenu" href="javascript:void(0);"><?php echo $nameParts['icon']; ?><span class="hidden-sm text"> <?php echo $nameParts['name']; ?></span> <span class="chevron <?php if (in_array($currentRoute, $route)) { ?>opened<? } else { ?>closed<?php } ?>"></span></a>
					<ul>
						<?php foreach ($route as $secondName => $secondRoute) { ?>
							<?php $secondNameParts = Helpers_Menu::getNameParts($secondName); ?>
							<li><a class="submenu" href="/<?php echo Extasy_Url::url_to_route($secondRoute); ?>"><?php echo $secondNameParts['icon']; ?><span class="hidden-sm text"> <?php echo $secondNameParts['name']; ?></span></a></li>
						<?php } ?>
					</ul>
				</li>
			<?php } else { ?>
				<?php $nameParts = Helpers_Menu::getNameParts($name); ?>
				<li  class="item<?php echo $i?>"><a href="/<?php echo Extasy_Url::url_to_route($route); ?>"><?php echo $nameParts['icon']; ?><span class="hidden-sm text"> <?php echo $nameParts['name']; ?></span></a></li>
			<?php } ?>
		<?php $i++; } ?>
	</ul>
</div>
<a href="javascript:void(0);" id="main-menu-min" class="full visible-md visible-lg"><i class="fa fa-angle-double-left"></i></a>