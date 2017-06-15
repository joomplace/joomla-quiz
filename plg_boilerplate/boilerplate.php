<?php
/**
* JoomlaQuiz Boilerplate Plugin for Joomla
* @version $Id: boilerplate.php 2011-03-03 17:30:15
* @package JoomlaQuiz
* @subpackage boilerplate.php
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgJoomlaquizBoilerplate extends plgJoomlaquizQuestion
{
	var $name		= 'Boilerplate';
	var $_name		= 'boilerplate';
	
	public function onCreateQuestion(&$data) {
		
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<form onsubmit=\'javascript: return false;\' name=\'quest_form'.$data['q_data']->c_id.'\'>' . "\n";
		$data['ret_str'] .= "\n" . "\t" . '</form>]]></quest_data_user>' . "\n";
		
		return $data;
	}
	
	public function onSaveQuestion(&$data){
		
		$database = JFactory::getDBO();
		
		$query = "SELECT a.c_attempts FROM #__quiz_t_question as a WHERE a.c_id = '".$data['quest_id']."' AND a.published = 1";
		$database->SetQuery( $query );
		$ddd = $database->LoadObjectList();
		
		$c_quest_score = 0;
		$data['c_all_attempts'] = 1;
		$data['is_avail'] = 1;
		$data['is_correct'] = 1;
		$answer = trim(urldecode($data['answer']));
		if (count($ddd)) {
			if ($ddd[0]->c_attempts) {
				$data['c_all_attempts'] = $ddd[0]->c_attempts; }
		}
		$data['c_quest_cur_attempt'] = 0;
		$query = "SELECT c_id, c_attempts FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' and c_question_id = '".$data['quest_id']."'";
		$database->SetQuery( $query );
		$c_tmp = $database->LoadObjectList();
		
		if (count($c_tmp)) {
			$data['c_quest_cur_attempt'] = $c_tmp[0]->c_attempts;
			if ($data['c_quest_cur_attempt'] >= $data['c_all_attempts']) {
				$data['is_avail'] = 0;
				$data['is_no_attempts'] = 1;
			}
			if ($data['is_avail']) {
				$query = "DELETE FROM #__quiz_r_student_question WHERE c_stu_quiz_id = '".$data['stu_quiz_id']."' and c_question_id = '".$data['quest_id']."'";
				$database->SetQuery( $query );
				$database->execute();
			}
		}
		if ($data['is_avail']) {
			if ($data['c_quest_cur_attempt'] && $data['c_penalty']) {
				if (((100-$data['c_penalty']*$data['c_quest_cur_attempt'])/100) < 0)
					$c_quest_score = 0;								
				else 
					$c_quest_score = $c_quest_score * ((100-$data['c_penalty']*$data['c_quest_cur_attempt'])/100) ;
			}

			$query = "INSERT INTO #__quiz_r_student_question (c_stu_quiz_id, c_question_id, c_score, c_attempts, is_correct)"
			. "\n VALUES('".$data['stu_quiz_id']."', '".$data['quest_id']."', '".$c_quest_score."', '".($data['c_quest_cur_attempt'] + 1)."', '".$data['is_correct']."')";
			$database->SetQuery($query);
			$database->execute();
		}
		
		$data['score'] = $c_quest_score;
		
		return true;
	}
		
	public function onFeedbackQuestion(&$data){
		
		$database = JFactory::getDBO();
		$data['qoption'] = "\t" . '<form  onsubmit=\'javascript: return false;\' name="quest_form"></form>' . "\n";
		return $data['qoption'];
	}
	
	public function onNextPreviewQuestion(&$data){
		
		$data['is_correct'] = 1;
		return $data;
	}
	
	public function onReviewQuestion(&$data){
		
		$data['ret_str'] .= "\t" . '<quest_data_user><![CDATA[<form onsubmit=\'javascript: return false;\' name=\'quest_form\'>' . "\n";
		$data['ret_str'] .= "\n" . "\t" . '</form>]]></quest_data_user>' . "\n";		
		return $data;		
	}
	
	//Administration part
		
	public function onAdminIsFeedback(&$data){
		return false;
	}
	
	public function onAdminIsPoints(&$data){
		return false;
	}
	
	public function onAdminIsPenalty(&$data){
		return false;
	}
	
	public function onAdminIsReportName(){
		return false;
	}
		
	public function onGetAdminReportsHTML(&$data){
		return;
	}
	
	public function onScoreByCategory(&$data){
		return true;
	}
}