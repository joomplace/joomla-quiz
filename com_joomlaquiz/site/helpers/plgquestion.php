<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

/**
 * Joomlaquiz Deluxe component helper.
 */
abstract class plgJoomlaquizQuestion
{
	var $_type		= 'joomlaquiz';
	
	function __construct() {
		return true;
	}
	
	abstract public function onCreateQuestion(&$data);
	abstract public function onScoreByCategory(&$data);
		
}