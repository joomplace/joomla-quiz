<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

function getTotalScoreMresponse($qid){

	$total_score = 0;
	$database = JFactory::getDBO();
	
	$query = "SELECT c_id FROM #__quiz_t_question WHERE c_type = 2 AND c_quiz_id = '".$qid."' AND published = 1";
	$database->SetQuery( $query );
	$qch_ids_type_2 = $database->loadColumn();

	if(count($qch_ids_type_2)) {
		foreach($qch_ids_type_2 as $key => $c_quetion_id){
			$query = "SELECT b.a_point FROM #__quiz_t_question as a, #__quiz_t_choice as b WHERE b.a_point >0 AND a.c_id = '".$c_quetion_id."' AND b.c_question_id = a.c_id AND a.published = 1";
			$database->SetQuery( $query );
			$a_points = $database->loadColumn();
			
			if (count($a_points)){
				$total_score += array_sum($a_points);
			}
		}

	}
	
	return $total_score;
}

?>
    
		 
		 