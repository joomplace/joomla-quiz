<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

function getTotalScoreBlank($qid){

	$total_score = 0;
	$database = JFactory::getDBO();
	$query = "SELECT c_id FROM #__quiz_t_question WHERE c_type = 6 AND c_quiz_id = ".$qid." AND published = 1";
	$database->SetQuery( $query );
	$qch_ids_type_6 = $database->loadColumn();

	if(count($qch_ids_type_6)) {
		$query = "SELECT SUM(points) FROM #__quiz_t_blank WHERE c_question_id IN (".implode(',', $qch_ids_type_6).")";
		$database->SetQuery( $query );
		$total_score += $database->LoadResult();
	}
	
	return $total_score;
}
