<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Image_Modifier_Resize extends Extasy_Image_Modifier_Abstract
{
	public function modify(Image $image)
	{
		return $image->resize(
			$this->option('width', 100),
			$this->option('height', 100)
		);
	}
}