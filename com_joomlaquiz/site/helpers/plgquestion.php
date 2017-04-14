<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

JPluginHelper::importPlugin('joomlaquiz');

/**
 * Joomlaquiz Deluxe component helper.
 */
abstract class plgJoomlaquizQuestion extends JPlugin
{
	var $_type		= 'joomlaquiz';

	abstract public function onScoreByCategory(&$data);
		
}