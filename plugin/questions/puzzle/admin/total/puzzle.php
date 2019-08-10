<?php
/**
* Joomlaquiz Deluxe Component for Joomla 3
* @package Joomlaquiz Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

function getTotalScorePuzzle($qid){

	$total_score = 0;
	$database = JFactory::getDBO();
	$query = "SELECT c_id FROM #__quiz_t_question WHERE c_type = '11' AND c_quiz_id = ".$qid." AND published = 1";
	$database->SetQuery( $query );
	$qch_ids_type_11 = $database->loadColumn();
	
	if(!empty($qch_ids_type_11)){
		foreach($qch_ids_type_11 as $c_question_id){
			$query = "SELECT c_pieces FROM #__quiz_t_puzzle WHERE c_question_id = '".$c_question_id."'";
			$database->SetQuery( $query );
			$c_pieces = $database->loadResult();

			if($c_pieces){
				$query = "SELECT c_point FROM #__quiz_t_question WHERE c_id = '".$c_question_id."'";
				$database->SetQuery( $query );
				$c_point = $database->loadResult();
				
				$total_score += ($c_pieces * $c_pieces * $c_point);
			}
		}
	}
	
	return $total_score;
}
