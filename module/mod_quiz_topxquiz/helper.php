<?php
/**
* JoomlaQuiz module for Joomla
* @version $Id: helper.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage helper.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die;

class modTopxquizHelper
{
	/*
	 * @since  1.5
	 */
	public static function getResult(&$params)
	{
		$database = JFactory::getDBO();
		
		$result = array();
		$v_content_count 	= intval( $params->get( 'quiz_count', 10 ) );
		$quiz_id		 	= trim( $params->get( 'quizid' ) );
		
		if ($v_content_count == 0) {
			$v_content_count = 5;
		}
		
		$query = "SELECT qtq.c_title, qtq.c_id as qid, qrsq.c_total_score 
		FROM #__quiz_t_quiz qtq, #__quiz_r_student_quiz qrsq
		WHERE qtq.c_id = qrsq.c_quiz_id and qrsq.c_passed = '1' ";
       
		if ($quiz_id) {
			$quiz_ids = explode( ',', $quiz_id );
			if(!empty($quiz_ids)){
				$query .= "\n AND ( qtq.c_id=" . implode( " OR qtq.c_id=", $quiz_ids ) . " )";
			}
		}
		$query .= "ORDER BY qrsq.c_total_score DESC, qtq.c_title ASC LIMIT 0,".$v_content_count;
		$database->SetQuery($query);
		$result = $database->LoadObjectList();
		if (empty($result)) {
			$result = array(); 
		}
		
		return $result;
	}
}
