<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

function removeQuestionsTruefalse($cids)
{
	$database = JFactory::getDBO();
	$query = "DELETE FROM `#__quiz_t_choice` WHERE `c_question_id` IN ($cids)";
	$database->setQuery( $query );
    $database->execute();

	return true;
}