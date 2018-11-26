<?php
/**
* Joomlaquiz Component for Joomla 3
* @package Joomlaquiz
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

require_once( JPATH_ROOT .'/components/com_joomlaquiz/libraries/apps.php' );

/**
 * Statistics Model.
 *
 */
class JoomlaquizModelStatistics extends JModelList
{	
	public function getStatistics(){
		
		$appsLib = JqAppPlugins::getInstance();
		$plugins = $appsLib->loadApplications();
		
		$database = JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		$my = JFactory::getUser();
	
		$params = JFactory::getApplication()->getParams();
		$menu_quiz_id = $params->get('quiz_id', 0);
		
		$quiz_id =   $menu_quiz_id;
		if(!$quiz_id){
			$quiz_id = JFactory::getApplication()->input->get('quiz_id');
		}
		
		if ($quiz_id < 1) {
			$query = "SELECT sq.c_quiz_id FROM #__quiz_r_student_quiz AS sq, #__quiz_t_quiz AS q WHERE sq.c_quiz_id = q.c_id AND q.published = 1 ORDER BY sq.c_id DESC";
			$database->setQuery( $query );
			$quiz_id = (int)$database->loadResult();
		}
		
		$header = JText::_('COM_STATISTICS_TITLE');
		
		if (!$menu_quiz_id) {
			$query = "SELECT c_id AS value, c_title AS text FROM #__quiz_t_quiz WHERE c_id > 0 AND published = 1 ORDER BY c_title";
			$database->setQuery( $query );
			$quizzes = $database->loadObjectList();
			
			if (empty($quizzes)){
				$quizzes[0] = new stdClass;
				$quizzes[0]->available = 'no stats';
				return array($header, $quizzes);
			}
			
			$javascript = 'onchange="document.adminForm.submit();"';
			$quizzes = JHTML::_('select.genericlist', $quizzes, 'quiz_id', 'class="text_area" size="1" style="max-width: 300px;" '.$javascript, 'value', 'text', $quiz_id ); 
		} else{
			$query = "SELECT c_title FROM #__quiz_t_quiz WHERE c_id = '{$menu_quiz_id}' AND c_id > 0 AND published = 1 ";
			$database->setQuery( $query );
			$quiz_title = $database->loadResult();
			
			if (!$quiz_title){
				$quizzes[0] = new stdClass;
				$quizzes[0]->available = 'no stats';
				return array($header, $quizzes);
			}
			
			$header =  sprintf(JText::_('COM_QUIZ_STATISTICS_TITLE'), $quiz_title);
			$quizzes = '';
		}
		
		$summary = array();
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_quiz WHERE c_quiz_id = '$quiz_id' AND c_finished = 1";
		$database->setQuery( $query );
		$summary['total_finished'] = $database->loadResult();
		
		$query = "SELECT COUNT(*) FROM #__quiz_r_student_quiz WHERE c_quiz_id = '$quiz_id' AND c_finished = 1 AND c_passed = 1";
		$database->setQuery( $query );
		$summary['total_passed'] = $database->loadResult();
		
		$query = "SELECT MAX(c_total_score) FROM #__quiz_r_student_quiz WHERE c_quiz_id = '$quiz_id' AND c_finished = 1";
		$database->setQuery( $query );
		$summary['user_max_score'] = (int)$database->loadResult();
		$query = "SELECT MIN(c_total_score) FROM #__quiz_r_student_quiz WHERE c_quiz_id = '$quiz_id' AND c_finished = 1";
		$database->setQuery( $query );
		$summary['user_min_score'] = (int)$database->loadResult();
		$query = "SELECT AVG(c_total_score) FROM #__quiz_r_student_quiz WHERE c_quiz_id = '$quiz_id' AND c_finished = 1";
		$database->setQuery( $query );
		$summary['user_avg_score'] = (int)$database->loadResult();
		
		$query = "SELECT c_full_score FROM #__quiz_t_quiz WHERE c_id = '$quiz_id' ";
		$database->setQuery( $query );
		$summary['quiz_max_score'] = $database->loadResult();
		$query = "SELECT c_passing_score FROM #__quiz_t_quiz WHERE c_id = '$quiz_id' ";
		$database->setQuery( $query );
		$summary['quiz_pass_score'] = $database->loadResult();
		
		$query = "SELECT * FROM #__quiz_t_question WHERE c_quiz_id = '".$quiz_id."' AND published = 1 AND c_type NOT IN (9) ORDER BY ordering, c_id";
		$database->SetQuery( $query );
		$questions = $database->LoadObjectList();
		
		if (is_array($questions))
		foreach($questions as &$question){
			$type = JoomlaquizHelper::getQuestionType($question->c_type);
			$data = array();
			$data['quest_type'] = $type;
			$data['question'] = $question;
			
			$appsLib->triggerEvent( 'onGetStatistic' , $data );
			$question = $data['question'];
		}
		
		return array($header, $quizzes, $summary, $questions);
	}
}
