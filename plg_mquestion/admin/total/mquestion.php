<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
function getTotalScoreMquestion($qid){

	$total_score = 0;
	$database = JFactory::getDBO();
	$query = "SELECT c_id FROM #__quiz_t_question WHERE c_type = 10 AND c_quiz_id = ".$qid." AND published = 1";
	$database->SetQuery( $query );
	$qch_ids_type_10 = $database->loadColumn();

	if(count($qch_ids_type_10)) {
		$query = "SELECT SUM(a_point) FROM #__quiz_t_choice WHERE c_question_id IN (".implode(',', $qch_ids_type_10).")";
		$database->SetQuery( $query );
		$total_score += $database->LoadResult();
	}
	
	return $total_score;
}

?>
    
		 
		 