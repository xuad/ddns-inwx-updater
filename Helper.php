<?php

/**
 * DDNS-INWX-Updater
 *
 * Copyright (c) Patrick Mosch
 *
 * @link http://xuad.net
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Class Helper
 *
 * Some helper methods
 * @copyright Patrick Mosch 2012
 * @author    Patrick Mosch
 */
class Helper
{

	/**
	 * Escape string
	 * @param string $str
	 * @return string
	 */
	static function htmlencode($str)
	{
		$str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
		return $str;
	}

}

?>
