<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

function removeQuestionsBlank($cids){
	
	$database = JFactory::getDBO();
	$query = "DELETE FROM #__quiz_t_faketext WHERE c_quest_id  IN ( $cids )";
	$database->setQuery( $query );
	$database->execute();
	
	$query = "SELECT c_id FROM #__quiz_t_blank WHERE c_question_id IN ( $cids )";
	$database->SetQuery( $query );
	$blank_cid = $database->loadColumn();
	if (is_array( $blank_cid ) && (!empty($blank_cid))) {
		$blank_cids = implode( ',', $blank_cid );

		$query = "DELETE FROM #__quiz_t_text"
		. "\n WHERE c_blank_id IN ( $blank_cids )"
		;
		$database->setQuery( $query );
		$database->execute();

        $query = "DELETE FROM `#__quiz_t_blank`"
            . "\n WHERE `c_id` IN ( $blank_cids )"
        ;
        $database->setQuery( $query );
        $database->execute();
	}
	
	return true;
}