<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Morphy
{
	static public function forms($word)
	{
		$opts = array(
			'storage' => PHPMORPHY_STORAGE_FILE,
			'with_gramtab' => false,
			'predict_by_suffix' => true, 
			'predict_by_db' => true
		);

		$dir = MODPATH . 'phpMorphy/dicts';

		$dict_bundle = new phpMorphy_FilesBundle($dir, 'rus');
		
		$morphy = new phpMorphy($dict_bundle, $opts);
		
		$base_form = $morphy->getPseudoRoot(mb_strtoupper($word));
		if (reset($base_form)) {
			return mb_strtolower(reset($base_form));
		}
		
		return $word;
	}
}