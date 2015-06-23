<?php defined('SYSPATH') or die('No direct script access.');
?>
                <div class="accordion" id="leftMenu">
					<? if ($parent=='' || $parent!=-1 || $url!='') { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle"  href="<?php echo ($url ? $url : '#')?>">
                                  <i class="icon-home"></i> <?php echo $name?>
                            </a>
                        </div>
                    </div>
					<? } ?>
				</div>