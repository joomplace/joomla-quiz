<?php
/**
* JoomlaQuiz module for Joomla
* @version $Id: helper.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage helper.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die;

class modTopxusersHelper
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
		
		if ($v_content_count == 0){
			$v_content_count = 5;
		}

		$query	= $database->getQuery(true);
		$query	->select(array('qrsq.c_total_score','qrsq.user_name','qrsq.user_surname','qrsq.c_student_id as id'))
				->from('#__quiz_r_student_quiz AS qrsq')
				->leftjoin('#__quiz_t_quiz AS qtq ON qtq.c_id = qrsq.c_quiz_id')
				->where("qrsq.c_passed = 1");
		if ($quiz_id){
			$quiz_ids = explode( ',', $quiz_id );
			if(count($quiz_ids)){
				$query->where("( qtq.c_id=" . implode( " OR qtq.c_id=", $quiz_ids ) . " )");
			}
		}
		$query->order('qrsq.c_total_score DESC');
		$query->setLimit(0,$v_content_count);
		$database->setQuery($query);
		$result = array_map(function($result){
			if($result->id){
				$result->name		= JFactory::getUser($result->id)->name;
				$result->username	= JFactory::getUser($result->id)->username;
			}else{
				$result->name		= $result->user_name.' '.$result->user_surname;
				$result->username	= $result->user_name.' '.$result->user_surname;
			}
			return $result;
		},$database->loadObjectList());
		if (count($result) == 0){
			$result = array();
		}
		return $result;
	}
}
