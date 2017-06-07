<?php
/**
 * Joomlaquiz Deluxe Component for Joomla 3
 * @package Joomlaquiz Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

function getTotalScoreChoice($qid){

	$total_score = 0;
	$database = JFactory::getDBO();
	$query = "SELECT SUM(c.a_point) FROM #__quiz_t_choice as c, #__quiz_t_question as q WHERE c.c_question_id = q.c_id AND q.c_quiz_id = ".$qid." AND q.c_type = 1 AND c.c_right = 1 AND q.published = 1";
	$database->SetQuery( $query );
	$total_score += $database->LoadResult();

	$query = "SELECT c_id FROM #__quiz_t_question WHERE c_type = 1 AND c_quiz_id = '".$qid."' AND published = 1";
	$database->SetQuery( $query );
	$qch_ids_type_1 = $database->loadColumn();

	if(count($qch_ids_type_1)) {
		foreach($qch_ids_type_1 as $key => $c_quetion_id){
			$query = "SELECT b.c_right FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE a.c_id = '".$c_quetion_id."' AND b.c_question_id = a.c_id AND a.published = 1";
			$database->SetQuery( $query );
			$c_choices = $database->loadColumn();
			
			if (!in_array(1, $c_choices)){
				$query = "SELECT MAX(a_point) FROM #__quiz_t_choice WHERE c_question_id = '".$c_quetion_id."' AND c_right = 0";
				$database->SetQuery( $query );
				$total_score += $database->LoadResult();
			}
		}

	}
	
	return $total_score;
}
